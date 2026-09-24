<?php
// Database Connection
include $_SERVER['DOCUMENT_ROOT'] . '/EXINS/db_connection.php';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Create Variable
    $id = $_POST['id'];
    $customer_name = $_POST['customer_name'];
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];
    $amount = $_POST['amount'];
    $transaction_type = $_POST['transaction_type'];
    $remarks = $_POST['remarks'];
    $transaction_date = $_POST['transaction_date'];

    // Use prepared statement to prevent sql injection
    $stmt = $conn->prepare("UPDATE transactions SET
        customer_name = ?,
        product_id = ?,
        quantity = ?,
        amount = ?,
        transaction_type = ?,
        remarks = ?,
        transaction_date = ?
        WHERE id = ?
    ");

    $stmt->bind_param(
        "siidsssi",
        $customer_name,
        $product_id,
        $quantity,
        $amount,
        $transaction_type,
        $remarks,
        $transaction_date,
        $id
    );

    if ($stmt->execute()) {
        echo "Updated Transaction SUccessfullly! <br>
        <a href='transactions.php'>Go Back</a>";
        exit();
    } else {
        $stmt->error;
    }
    $stmt->close();
}
$conn->close();
?>