<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include $_SERVER['DOCUMENT_ROOT'] . '/EXINS/db_connection.php';

// STEP 1: Handle final form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'confirm_purchase') {

    $pid = intval($_POST['pid']);
    $purchase_qty = intval($_POST['qty']);
    $customer_name = trim($_POST['customer_name']);
    $total_amount = floatval($_POST['totalAmount']);
    $product_price = floatval($_POST['pricePerUnit']); // Captured from the form field
    $transaction_type = $_POST['transaction_type'] ?? 'Sold';
    $transaction_date = $_POST['transaction_date'] ?? date('Y-m-d H:i:s');
    $remarks = trim($_POST['remarks'] ?? '');

    // Get current product stock and name
    $stmt = $conn->prepare("SELECT product_name, quantity FROM products WHERE id = ?");
    $stmt->bind_param("i", $pid);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($product = $result->fetch_assoc()) {

        $current_stock = intval($product['quantity']);
        $new_stock = $current_stock;

        // Determine new stock based on transaction type
        if ($transaction_type == "Sold" || $transaction_type == "Damaged" || $transaction_type == "Lost") {
            if ($current_stock < $purchase_qty) {
                echo "<h2 style='color:red;'>Not Enough Stock Available! Current stock: {$current_stock}</h2>";
                echo "<a href='shop.php'>Go Back</a>";
                exit;
            }
            $new_stock = $current_stock - $purchase_qty;
        } elseif ($transaction_type == "Returned") {
            $new_stock = $current_stock + $purchase_qty;
        }

        // 1. Update stock in the 'products' table
        $update_stmt = $conn->prepare("UPDATE products SET quantity = ? WHERE id = ?");
        $update_stmt->bind_param("ii", $new_stock, $pid);
        $update_stmt->execute();
        $update_stmt->close();

        // 2. Insert into the legacy 'transactions' table (for logs)
        $trans_stmt = $conn->prepare("INSERT INTO transactions
        (customer_name, product_id, quantity, amount, transaction_type, remarks, transaction_date)
        VALUES (?, ?, ?, ?, ?, ?, ?)");

        $trans_stmt->bind_param(
            "siidsss",
            $customer_name,
            $pid,
            $purchase_qty,
            $total_amount,
            $transaction_type,
            $remarks,
            $transaction_date
        );
        $trans_stmt->execute();
        $trans_stmt->close();

        // 3. Insert into 'new_orders' table 
        // Note: 'Pending' status means inventory won't drop yet until you click 'Preparing' in owner_orders.php!
        $order_status = 'Pending';
        $order_stmt = $conn->prepare("INSERT INTO new_orders (customer_name, total_amount, status, created_at) VALUES (?, ?, ?, ?)");
        $order_stmt->bind_param("sdss", $customer_name, $total_amount, $order_status, $transaction_date);

        if ($order_stmt->execute()) {
            $order_id = $conn->insert_id; // Get the generated order ID
            $order_stmt->close();

            // 4. Insert into 'order_items' table so owner orders management can display it
            $item_stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
            $item_stmt->bind_param("iiid", $order_id, $pid, $purchase_qty, $product_price);

            if ($item_stmt->execute()) {
                $item_stmt->close();

                // Success Output Screen
                echo "
                <div style='
                    background:#151413;
                    color:#EAE0D5;
                    padding:40px;
                    border-radius:20px;
                    text-align:center;
                    font-family:sans-serif;
                    max-width:600px;
                    margin:40px auto;
                '>

                <h2 style='color:#C6AC8E;'>Transaction & Order Successful!</h2>
                <p><strong>Customer:</strong> " . htmlspecialchars($customer_name) . "</p>
                <p><strong>Product:</strong> " . htmlspecialchars($product['product_name']) . "</p>
                <p><strong>Transaction Type:</strong> " . htmlspecialchars($transaction_type) . "</p>
                <p><strong>Order ID:</strong> #{$order_id}</p>
                <p><strong>Quantity Bought:</strong> {$purchase_qty}</p>
                <p><strong>Total Amount:</strong> ₱" . number_format($total_amount, 2) . "</p>
                <p><strong>Remaining Inventory Stock:</strong> {$new_stock}</p>
                
                <br>

                <a href='shop.php'
                style='
                    background:#C6AC8E;
                    color:#0A0908;
                    padding:12px 25px;
                    border-radius:30px;
                    text-decoration:none;
                    font-weight:bold;
                '>
                Return to Shop
                </a>

                </div>";
            } else {
                echo "<h2 style='color:red;'>Database Order Items Error: " . $conn->error . "</h2>";
            }

        } else {
            echo "<h2 style='color:red;'>Database Orders Error: " . $conn->error . "</h2>";
        }
    } else {
        echo "<h2 style='color:red;'>Product not found!</h2>";
    }

    $stmt->close();
    $conn->close();
    exit;
}
?>

<?php

?>