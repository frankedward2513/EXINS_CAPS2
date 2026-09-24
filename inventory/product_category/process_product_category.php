<?php
// Database Connection
include $_SERVER['DOCUMENT_ROOT'] . '/EXINS/db_connection.php';

// check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Create Variables
    $product_category = $_POST['product_category'];

    // Use prepared statements to prevent SQL Injection | s-string
    $stmt = $conn->prepare("INSERT INTO product_category 
    (CATEGORY_NAME) 
    VALUES (?)");

    $stmt->bind_param(
        "s",
        $product_category
    );

    if ($stmt->execute()) {
        // Back to page
        echo "Product Category Added Successfully! <br>
        <a href='management_product_category.php'>Go back</a>";
        exit();
    } else {
        echo $stmt->error;
    }
    $stmt->close();
}
$conn->close();
?>