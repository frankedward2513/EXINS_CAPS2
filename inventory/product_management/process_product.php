<?php
// Database Connection
include $_SERVER['DOCUMENT_ROOT'] . '/EXINS/db_connection.php';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Create Variables
    $product_name = $_POST['product_name'];
    $category_id = $_POST['category_id'];
    $bale_id = $_POST['bale_id'];
    $price = $_POST['price']; // Added price variable
    $quantity = $_POST['quantity'];
    $product_link = $_POST['product_link'];
    $description = $_POST['description'];

    $image_path = "";

    // Handle file upload for product image
    if (isset($_FILES['myfile']) && $_FILES['myfile']['error'] == 0) {
        $target_dir = "../../uploads/product/";
        $file_name = time() . "_" . basename($_FILES['myfile']['name']);
        $image_path = $target_dir . $file_name;
        move_uploaded_file($_FILES['myfile']['tmp_name'], $image_path);
    }

    // Use prepared statements to prevent SQL Injection
    $stmt = $conn->prepare("INSERT INTO products 
    (product_name, category_id, bale_id, price, quantity, product_link, image_path, description) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

    // Parameter types: s = string, d = double/float, i = integer
    // Order: product_name (s), category_id (i), bale_id (i), price (d), quantity (i), product_link (s), image_path (s), description (s)
    $stmt->bind_param(
        "siidisss",
        $product_name,
        $category_id,
        $bale_id,
        $price,
        $quantity,
        $product_link,
        $image_path,
        $description
    );

    if ($stmt->execute()) {
        // Back to page
        echo "Product added successfully! <br>
        <a href='management_product.php'>Go back</a>";
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}
$conn->close();
?>