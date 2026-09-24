<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EXINS - Customer Portal</title>
    <link rel="stylesheet" href="styles/system.css">
    <link rel="stylesheet" href="styles/pos.css">
    <script src="scripts/scripts.js" type="text/javascript"></script>
</head>

<body>
    <nav>
        <!-- LEFT NAVIGATION -->
        <ul class="nav-left">
            <h1>EXINS</h1>
            <li><a href="customer/shop_customer/shop_cust.php" target="contentFrame">Shop</a></li>

            <?php if (isset($_SESSION['customer_id'])): ?>
                <li class="nav-divider-small"></li>
                <!-- Cart link added to the navigation bar -->
                <li><a href="customer/shop_customer/cart/cart.php" target="contentFrame">Cart</a></li>
                <li><a href="customer/shop_customer/cart/my_orders.php" target="contentFrame">My Orders</a></li>
            <?php endif; ?>
        </ul>

        <!-- RIGHT NAVIGATION -->
        <ul class="nav-right">
            <?php if (isset($_SESSION['customer_id'])): ?>
                <li><span style="color: #333; font-weight: bold; font-size: 14px;">Hi,
                        <?= htmlspecialchars($_SESSION['customer_name'] ?? 'Customer'); ?></span></li>
                <li class="nav-divider-small"></li>
                <li><a href="customer/authentication/customer_logout.php" target="_self">Log Out</a></li>
            <?php else: ?>
                <li><a href="customer/authentication/customer_auth.php?tab=login" target="contentFrame">Log In</a></li>
                <li class="nav-divider-small"></li>
                <li><a href="customer/authentication/customer_auth.php?tab=signup" target="contentFrame">Sign Up</a></li>
            <?php endif; ?>
        </ul>
    </nav>

    <!-- Main content frame -->
    <iframe name="contentFrame" src="customer/shop_customer/shop_cust.php" frameborder="0"
        style="margin-top: 75px; display: block; width: 100%; height: calc(100vh - 75px);">
    </iframe>
</body>

</html>