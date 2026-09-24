<?php
session_start();
include '../../../db_connection.php'; // Adjust path if placed elsewhere

// Ensure customer is logged in
if (!isset($_SESSION['customer_id'])) {
    header("Location: ../../authentication/customer_auth.php?tab=login");
    exit();
}

$customer_id = $_SESSION['customer_id'];

// Fetch orders for this customer
$orders_query = "SELECT * FROM orders WHERE customer_id = ? ORDER BY created_at DESC";
$orders_stmt = $conn->prepare($orders_query);
$orders_stmt->bind_param("i", $customer_id);
$orders_stmt->execute();
$orders_result = $orders_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - EXINS</title>
    <link rel="stylesheet" href="../../../styles/shop.css">
    <style>
        /* Reusing your shop style consistency */
        .order-container {
            max-width: 950px;
            margin: 30px auto;
            padding: 20px;
            font-family: Arial, sans-serif;
        }

        .order-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .badge {
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 0.75em;
            font-weight: bold;
            text-transform: uppercase;
        }

        .badge-pending {
            background: #fef08a;
            color: #854d0e;
        }

        .badge-completed {
            background: #dcfce7;
            color: #166534;
        }
    </style>
</head>

<body>
    <div class="order-container">

        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2 style="margin: 0; color: #1e293b;">My Orders</h2>
            <a href="../shop_cust.php" style="color: #2563eb; text-decoration: none; font-weight: bold;">&larr; Back to
                Shop</a>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div
                style="padding: 12px; background: #dcfce7; border: 1px solid #bbf7d0; color: #166534; border-radius: 6px; margin-bottom: 20px; font-size: 0.9em;">
                Order placed successfully! Thank you for your purchase.
            </div>
        <?php endif; ?>

        <?php if ($orders_result->num_rows === 0): ?>
            <p style="color: #64748b; text-align: center; padding: 40px 0;">You haven't placed any orders yet.</p>
        <?php else: ?>
            <?php while ($order = $orders_result->fetch_assoc()): ?>
                <div class="order-card">
                    <div
                        style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e2e8f0; padding-bottom: 12px; margin-bottom: 15px;">
                        <div>
                            <span style="font-weight: bold; color: #0f172a;">Order #<?php echo $order['id']; ?></span>
                            <span
                                style="font-size: 0.85em; color: #64748b; margin-left: 15px;"><?php echo $order['created_at']; ?></span>
                        </div>
                        <div>
                            <span class="badge badge-pending"><?php echo htmlspecialchars($order['status']); ?></span>
                        </div>
                    </div>

                    <!-- Order Info -->
                    <div
                        style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 15px; margin-bottom: 15px; font-size: 0.9em; color: #334155;">
                        <div><strong>Courier:</strong> <?php echo htmlspecialchars($order['courier']); ?></div>
                        <div><strong>Payment Method:</strong> <?php echo htmlspecialchars($order['payment_method']); ?></div>
                        <div><strong>Total Amount:</strong> $<?php echo number_format($order['total_amount'], 2); ?></div>
                        <?php if ($order['payment_method'] === 'COD'): ?>
                            <div><strong>Downpayment Paid:</strong> $<?php echo number_format($order['downpayment'], 2); ?></div>
                            <div style="color: #dc2626;"><strong>Remaining Balance:</strong>
                                $<?php echo number_format($order['remaining_balance'], 2); ?></div>
                        <?php endif; ?>
                    </div>

                    <!-- Order Items Table -->
                    <h4 style="margin: 15px 0 8px 0; font-size: 0.95em; color: #1e293b;">Items Purchased:</h4>
                    <table style="width: 100%; border-collapse: collapse; font-size: 0.85em;">
                        <thead>
                            <tr style="border-bottom: 1px solid #e2e8f0; text-align: left; color: #64748b;">
                                <th style="padding: 6px;">Product Name</th>
                                <th style="padding: 6px;">Price</th>
                                <th style="padding: 6px;">Qty</th>
                                <th style="padding: 6px;">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $order_id = $order['id'];
                            $items_query = "SELECT oi.*, p.product_name, p.image_path FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?";
                            $items_stmt = $conn->prepare($items_query);
                            $items_stmt->bind_param("i", $order_id);
                            $items_stmt->execute();
                            $items_result = $items_stmt->get_result();

                            while ($item = $items_result->fetch_assoc()):
                                ?>
                                <tr style="border-bottom: 1px solid #f1f5f9;">
                                    <td style="padding: 8px; display: flex; align-items: center; gap: 10px;">
                                        <img src="/EXINS/uploads/product/<?php echo htmlspecialchars($item['image_path']); ?>"
                                            width="35" height="35" style="object-fit: cover; border-radius: 4px;">
                                        <span><?php echo htmlspecialchars($item['product_name']); ?></span>
                                    </td>
                                    <td style="padding: 8px;">$<?php echo number_format($item['price'], 2); ?></td>
                                    <td style="padding: 8px;"><?php echo $item['quantity']; ?></td>
                                    <td style="padding: 8px;">$<?php echo number_format($item['price'] * $item['quantity'], 2); ?>
                                    </td>
                                </tr>
                            <?php endwhile;
                            $items_stmt->close(); ?>
                        </tbody>
                    </table>

                    <?php if (!empty($order['receipt_image'])): ?>
                        <div style="margin-top: 15px; font-size: 0.85em;">
                            <strong>Uploaded Receipt:</strong><br>
                            <a href="/EXINS/uploads/receipts/<?php echo htmlspecialchars($order['receipt_image']); ?>"
                                target="_blank" style="color: #2563eb; text-decoration: underline;">View Receipt Image</a>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        <?php endif; ?>
    </div>
</body>

</html>