<?php
session_start();
// Database Connection (2 levels up from shop_customer to root)
include '../../db_connection.php';

$categories = $conn->query("SELECT * FROM product_category");
$products = $conn->query("SELECT * FROM products WHERE quantity > 0");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Shop</title>
    <link rel="stylesheet" href="../../styles/shop.css">
</head>

<body>
    <h2>Shop Catalog</h2>

    <!-- Filter Buttons -->
    <div class="filter-container">
        <button class="category_button active" onclick="filterSelection('all', this)">Show All</button>
        <?php while ($cat = $categories->fetch_assoc()): ?>
            <button class="category_button" onclick="filterSelection('<?php echo $cat['ID']; ?>', this)">
                <?php echo htmlspecialchars($cat['CATEGORY_NAME']); ?>
            </button>
        <?php endwhile; ?>
    </div>

    <!-- Products Grid -->
    <div id="product-container">
        <?php while ($p = $products->fetch_assoc()): ?>
            <div class="product-card" data-category="<?php echo $p['category_id']; ?>">

                <div class="product-image-wrapper">
                    <img src="/EXINS/uploads/product/<?php echo htmlspecialchars($p['image_path']); ?>" alt="Product Image">
                </div>

                <strong class="product-name"><?php echo htmlspecialchars($p['product_name']); ?></strong>
                <div class="product-price">
                    $<?php echo number_format($p['price'], 2); ?>
                </div>
                <span class="stock-label">
                    Available: <?php echo $p['quantity']; ?>
                </span>

                <!-- View More Button linking to the full details page -->
                <form action="view_product.php" method="GET">
                    <input type="hidden" name="id" value="<?php echo $p['id']; ?>">
                    <input type="submit" value="View More" class="view-more-btn">
                </form>

            </div>
        <?php endwhile; ?>
    </div>

    <script>
        function filterSelection(c, btn) {
            let x = document.getElementsByClassName("product-card");
            let buttons = document.getElementsByClassName("category_button");
            for (let i = 0; i < buttons.length; i++) buttons[i].classList.remove("active");
            if (btn) btn.classList.add("active");

            for (let i = 0; i < x.length; i++) {
                if (c == "all" || x[i].getAttribute("data-category") == c) {
                    x[i].style.display = "inline-block";
                } else {
                    x[i].style.display = "none";
                }
            }
        }
    </script>
</body>

</html>