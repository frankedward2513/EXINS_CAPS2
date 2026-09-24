<?php
session_start();
include '../../../db_connection.php';

// Ensure customer is logged in
if (!isset($_SESSION['customer_id'])) {
    header("Location: ../../authentication/customer_auth.php?tab=login");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['selected_items'])) {
    $customer_id = $_SESSION['customer_id'];
    $selected_cart_ids = $_POST['selected_items'];
    $courier = trim($_POST['courier']);
    $payment_method = trim($_POST['payment_method']);

    $receipt_filename = null;

    // Handle receipt upload if Pay Now is selected
    if ($payment_method === 'Pay Now' && isset($_FILES['receipt_image']) && $_FILES['receipt_image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../../../uploads/receipts/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        $file_ext = pathinfo($_FILES['receipt_image']['name'], PATHINFO_EXTENSION);
        $receipt_filename = 'receipt_' . time() . '_' . mt_rand(1000, 9999) . '.' . $file_ext;
        move_uploaded_file($_FILES['receipt_image']['tmp_name'], $upload_dir . $receipt_filename);
    }

    // Fetch cart items and calculate grand total
    $placeholders = implode(',', array_fill(0, count($selected_cart_ids), '?'));
    $types = str_repeat('i', count($selected_cart_ids));

    $query = "SELECT c.id as cart_id, c.quantity as cart_qty, p.id as product_id, p.price 
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

    $grand_total = 0;
    $cart_items = [];
    while ($row = $result->fetch_assoc()) {
        $grand_total += ($row['price'] * $row['cart_qty']);
        $cart_items[] = $row;
    }
    $stmt->close();

    // Calculate Downpayment & Remaining Balance
    $downpayment = ($payment_method === 'COD') ? 100.00 : $grand_total;
    $remaining_balance = ($payment_method === 'COD') ? max(0, $grand_total - 100.00) : 0.00;

    // Insert into orders table
    $order_query = "INSERT INTO orders (customer_id, courier, payment_method, total_amount, downpayment, remaining_balance, receipt_image, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'Pending')";
    $order_stmt = $conn->prepare($order_query);
    $order_stmt->bind_param("issddds", $customer_id, $courier, $payment_method, $grand_total, $downpayment, $remaining_balance, $receipt_filename);
    $order_stmt->execute();
    $order_id = $order_stmt->insert_id;
    $order_stmt->close();

    // Insert items into order_items, deduct product stock, and delete from cart
    foreach ($cart_items as $item) {
        // Insert order item
        $item_stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        $item_stmt->bind_param("iiid", $order_id, $item['product_id'], $item['cart_qty'], $item['price']);
        $item_stmt->execute();
        $item_stmt->close();

        // Deduct stock from products table
        $stock_stmt = $conn->prepare("UPDATE products SET quantity = quantity - ? WHERE id = ?");
        $stock_stmt->bind_param("ii", $item['cart_qty'], $item['product_id']);
        $stock_stmt->execute();
        $stock_stmt->close();

        // Remove from cart
        $del_cart = $conn->prepare("DELETE FROM cart WHERE id = ?");
        $del_cart->bind_param("i", $item['cart_id']);
        $del_cart->execute();
        $del_cart->close();
    }

    // Redirect to My Orders page
    header("Location: my_orders.php?success=1");
    exit();
} else {
    header("Location: cart.php");
    exit();
}
?>