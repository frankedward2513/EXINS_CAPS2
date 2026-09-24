<?php
// Databse Connection
include $_SERVER['DOCUMENT_ROOT'] . '/EXINS/db_connection.php';

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Create Variable
    $id = $_POST['id'];
    $new_name = $_POST['new_name'];

    // Use prevent statements to prevent sql injection
    $stmt = $conn->prepare("UPDATE product_category SET 
    CATEGORY_NAME = ? 
    WHERE ID = ?");

    $stmt->bind_param(
        "si",
        $new_name,
        $id
    );

    if ($stmt->execute()) {
        // Back to page
        echo "Product Category Changed Successfully! <br>
        <a href='management_product_category.php'>Go back</a>";
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}
?>