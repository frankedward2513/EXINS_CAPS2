<?php
session_start();
include '../../../db_connection.php';

// Ensure customer is logged in
if (!isset($_SESSION['customer_id'])) {
    header("Location: ../../authentication/customer_auth.php?tab=login");
    exit();
}

// Check if any items were selected
if (!isset($_POST['selected_items']) || empty($_POST['selected_items'])) {
    header("Location: cart.php");
    exit();
}

$selected_cart_ids = $_POST['selected_items'];
$customer_id = $_SESSION['customer_id'];

// Fetch Customer Information
$cust_stmt = $conn->prepare("SELECT fullname, phone, address FROM customers WHERE id = ?");
$cust_stmt->bind_param("i", $customer_id);
$cust_stmt->execute();
$customer = $cust_stmt->get_result()->fetch_assoc();
$cust_stmt->close();

// Prepare placeholders for SQL IN clause
$placeholders = implode(',', array_fill(0, count($selected_cart_ids), '?'));
$types = str_repeat('i', count($selected_cart_ids));

// Fetch selected items from cart joined with products
$query = "SELECT c.id as cart_id, c.quantity as cart_qty, p.id as product_id, p.product_name, p.price, p.image_path, p.quantity as stock_qty 
          FROM cart c 
          JOIN products p ON c.product_id = p.id 
          WHERE c.customer_id = ? AND c.id IN ($placeholders)";

$stmt = $conn->prepare($query);
$bind_params = array_merge([$customer_id], $selected_cart_ids);
$tmp = [];
foreach ($bind_params as $key => $value) {
    $tmp[$key] = &$bind_params[$key];
}
call_user_func_array([$stmt, 'bind_param'], array_merge(['i' . $types], $tmp));

$stmt->execute();
$result = $stmt->get_result();

$checkout_items = [];
$grand_total = 0;

while ($row = $result->fetch_assoc()) {
    $checkout_items[] = $row;
    $grand_total += ($row['price'] * $row['cart_qty']);
}
$stmt->close();

// Down payment rule for COD
$cod_downpayment = 100;
$cod_remaining = max(0, $grand_total - $cod_downpayment);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout Summary - EXINS</title>
    <link rel="stylesheet" href="../../../styles/shop.css">
    <style>
        .section-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .payment-details {
            display: none;
            margin-top: 15px;
            padding: 15px;
            background: #fff;
            border: 1px dashed #cbd5e1;
            border-radius: 6px;
        }
    </style>
</head>

