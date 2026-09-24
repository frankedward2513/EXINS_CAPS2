<?php
// Database Connection
include $_SERVER['DOCUMENT_ROOT'] . '/EXINS/db_connection.php';

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Create Variables
    $product_id = intval($_POST['product_id']);
    $product_name = trim($_POST['product_name']);
    $category_id = intval($_POST['category_id']);
    $bale_id = intval($_POST['bale_id']);
    $price = floatval($_POST['price']);
    $quantity = intval($_POST['quantity']);
    $product_link = trim($_POST['product_link']);
    $description = trim($_POST['description']);

    $image_path = $_POST['old_image'];

    // Check if a new file was uploaded
    if (isset($_FILES['myfile']) && $_FILES['myfile']['error'] == 0) {
        $target_dir = "../../uploads/product/";
        $file_name = time() . "_" . basename($_FILES['myfile']['name']);
        $image_path = $target_dir . $file_name;
        move_uploaded_file($_FILES['myfile']['tmp_name'], $image_path);
    }

    // Use prepared statement to prevent sql injection
    $stmt = $conn->prepare("UPDATE products SET 
    product_name = ?, 
    category_id = ?, 
    bale_id = ?, 
    price = ?, 
    quantity = ?, 
    product_link = ?, 
    image_path = ?, 
    description = ? 
    WHERE id = ?");

    // Corrected to 9 types matching the 9 variables below:
    // s (product_name), i (category_id), i (bale_id), d (price), i (quantity), s (product_link), s (image_path), s (description), i (product_id)
    $stmt->bind_param(
        "siidisssi",
        $product_name,
        $category_id,
        $bale_id,
        $price,
        $quantity,
        $product_link,
        $image_path,
        $description,
        $product_id
    );

    if ($stmt->execute()) {
        // Back to page
        echo "Product Changed Successfully! <br>
        <a href='management_product.php'>Go back</a>";
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}
$conn->close();
?>