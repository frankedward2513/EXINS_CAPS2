<?php
// Database Connection
include $_SERVER['DOCUMENT_ROOT'] . '/EXINS/db_connection.php';

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Create Variable
    $id = $_POST['id'];
    $bale_name = $_POST['bale_name'];
    $b_quantity = $_POST['b_quantity'];
    $b_price = $_POST['b_price'];
    $price_per_piece = 0;

    if ($b_quantity > 0) {
        $price_per_piece = $b_price / $b_quantity;
    }

    // Use prepared statements to prevent sql injection
    $stmt = $conn->prepare("UPDATE bales SET 
    bale_name=?, 
    b_quantity=?, 
    b_price=?, 
    price_per_piece=? 
    WHERE ID=?");

    $stmt->bind_param(
        "siddi",
        $bale_name,
        $b_quantity,
        $b_price,
        $price_per_piece,
        $id
    );

    if ($stmt->execute()) {
        // Go back to page
        echo "Bale Changed Successfully! <br> 
        <a href='management_bale.php'>Go back</a>";
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}
?>