<?php
session_start();
include '../db_connection.php'; // Adjust path based on where owner_orders.php is saved

// Handle Status Update Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['new_status'])) {
    $order_id = intval($_POST['order_id']);
    $new_status = trim($_POST['new_status']);

    $allowed_statuses = ['Pending', 'Preparing', 'Drop to Courier', 'Completed', 'Cancelled'];

    if (in_array($new_status, $allowed_statuses)) {
        $update_stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $update_stmt->bind_param("si", $new_status, $order_id);
        $update_stmt->execute();
        $update_stmt->close();

        // Retain the current filter when redirecting back using relative dynamic target
        $current_filter = isset($_POST['current_filter']) ? urlencode($_POST['current_filter']) : 'All';
        $redirect_page = basename($_SERVER['PHP_SELF']);
        header("Location: " . $redirect_page . "?status=" . $current_filter . "&updated=1");
        exit();
    }
}

// Get selected filter from URL (Default is 'All')
$selected_filter = isset($_GET['status']) ? trim($_GET['status']) : 'All';

// Build query based on filter selection
if ($selected_filter !== 'All' && in_array($selected_filter, ['Pending', 'Preparing', 'Drop to Courier', 'Completed', 'Cancelled'])) {
    $stmt = $conn->prepare("SELECT * FROM orders WHERE status = ? ORDER BY created_at DESC");
    $stmt->bind_param("s", $selected_filter);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
} else {
    $query = "SELECT * FROM orders ORDER BY created_at DESC";
    $result = $conn->query($query);
}

