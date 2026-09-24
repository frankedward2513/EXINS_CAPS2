<?php
session_start();
// Database Connection (3 levels up from cart folder to root)
include '../../../db_connection.php';

// Ensure customer is logged in
if (!isset($_SESSION['customer_id'])) {
    echo "<script>window.location.href = '../../authentication/customer_auth.php?tab=login';</script>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_id = $_SESSION['customer_id'];
    $product_id = intval($_POST['pid']);
    $quantity_to_add = 1;

    // Check if this product already exists in THIS SPECIFIC customer's cart
    $stmt = $conn->prepare("SELECT id, quantity FROM cart WHERE customer_id = ? AND product_id = ?");
    $stmt->bind_param("ii", $customer_id, $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $cart_row = $result->fetch_assoc();
        $new_qty = $cart_row['quantity'] + $quantity_to_add;

        $update = $conn->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
        $update->bind_param("ii", $new_qty, $cart_row['id']);
        $update->execute();
        $update->close();
    } else {
        $insert = $conn->prepare("INSERT INTO cart (customer_id, product_id, quantity) VALUES (?, ?, ?)");
        $insert->bind_param("iii", $customer_id, $product_id, $quantity_to_add);
        $insert->execute();
        $insert->close();
    }
    $stmt->close();

    // Redirect back to shop page
    header("Location: ../shop_cust.php");
    exit();
}