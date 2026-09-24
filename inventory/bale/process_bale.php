<?php
// Database Connection
include $_SERVER['DOCUMENT_ROOT'] . '/EXINS/db_connection.php';

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Create Variables
    $bale_name = $_POST['bale_name'];
    $b_quantity = $_POST['b_quantity'];
    $b_price = $_POST['b_price'];

    $price_per_piece = 0;
    if ($b_quantity > 0) {
        $price_per_piece = $b_price / $b_quantity;
    }

    // Use prepared statements to prevent SQL Injection
    $stmt = $conn->prepare("INSERT INTO bales 
    (bale_name, b_quantity, b_price, price_per_piece) 
    VALUES(?,?,?,?)");

    $stmt->bind_param(
        "sidd",
        $bale_name,
        $b_quantity,
        $b_price,
        $price_per_piece
    );

    if ($stmt->execute()) {
        // Go back to page
        echo "Bale Added Successfully! <br> 
        <a href='management_bale.php'>Go back</a>";
        exit();
    } else {
        echo $stmt->error;
    }
    $stmt->close();
}
$conn->close();
?>