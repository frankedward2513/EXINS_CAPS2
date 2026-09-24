<!-- Database Connection -->
<?php
include $_SERVER['DOCUMENT_ROOT'] . '/EXINS/db_connection.php';

// Fetch Categories for Options
$category_result = $conn->query("SELECT ID, CATEGORY_NAME FROM product_category");

// Fetch Bale for Options
$bale_result = $conn->query("SELECT id, bale_name FROM bales");

// Collect Filter Values
$search_id = isset($_GET['id']) ? $conn->real_escape_string($_GET['id']) : '';
$search_product_name = isset($_GET['product_name']) ? $conn->real_escape_string($_GET['product_name']) : '';
$search_category = isset($_GET['category_id']) ? $conn->real_escape_string($_GET['category_id']) : '';

// Build Query
$sql = "SELECT * FROM products WHERE 1=1";

// Products ID
if (!empty($search_id)) {
    $sql .= " AND id='$search_id'";
}

// Products Name
if (!empty($search_product_name)) {
    $sql .= " AND product_name LIKE '%$search_product_name%'";
}

// Category ID
if (!empty($search_category)) {
    $sql .= " AND category_id='$search_category'";
}

$sql .= " ORDER BY created_at DESC";
$product_result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Meta -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Title Page -->
    <title>Product Management</title>

    <!-- External Styles -->
    <link rel="stylesheet" href="../../styles/form.css">
</head>

<body>
    <form action="process_product.php" enctype="multipart/form-data" method="POST">

        <!-- Form Title -->
        <h1>Product Management Form</h1>

        <!-- Product Name -->
        <label for="product_name">Product Name:</label><br>
        <input type="text" id="product_name" name="product_name"><br>

        <!-- Category -->
        <label for="category_id">Product Category:</label><br>
        <select name="category_id" id="category_id" required>
            <option value="">- - Select Category - -</option>
            <?php
            // Re-query or reset pointer for category options
            $cat_form_result = $conn->query("SELECT ID, CATEGORY_NAME FROM product_category");
            if ($cat_form_result && $cat_form_result->num_rows > 0) {
                while ($cat = $cat_form_result->fetch_assoc()) {
                    echo "<option value='{$cat['ID']}'>{$cat['CATEGORY_NAME']}</option>";
                }
            }
            ?>
        </select><br>

        <!-- Bale -->
        <label for="bale_id">Bale Category:</label><br>
        <select name="bale_id" id="bale_id" required>
            <option value="">- - Select Category - -</option>
            <?php
            if ($bale_result && $bale_result->num_rows > 0) {
                while ($bale = $bale_result->fetch_assoc()) {
                    echo "<option value='{$bale['id']}'>{$bale['bale_name']}</option>";
                }
            }
            ?>
        </select><br>

        <!-- Price -->
        <label for="price">Price (₱):</label><br>
        <input type="number" id="price" name="price" step="0.01" min="0" placeholder="0.00" required><br>

        <!-- Quantity -->
        <label for="quantity">Quantity:</label><br>
        <input type="number" id="quantity" name="quantity" min="1" required><br>

        <!-- Product Image -->
        <label for="myfile">Image (Optional):</label><br>
        <input type="file" id="myfile" name="myfile"><br>

        <!-- Link -->
        <label for="product_link">Link: </label><br>
        <input type="url" id="product_link" name="product_link" placeholder="https://example.com"><br>

        <label for="description">Description:</label><br>
        <textarea id="description" name="description" rows="3" placeholder="Enter a short description..."
            maxlength="500" style="width: 100%; max-width: 100%; box-sizing: border-box;"></textarea>

        <!-- Submit / Cancel -->
        <div id="submitorcancel">
            <input type="submit" value="SUBMIT" id="submit">
            <input type="reset" value="CANCEL" id="reset">
        </div>

    </form>

    <hr>

    <!-- Filter Form -->
    <form method="GET" class="filter-form">
        <h3>Filter Products</h3>

        <!-- Product ID -->
        <input type="number" name="id" class="filter-input" placeholder="Product ID"
            value="<?php echo htmlspecialchars($search_id); ?>">

        <!-- Product Name -->
        <input type="text" name="product_name" class="filter-input" placeholder="Product Name"
            value="<?php echo htmlspecialchars($search_product_name); ?>">

        <!-- Category Dropdown -->
        <select name="category_id" class="filter-input">
            <option value="">- - All Categories - -</option>
            <?php
            if ($category_result && $category_result->num_rows > 0) {
                while ($cat = $category_result->fetch_assoc()) {
                    $selected = ($search_category == $cat['ID']) ? 'selected' : '';
                    echo "<option value='{$cat['ID']}' $selected>{$cat['CATEGORY_NAME']}</option>";
                }
            }
            ?>
        </select>

        <!-- Filter Button -->
        <button type="submit" class="filter-btn">Filter</button>
        <a href="management_product.php">Clear</a>
    </form>

    <!-- Database Table Display -->
    <?php
    include 'table_product.php';
    ?>

</body>

</html>