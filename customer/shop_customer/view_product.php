<?php
session_start();
// Database Connection (2 levels up from shop_customer to root)
include '../../db_connection.php';

// Get and validate product ID from URL query string
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Product not found.";
    exit();
}

$p = $result->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($p['product_name']); ?> - Details</title>
    <link rel="stylesheet" href="../../styles/shop.css">
</head>

<body>
    <div class="product-details-container" style="padding: 20px; max-width: 800px; margin: auto;">
        <a href="shop_cust.php">&larr; Back to Shop</a>

        <div class="product-details-wrapper" style="margin-top: 20px; display: flex; gap: 30px;">
            <!-- Product Image -->
            <div>
                <img src="/EXINS/uploads/product/<?php echo htmlspecialchars($p['image_path']); ?>" alt="Product Image"
                    width="300" height="300" style="object-fit: cover; border-radius: 8px;">
            </div>

            <!-- Full Product Details Info -->
            <div style="flex-grow: 1;">
                <h2><?php echo htmlspecialchars($p['product_name']); ?></h2>
                <p><strong>Price:</strong> $<?php echo number_format($p['price'], 2); ?></p>
                <p><strong>Available Quantity:</strong> <?php echo $p['quantity']; ?></p>

                <?php if (!empty($p['product_link'])): ?>
                    <p><strong>Product Link:</strong> <a href="<?php echo htmlspecialchars($p['product_link']); ?>"
                            target="_blank" rel="noopener noreferrer">Open External Link</a></p>
                <?php endif; ?>

                <p><strong>Description:</strong><br><?php echo nl2br(htmlspecialchars($p['description'])); ?></p>

                <!-- Add to Cart Form -->
                <form action="cart/add_to_cart.php" method="POST" style="margin-top: 20px;">
                    <input type="hidden" name="pid" value="<?php echo $p['id']; ?>">
                    <input type="submit" value="Add to Cart" class="add-to-cart-btn"
                        style="padding: 10px 20px; background: #2563eb; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;">
                </form>
            </div>
        </div>
    </div>
</body>

</html>