<body class="bg-slate-900 text-slate-100 min-h-screen p-6">
    <div
        style="max-width: 850px; margin: auto; background: #1e293b; padding: 30px; border-radius: 12px; border: 1px solid #334155;">

        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2 style="color: #f87171; margin: 0;">Checkout Summary</h2>
            <a href="cart.php" style="color: #94a3b8; text-decoration: none; font-size: 0.9em;">&larr; Back to Cart</a>
        </div>

        <!-- Customer Details Section -->
        <div class="section-box" style="background: #0f172a; border-color: #334155;">
            <h3 style="margin-top: 0; color: #38bdf8; font-size: 1.1em;">Customer Information</h3>
            <p style="margin: 5px 0;"><strong>Name:</strong>
                <?php echo htmlspecialchars($customer['fullname'] ?? 'N/A'); ?></p>
            <p style="margin: 5px 0;"><strong>Contact Number:</strong>
                <?php echo htmlspecialchars($customer['phone'] ?? 'N/A'); ?></p>
            <p style="margin: 5px 0;"><strong>Address:</strong>
                <?php echo htmlspecialchars($customer['address'] ?? 'N/A'); ?></p>
        </div>

        <!-- Order List Table -->
        <div class="section-box" style="background: #0f172a; border-color: #334155;">
            <h3 style="margin-top: 0; color: #38bdf8; font-size: 1.1em;">Order Items</h3>
            <table style="width: 100%; border-collapse: collapse; margin-top: 10px;">
                <thead>
                    <tr style="border-bottom: 2px solid #334155; text-align: left; color: #94a3b8; font-size: 0.85em;">
                        <th style="padding: 8px;">Product</th>
                        <th style="padding: 8px;">Price</th>
                        <th style="padding: 8px;">Qty</th>
                        <th style="padding: 8px;">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($checkout_items as $item): ?>
                        <tr style="border-bottom: 1px solid #1e293b;">
                            <td style="padding: 10px; display: flex; align-items: center; gap: 12px;">
                                <img src="/EXINS/uploads/product/<?php echo htmlspecialchars($item['image_path']); ?>"
                                    width="45" height="45" style="object-fit: cover; border-radius: 4px;">
                                <span><?php echo htmlspecialchars($item['product_name']); ?></span>
                            </td>
                            <td style="padding: 10px;">$<?php echo number_format($item['price'], 2); ?></td>
                            <td style="padding: 10px;"><?php echo $item['cart_qty']; ?></td>
                            <td style="padding: 10px;">$<?php echo number_format($item['price'] * $item['cart_qty'], 2); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div style="text-align: right; margin-top: 15px;">
                <h3 style="color: #4ade80; margin: 0;">Total Amount: $<?php echo number_format($grand_total, 2); ?></h3>
            </div>
        </div>

        <!-- Final Checkout Form -->
        <form action="place_order.php" method="POST" enctype="multipart/form-data">
            <?php foreach ($selected_cart_ids as $cart_id): ?>
                <input type="hidden" name="selected_items[]" value="<?php echo $cart_id; ?>">
            <?php endforeach; ?>

            <!-- Courier Selection Section -->
            <div class="section-box" style="background: #0f172a; border-color: #334155;">
                <h3 style="margin-top: 0; color: #38bdf8; font-size: 1.1em;">Courier Partner</h3>
                <label style="display: block; font-size: 0.9em; color: #cbd5e1; margin-bottom: 8px;">Select
                    Courier:</label>
                <select name="courier" required
                    style="width: 100%; padding: 10px; background: #1e293b; border: 1px solid #475569; color: white; border-radius: 6px;">
                    <option value="">-- Choose Courier Partner --</option>
                    <option value="Lalamove">Lalamove</option>
                    <option value="LBC">LBC</option>
                    <option value="J&T">J&T Express</option>
                </select>
                <p style="font-size: 0.8em; color: #f59e0b; margin-top: 6px;">* Note: The shipping fee will be
                    shouldered by the customer upon delivery or dispatch. Thank you for your understanding!</p>
            </div>

            <!-- Payment Method Section -->
            <div class="section-box" style="background: #0f172a; border-color: #334155;">
                <h3 style="margin-top: 0; color: #38bdf8; font-size: 1.1em;">Payment Method</h3>

                <div style="display: flex; gap: 20px; margin-bottom: 15px;">
                    <label style="cursor: pointer;">
                        <input type="radio" name="payment_method" value="COD" onclick="togglePayment('cod')" required>
                        Cash on Delivery (COD)
                    </label>
                    <label style="cursor: pointer;">
                        <input type="radio" name="payment_method" value="Pay Now" onclick="togglePayment('paynow')"
                            required> Pay Now (GCash)
                    </label>
                </div>

                <!-- Scan Via GCash & QR Code Image -->
                <div id="gcash-qr-section"
                    style="text-align: center; margin-bottom: 15px; padding: 10px; background: #1e293b; border-radius: 6px; display: none;">
                    <p style="font-size: 0.9em; font-weight: bold; color: #38bdf8; margin-bottom: 8px;">Scan Via GCash
                    </p>
                    <img src="/EXINS/uploads/system/gcash_qr_placeholder.png" alt="GCash QR Code"
                        style="width: 160px; height: 160px; object-fit: cover; border-radius: 6px; border: 2px solid #475569;">
                    <p style="font-size: 0.75em; color: #94a3b8; margin-top: 4px;">Scan using your GCash app to transfer
                        payment.</p>
                </div>

                <!-- COD Note Details & Down Payment Receipt Upload -->
                <div id="cod-details" class="payment-details" style="color: #334155;">
                    <p style="margin: 0; font-weight: bold;">COD Payment Breakdown:</p>
                    <p style="margin: 4px 0;"><strong>Down Payment:</strong>
                        $<?php echo number_format($cod_downpayment, 2); ?></p>
                    <p style="margin: 4px 0; color: #dc2626;"><strong>Remaining to pay:</strong>
                        $<?php echo number_format($cod_remaining, 2); ?></p>

                    <label
                        style="display: block; margin-top: 10px; font-size: 0.85em; font-weight: bold; color: #1e293b;">Upload
                        Down Payment Receipt:</label>
                    <input type="file" name="receipt_image" accept="image/*" style="margin-top: 5px; width: 100%;">
                </div>

                <!-- Pay Now Upload Receipt Section -->
                <div id="paynow-details" class="payment-details" style="color: #334155;">
                    <p style="margin: 0; font-weight: bold;">Total Amount to be Paid: <span
                            style="color: #16a34a;">$<?php echo number_format($grand_total, 2); ?></span></p>
                    <label
                        style="display: block; margin-top: 10px; font-size: 0.85em; font-weight: bold; color: #1e293b;">Upload
                        Full Payment Receipt:</label>
                    <input type="file" name="receipt_image" accept="image/*" style="margin-top: 5px; width: 100%;">
                </div>
            </div>

            <!-- Submit Action -->
            <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 20px;">
                <a href="cart.php"
                    style="padding: 10px 20px; background: #475569; color: white; text-decoration: none; border-radius: 6px; font-weight: bold; font-size: 0.9em;">Back
                    to Shop / Cart</a>
                <button type="submit"
                    style="padding: 10px 25px; background: #16a34a; color: white; border: none; border-radius: 6px; font-weight: bold; cursor: pointer; font-size: 0.95em;">Confirm
                    Transaction</button>
            </div>
        </form>
    </div>

    <script>
        function togglePayment(method) {
            const codDetails = document.getElementById('cod-details');
            const paynowDetails = document.getElementById('paynow-details');
            const gcashQrSection = document.getElementById('gcash-qr-section');

            // Always show GCash QR code because both methods rely on GCash (COD uses it for downpayment)
            gcashQrSection.style.display = 'block';

            if (method === 'cod') {
                codDetails.style.display = 'block';
                paynowDetails.style.display = 'none';
            } else if (method === 'paynow') {
                codDetails.style.display = 'none';
                paynowDetails.style.display = 'block';
            }
        }
    </script>
</body>

</html>