if (!$result) {
    die("<div style='padding: 20px; font-family: Arial; color: red;'><strong>Database Query Error:</strong> " . $conn->error . "</div>");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Orders Management - EXINS</title>
    <link rel="stylesheet" href="../styles/system.css">
    <style>
        html,
        body {
            min-height: 100%;
            margin: 0;
            padding: 0;
            background: transparent;
            overflow-y: auto;
            /* Allow the content inside iframe to scroll */
        }

        .admin-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            font-family: Arial, sans-serif;
            box-sizing: border-box;
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

        .badge-preparing {
            background: #e0f2fe;
            color: #0369a1;
        }

        .badge-drop-to-courier {
            background: #fed7aa;
            color: #9a3412;
        }

        .badge-completed {
            background: #dcfce7;
            color: #166534;
        }

        .badge-cancelled {
            background: #fee2e2;
            color: #991b1b;
        }

        .status-form {
            display: flex;
            gap: 10px;
            align-items: center;
            margin-top: 15px;
            background: #f8fafc;
            padding: 10px;
            border-radius: 6px;
        }

        select {
            padding: 6px 10px;
            border-radius: 4px;
            border: 1px solid #cbd5e1;
        }

        button {
            padding: 6px 14px;
            background: #2563eb;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
        }

        button:hover {
            background: #1d4ed8;
        }

        /* Filter Button Bar Styles */
        .filter-bar {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-bottom: 25px;
            background: #fff;
            padding: 12px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
        }

        .filter-btn {
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.85em;
            font-weight: bold;
            text-decoration: none;
            border: 1px solid #cbd5e1;
            background: #f8fafc;
            color: #475569;
            transition: all 0.2s;
        }

        .filter-btn:hover {
            background: #e2e8f0;
        }

        .filter-btn.active {
            background: #2563eb;
            color: #fff;
            border-color: #2563eb;
        }
    </style>
</head>

<body>
    <div class="admin-container">

        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2 style="margin: 0; color: #1e293b;">Customer Orders Management</h2>
            <a href="shop.php">Back to Shop</a>
        </div>

        <!-- Filter Buttons Bar -->
        <div class="filter-bar">
            <span
                style="align-self: center; font-size: 0.85em; font-weight: bold; color: #64748b; margin-right: 5px;">Filter
                By:</span>
            <a href="owner_orders.php?status=All"
                class="filter-btn <?php echo ($selected_filter === 'All') ? 'active' : ''; ?>">All Orders</a>
            <a href="owner_orders.php?status=Pending"
                class="filter-btn <?php echo ($selected_filter === 'Pending') ? 'active' : ''; ?>">Pending</a>
            <a href="owner_orders.php?status=Preparing"
                class="filter-btn <?php echo ($selected_filter === 'Preparing') ? 'active' : ''; ?>">Preparing</a>
            <a href="owner_orders.php?status=Drop to Courier"
                class="filter-btn <?php echo ($selected_filter === 'Drop to Courier') ? 'active' : ''; ?>">Drop to
                Courier</a>
            <a href="owner_orders.php?status=Completed"
                class="filter-btn <?php echo ($selected_filter === 'Completed') ? 'active' : ''; ?>">Completed</a>
            <a href="owner_orders.php?status=Cancelled"
                class="filter-btn <?php echo ($selected_filter === 'Cancelled') ? 'active' : ''; ?>">Cancelled</a>
        </div>

        <?php if (isset($_GET['updated'])): ?>
            <div
                style="padding: 12px; background: #dcfce7; border: 1px solid #bbf7d0; color: #166534; border-radius: 6px; margin-bottom: 20px; font-size: 0.9em;">
                Order status successfully updated!
            </div>
        <?php endif; ?>

        <?php if ($result->num_rows === 0): ?>
            <p style="color: #64748b; text-align: center; padding: 40px 0;">No customer orders found for status:
                <strong><?php echo htmlspecialchars($selected_filter); ?></strong>
            </p>
        <?php else: ?>
            <?php while ($order = $result->fetch_assoc()): ?>
                <div class="order-card">
                    <!-- Top Order Meta -->
                    <div
                        style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e2e8f0; padding-bottom: 12px; margin-bottom: 15px;">
                        <div>
                            <span style="font-weight: bold; color: #0f172a; font-size: 1.1em;">Order
                                #<?php echo $order['id']; ?></span>
                            <span style="font-size: 0.85em; color: #64748b; margin-left: 15px;">Placed on:
                                <?php echo $order['created_at']; ?></span>
                        </div>
                        <div>
                            <?php
                            $status_class = 'badge-pending';
                            $s = strtolower($order['status'] ?? 'pending');
                            if ($s === 'preparing')
                                $status_class = 'badge-preparing';
                            elseif ($s === 'drop to courier')
                                $status_class = 'badge-drop-to-courier';
                            elseif ($s === 'completed')
                                $status_class = 'badge-completed';
                            elseif ($s === 'cancelled')
                                $status_class = 'badge-cancelled';
                            ?>
                            <span
                                class="badge <?php echo $status_class; ?>"><?php echo htmlspecialchars($order['status'] ?? 'Pending'); ?></span>
                        </div>
                    </div>

                    <!-- Transaction & Customer Info -->
                    <div
                        style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 15px; font-size: 0.9em; color: #334155;">
                        <div><strong>Customer ID:</strong> #<?php echo htmlspecialchars($order['customer_id'] ?? 'N/A'); ?>
                        </div>
                        <div><strong>Courier:</strong> <?php echo htmlspecialchars($order['courier'] ?? 'N/A'); ?></div>
                        <div><strong>Payment Method:</strong> <?php echo htmlspecialchars($order['payment_method'] ?? 'N/A'); ?>
                        </div>
                        <div><strong>Total Amount:</strong> $<?php echo number_format($order['total_amount'] ?? 0, 2); ?></div>

                        <?php if (isset($order['payment_method']) && $order['payment_method'] === 'COD'): ?>
                            <div><strong>Downpayment:</strong> $<?php echo number_format($order['downpayment'] ?? 0, 2); ?></div>
                            <div style="color: #dc2626;"><strong>Remaining Balance:</strong>
                                $<?php echo number_format($order['remaining_balance'] ?? 0, 2); ?></div>
                        <?php endif; ?>
                    </div>

                    <!-- Items Purchased Table -->
                    <h4 style="margin: 15px 0 8px 0; font-size: 0.95em; color: #1e293b;">Ordered Items:</h4>
                    <table style="width: 100%; border-collapse: collapse; font-size: 0.85em; margin-bottom: 15px;">
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
                            $items_stmt = $conn->prepare("SELECT oi.*, p.product_name, p.image_path FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?");
                            $items_stmt->bind_param("i", $order_id);
                            $items_stmt->execute();
                            $items_res = $items_stmt->get_result();

                            while ($item = $items_res->fetch_assoc()):
                                ?>
                                <tr style="border-bottom: 1px solid #f1f5f9;">
                                    <td style="padding: 8px; display: flex; align-items: center; gap: 10px;">
                                        <img src="../uploads/product/<?php echo htmlspecialchars($item['image_path']); ?>"
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
                        <div style="margin-bottom: 15px; font-size: 0.85em;">
                            <strong>Customer Payment Receipt:</strong><br>
                            <a href="../uploads/receipts/<?php echo htmlspecialchars($order['receipt_image']); ?>" target="_blank"
                                style="color: #2563eb; text-decoration: underline;">View Uploaded Receipt Image</a>
                        </div>
                    <?php endif; ?>

                    <!-- Update Status Form -->
                    <form action="" method="POST" class="status-form">
                        <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                        <input type="hidden" name="current_filter" value="<?php echo htmlspecialchars($selected_filter); ?>">

                        <label for="new_status_<?php echo $order['id']; ?>"
                            style="font-size: 0.85em; font-weight: bold; color: #475569;">Update Status:</label>
                        <select name="new_status" id="new_status_<?php echo $order['id']; ?>">
                            <option value="Pending" <?php echo (($order['status'] ?? '') === 'Pending') ? 'selected' : ''; ?>>
                                Pending</option>
                            <option value="Preparing" <?php echo (($order['status'] ?? '') === 'Preparing') ? 'selected' : ''; ?>>
                                Preparing</option>
                            <option value="Drop to Courier" <?php echo (($order['status'] ?? '') === 'Drop to Courier') ? 'selected' : ''; ?>>Drop to Courier</option>
                            <option value="Completed" <?php echo (($order['status'] ?? '') === 'Completed') ? 'selected' : ''; ?>>
                                Completed</option>
                            <option value="Cancelled" <?php echo (($order['status'] ?? '') === 'Cancelled') ? 'selected' : ''; ?>>
                                Cancelled</option>
                        </select>
                        <button type="submit">Update Status</button>
                    </form>

                </div>
            <?php endwhile; ?>
        <?php endif; ?>
    </div>
</body>

</html>