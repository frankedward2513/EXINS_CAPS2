<?php
// Database Connection
include $_SERVER['DOCUMENT_ROOT'] . '/EXINS/db_connection.php';

// Fetch Categories for Options
$categories = $conn->query("SELECT * FROM product_category");

// Fetch products where quantity is greater than 0
$products = $conn->query("SELECT * FROM products WHERE quantity > 0");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Meta -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Title Page -->
    <title>Shop</title>

    <!-- External Styles -->
    <link rel="stylesheet" href="../styles/shop.css">
</head>

<body>

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2>Shop</h2>
        <!-- Link / Button to View Orders -->
        <a href="owner_orders.php" class="view-orders-btn"
            style="background: #2563eb; color: white; padding: 10px 16px; text-decoration: none; border-radius: 6px; font-weight: bold; font-family: sans-serif;">
            View My Orders
        </a>
    </div>

    <!-- Filter Buttons -->
    <button class="category_button" onclick="filterSelection('all')">Show All</button>
    <?php while ($cat = $categories->fetch_assoc()): ?>
        <button class="category_button"
            onclick="filterSelection('<?php echo $cat['ID']; ?>')"><?php echo $cat['CATEGORY_NAME']; ?>
        </button>
    <?php endwhile; ?>

    <!-- Products Grid -->
    <div id="product-container">
        <?php while ($p = $products->fetch_assoc()): ?>
            <div class="product-card" data-category="<?php echo $p['category_id']; ?>">

                <!-- Image -->
                <img src="/EXINS/uploads/product/<?php echo $p['image_path']; ?>" alt="Product Image" width="100"
                    height="100">

                <!-- Product Name -->
                <strong><?php echo $p['product_name']; ?></strong>

                <!-- Quantity -->
                <span class="stock-label">Available: <?php echo $p['quantity']; ?></span>

                <!-- Product Link -->
                <a href="<?php echo $p['product_link']; ?>">visit</a>

                <!-- Trigger Button -->
                <button onclick="toggleDescription(this)">View Description</button>

                <!-- Description Popup (Hidden by default) -->
                <p class="product-desc"
                    style="display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: #fff; padding: 20px; border: 1px solid #ccc; box-shadow: 0 4px 10px rgba(0,0,0,0.3); z-index: 1000; border-radius: 8px; max-width: 400px; width: 80%;">
                    <?php echo $p['description']; ?>
                    <br><br>
                    <button onclick="toggleDescription(this.parentElement.previousElementSibling)">Close</button>
                </p>

                <!-- Checkout -->
                <form action="checkout.php" method="POST">
                    <input type="hidden" name="pid" value="<?php echo $p['id']; ?>">
                    <input type="hidden" name="pname" value="<?php echo $p['product_name']; ?>">
                    <!-- Submit -->
                    <input type="submit" value="BUY">
                </form>

            </div>
        <?php endwhile; ?>
    </div>

    <script>
        function filterSelection(c) {
            let x = document.getElementsByClassName("product-card");
            for (let i = 0; i < x.length; i++) {
                if (c == "all" || x[i].getAttribute("data-category") == c) {
                    x[i].style.display = "inline-block";
                } else {
                    x[i].style.display = "none";
                }
            }
        }

        function toggleDescription(button) {
            // Finds the paragraph right after the button
            const desc = button.nextElementSibling;

            if (desc.style.display === "none") {
                desc.style.display = "block";
            } else {
                desc.style.display = "none";
            }
        }
    </script>

</body>

</html>