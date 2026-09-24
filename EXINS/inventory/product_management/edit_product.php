<?php
// Database Connection
include $_SERVER['DOCUMENT_ROOT'] . '/EXINS/db_connection.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch the existing product data securely
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

if (!$product) {
    echo "Product not found.";
    exit();
}

// Fetch Categories for Options
$category_result = $conn->query("SELECT ID, CATEGORY_NAME FROM product_category");

// Fetch Bale for Option
$bale_result = $conn->query("SELECT id, bale_name FROM bales");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
    <!-- External Styles -->
    <link rel="stylesheet" href="../../styles/form.css">
</head>

<body>

    <!-- Form -->
    <form action="update_product.php" method="POST" enctype="multipart/form-data">

        <p>Edit Product Form</p>

        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
        <input type="hidden" name="old_image" value="<?php echo htmlspecialchars($product['image_path']); ?>">

        <!-- PRODUCT NAME -->
        <label>Product Name:</label><br>
        <input type="text" name="product_name" value="<?php echo htmlspecialchars($product['product_name']); ?>"
            required><br>

        <!-- CATEGORY -->
        <label>Category:</label><br>
        <select name="category_id" required>
            <option value="">- - Select Category - -</option>
            <?php
            if ($category_result && $category_result->num_rows > 0) {
                while ($cat = $category_result->fetch_assoc()) {
                    $selected = ($cat['ID'] == $product['category_id']) ? 'selected' : '';
                    echo "<option value='{$cat['ID']}' {$selected}>" . htmlspecialchars($cat['CATEGORY_NAME']) . "</option>";
                }
            }
            ?>
        </select><br>

        <!-- BALE -->
        <label>Bale:</label><br>
        <select name="bale_id" required>
            <option value="">- - Select Bale - -</option>
            <?php
            if ($bale_result && $bale_result->num_rows > 0) {
                while ($bale = $bale_result->fetch_assoc()) {
                    $selected = ($bale['id'] == $product['bale_id']) ? 'selected' : '';
                    echo "<option value='{$bale['id']}' {$selected}>" . htmlspecialchars($bale['bale_name']) . "</option>";
                }
            }
            ?>
        </select><br>

        <!-- PRICE (Added) -->
        <label for="price">Price (₱):</label><br>
        <input type="number" id="price" name="price" step="0.01" min="0"
            value="<?php echo htmlspecialchars($product['price']); ?>" required><br>

        <!-- QUANTITY -->
        <label>Quantity:</label><br>
        <input type="number" name="quantity" min="0" value="<?php echo htmlspecialchars($product['quantity']); ?>"
            required><br>

        <!-- PRODUCT LINK -->
        <label>Product Link:</label><br>
        <input type="url" name="product_link" value="<?php echo htmlspecialchars($product['product_link']); ?>"
            placeholder="https://example.com"><br>

        <!-- IMAGE -->
        <label for="myfile">Product Image:</label><br>
        <input type="file" id="myfile" name="myfile"><br>
        <?php if (!empty($product['image_path'])): ?>
            <p>Current Product Image:</p>
            <img src="<?php echo htmlspecialchars($product['image_path']); ?>" width="100" height="100"><br>
        <?php endif; ?>

        <!-- DESCRIPTION -->
        <label>Description:</label><br>
        <textarea name="description" rows="3"><?php echo htmlspecialchars($product['description']); ?></textarea><br>

        <input type="submit" value="UPDATE">

    </form>

</body>

</html>