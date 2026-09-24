<?php
// Database Connection (if not already included by the parent file)
if (!isset($conn)) {
    include $_SERVER['DOCUMENT_ROOT'] . '/EXINS/db_connection.php';
}

// Collect Filter Values safely from URL
$search_id = isset($_GET['id']) ? $conn->real_escape_string($_GET['id']) : '';
$search_product_name = isset($_GET['product_name']) ? $conn->real_escape_string($_GET['product_name']) : '';
$search_category = isset($_GET['category_id']) ? $conn->real_escape_string($_GET['category_id']) : '';

// Build Query with Filters and Joins (Added p.price)
$query = "SELECT p.id, p.product_name, pc.ID as category_id, pc.CATEGORY_NAME, b.bale_name, p.price, p.quantity, p.product_link, p.description, p.created_at, p.image_path
    FROM products p
    LEFT JOIN product_category pc ON p.category_id = pc.ID
    LEFT JOIN bales b ON p.bale_id = b.id
    WHERE 1=1 AND p.quantity > 0";

// Append filters if they are provided
if (!empty($search_id)) {
    $query .= " AND p.id = '$search_id'";
}
if (!empty($search_product_name)) {
    $query .= " AND p.product_name LIKE '%$search_product_name%'";
}
if (!empty($search_category)) {
    $query .= " AND p.category_id = '$search_category'";
}

$query .= " ORDER BY p.created_at DESC";
$result = $conn->query($query);
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
    <link rel="stylesheet" href="../../styles/database.css">
</head>

<body>
    <h3>Product Database</h3>
    <table border="1">

        <!-- Table Headers -->
        <tr>
            <th>ID</th>
            <th>Product Name</th>
            <th>Category</th>
            <th>Bale</th>
            <th>Price</th>
            <th>Quantity</th>
            <th>Link</th>
            <th>Image</th>
            <th>Description</th>
            <th>Action</th>
        </tr>

        <?php
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $imgPath = $row['image_path'];
                $productDisplay = !empty($imgPath) ? "<img src='$imgPath' width='100' height='100' alt='product'>" : "No Image";
                $formattedPrice = number_format($row['price'], 2);

                echo "<tr> 
                    <td>{$row['id']}</td>
                    <td>{$row['product_name']}</td>
                    <td>{$row['CATEGORY_NAME']}</td>
                    <td>{$row['bale_name']}</td>
                    <td>₱{$formattedPrice}</td>
                    <td>{$row['quantity']}</td>
                    <td><a href='{$row['product_link']}' target='_blank'>{$row['product_link']}</a></td>
                    <td>{$productDisplay}</td>
                    <td>{$row['description']}</td>

                    <td>
                        <a href='edit_product.php?id={$row['id']}'>Edit</a> |
                        <a href='delete_product.php?id={$row['id']}' onclick='return confirm(\"Are you sure you want to delete this product?\")'>Delete</a>
                    </td>

                </tr>";
            }
        } else {
            echo "<tr>
                <td colspan='10'>No products found.</td>
            </tr>";
        }
        ?>
    </table>
</body>

</html>