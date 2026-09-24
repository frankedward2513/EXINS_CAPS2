<?php
// Database Connection
include $_SERVER['DOCUMENT_ROOT'] . '/EXINS/db_connection.php';

$id = $_GET['id'];

// Fetch the Existing Data
$stmt = $conn->prepare("SELECT CATEGORY_NAME FROM product_category WHERE ID = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$product_category = $result->fetch_assoc();
?>

<!-- External Style -->
<link rel="stylesheet" href="../../styles/form.css">

<!-- Form -->
<form action="update_product_category.php" method="POST">

    <!-- Form Title -->
    <p>Edit Product Category Form</p>

    <input type="hidden" name="id" value="<?php echo $id; ?>">

    <!-- Category Name -->
    <label>Category Name: </label>
    <input type="text" name="new_name" value="<?php echo $product_category['CATEGORY_NAME']; ?>" required>

    <!-- Submit -->
    <input type="submit" value="UPDATE">

</form>