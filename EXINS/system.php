<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JKsur+ Dashboard</title>
    <link rel="stylesheet" href="styles/system.css">
    <script src="scripts/scripts.js" type="text/javascript"></script>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }

        nav {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 75px;
            background: #fff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 20px;
            box-sizing: border-box;
            z-index: 1000;
        }

        .nav-left,
        .nav-center,
        .nav-right {
            display: flex;
            align-items: center;
            list-style: none;
            gap: 15px;
            margin: 0;
            padding: 0;
        }

        .nav-left h1 {
            color: #da291c;
            margin: 0;
            font-size: 24px;
        }

        .nav-left a,
        .nav-center a,
        .nav-right a {
            text-decoration: none;
            color: #333;
            font-weight: bold;
            font-size: 14px;
        }

        .nav-left a:hover,
        .nav-center a:hover,
        .nav-right a:hover {
            color: #da291c;
        }

        .nav-divider,
        .nav-divider-small {
            background: #ccc;
        }

        .nav-divider-small {
            width: 1px;
            height: 15px;
        }
    </style>
</head>

<body>
    <nav>
        <ul class="nav-left">
            <h1>JKsur+</h1>
            <?php if (isset($_SESSION['owner_id'])): ?>
                <li><a href="dashboard/dashboard.php" target="contentFrame">Dashboard</a></li>
                <li class="nav-divider-small"></li>
                <li><a href="Shop/shop.php" target="contentFrame">Shop</a></li>
            <?php endif; ?>
        </ul>

        <?php if (isset($_SESSION['owner_id'])): ?>
            <ul class="nav-center">
                <li class="nav-divider"></li>
                <li>Inventory Management
                    <ul class="submenu">
                        <li><a href="inventory/bale/management_bale.php" target="contentFrame">Bale</a></li>
                        <li><a href="inventory/product_category/management_product_category.php"
                                target="contentFrame">Product Category</a></li>
                        <li><a href="inventory/product_management/management_product.php" target="contentFrame">Product</a>
                        </li>
                        <li><a href="inventory/supplier/management_supplier.php" target="contentFrame">Supplier</a></li>
                        <li><a href="inventory/barcode/generate_barcode.php" target="contentFrame">Barcode</a></li>
                    </ul>
                </li>
                <li>Finance Management
                    <ul class="submenu">
                        <li><a href="finance/expense_categories/management_expense_category.php"
                                target="contentFrame">Expense Category</a></li>
                        <li><a href="finance/expense_management/management_expense.php" target="contentFrame">Expense</a>
                        </li>
                        <li><a href="finance/transactions/transactions.php" target="contentFrame">Transactions</a></li>
                    </ul>
                </li>
                <li>Business Intelligence
                    <ul class="submenu">
                        <li><a href="bus_int/forecasting/forecast.php" target="contentFrame">Forecast EWMA</a></li>
                        <li><a href="bus_int/analytics/analytics.php" target="contentFrame">Business Report</a></li>
                        <li><a href="ai/expense_analysis.php" target="contentFrame">Expense Insight</a></li>
                        <li><a href="ai/inventory_analysis.php" target="contentFrame">Inventory Insight</a></li>
                    </ul>
                </li>
                <li class="nav-divider"></li>
            </ul>
        <?php endif; ?>

        <ul class="nav-right">
            <?php if (isset($_SESSION['owner_id'])): ?>
                <li><span style="color: #333; font-weight: bold; font-size: 14px;">Hi, Owner</span></li>
                <li class="nav-divider-small"></li>
                <li><a href="Owner/owner_logout.php" target="_self">Log Out</a></li>
            <?php else: ?>
                <!-- Linked to owner_auth.php for Login -->
                <li><a href="Owner/owner_auth.php" target="contentFrame">Log In</a></li>
                <li class="nav-divider-small"></li>
                <!-- Linked to owner_auth.php for Sign Up -->
                <li><a href="Owner/owner_auth.php" target="contentFrame">Sign Up</a></li>
            <?php endif; ?>
        </ul>
    </nav>

    <!-- Main content frame styled to clear the 75px fixed navigation header -->
    <iframe name="contentFrame"
        src="<?php echo isset($_SESSION['owner_id']) ? 'dashboard/dashboard.php' : 'Owner/owner_auth.php'; ?>"
        frameborder="0" style="margin-top: 75px; display: block; width: 100%; height: calc(100vh - 75px);">
    </iframe>
</body>

</html>