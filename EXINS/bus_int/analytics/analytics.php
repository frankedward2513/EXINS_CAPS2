<?php
include 'analytics_data.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Meta -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Title -->
    <title>Analytics</title>
    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Styles -->
    <link rel="stylesheet" href="../../styles/analytics.css">
</head>

<body>
    <div class="container">
        <!-- Title -->
        <h1>Business Report</h1>

        <!-- CHART 1 -->
        <div class="card">
            <h2> 1. Remaining Inventory by Category </h2>
            <canvas id="inventoryChart"></canvas>
        </div>

        <!-- CHART 2 -->
        <div class="card">
            <h2> 2. Sales by Category </h2>
            <canvas id="salesChart"></canvas>
        </div>

        <!-- CHART 3 -->
        <div class="card">
            <h2> 3. Expenses by Category </h2>
            <canvas id="expenseChart"></canvas>
        </div>

        <!-- CHART 4 -->
        <div class="card">
            <h2> 4. Transaction Types </h2>
            <canvas id="transactionChart"></canvas>
        </div>

        <!-- CHART 5 -->
        <div class="card">
            <h2> 5. Top Customers </h2>
            <canvas id="customerChart"></canvas>
        </div>

        <!-- CHART 6 -->
        <div class="card">
            <h2> 6. Slow-Moving Products </h2>
            <canvas id="slowChart"></canvas>
        </div>

        <!-- CHART 7 -->
        <div class="card">
            <h2> 7. Monthly Sales and Expenses </h2>
            <canvas id="monthlyChart"></canvas>
        </div>

        <!-- CHART 8 -->
        <div class="card">
            <h2> 8. Inventory Value by Category </h2>
            <canvas id="valueChart"></canvas>
        </div>
    </div>

    <script>
        /* ==============================
           1. Remaining Inventory
        ============================== */
        const inventoryCategories =
            <?php echo json_encode($inventory_categories); ?>;
        const inventoryQuantity =
            <?php echo json_encode($inventory_quantity); ?>;

        /* ==============================
           2. Total Sales
        ============================== */
        const salesCategories =
            <?php echo json_encode($sales_categories); ?>;
        const salesAmount =
            <?php echo json_encode($sales_amount); ?>;

        /* ==============================
           3. Total Expenses
        ============================== */
        const expenseCategories =
            <?php echo json_encode($expense_categories); ?>;
        const expenseAmount =
            <?php echo json_encode($expense_amount); ?>;

        /* ==============================
           4. TRANSACTION TYPES
        ============================== */
        const transactionTypes = {
            Sold: <?php echo (int) $transaction_types['Sold']; ?>,
            Returned: <?php echo (int) $transaction_types['Returned']; ?>,
            Damaged: <?php echo (int) $transaction_types['Damaged']; ?>,
            Lost: <?php echo (int) $transaction_types['Lost']; ?>
        };

        /* ==============================
           5. CUSTOMERS
        ============================== */
        const customerNames =
            <?php echo json_encode($customer_names); ?>;
        const customerAmount =
            <?php echo json_encode($customer_amount); ?>;

        /* ==============================
           6. SLOW PRODUCTS
        ============================== */
        const slowProducts =
            <?php echo json_encode($slow_products); ?>;
        const slowDays =
            <?php echo json_encode($slow_days); ?>;

        /* ==============================
           7. MONTHLY DATA
        ============================== */
        const months =
            <?php echo json_encode($month_labels); ?>;
        const monthlySales =
            <?php echo json_encode($sales_monthly); ?>;
        const monthlyExpenses =
            <?php echo json_encode($expenses_monthly); ?>;

        /* ==============================
           8. INVENTORY VALUE
        ============================== */
        const inventoryValueCategories =
            <?php echo json_encode($inventory_value_categories); ?>;
        const inventoryValues =
            <?php echo json_encode($inventory_values); ?>

    </script>

    <script src="analytics.js"></script>