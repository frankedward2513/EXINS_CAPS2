<?php
include 'controller.php';
include 'analytics_data.php'; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Dashboard - Homepage</title>
    <!-- Styles -->
    <link rel="stylesheet" href="../styles/dashboard.css">
    <link rel="stylesheet" href="../styles/analytics.css">
    <!-- Chart.js Library -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>

    <div class="container">

        <div class="header-wrapper">
            <h1>Dashboard Overview</h1>

            <!-- Date Range Filter Form -->
            <form method="GET" action="" class="filter-form">
                <div class="form-group">
                    <label>Start Date</label>
                    <input type="date" name="start_date" value="<?php echo htmlspecialchars($start_date); ?>">
                </div>
                <div class="form-group">
                    <label>End Date</label>
                    <input type="date" name="end_date" value="<?php echo htmlspecialchars($end_date); ?>">
                </div>
                <div class="filter-actions">
                    <button type="submit" class="btn-primary">Filter</button>
                    <a href="dashboard.php" class="btn-secondary">Reset</a>
                </div>
            </form>
        </div>

        <!-- KPI Cards Grid -->
        <div class="kpi-grid">

            <div class="kpi-card border-blue">
                <p class="kpi-label">Total Customers</p>
                <p class="kpi-value"><?php echo number_format($total_customers); ?></p>
            </div>

            <div class="kpi-card border-green">
                <p class="kpi-label">Total Sales (Sold)</p>
                <p class="kpi-value">$<?php echo number_format($total_sales, 2); ?></p>
            </div>

            <div class="kpi-card border-red">
                <p class="kpi-label">Total Expenses</p>
                <p class="kpi-value">$<?php echo number_format($total_expenses, 2); ?></p>
            </div>

            <div class="kpi-card border-indigo">
                <p class="kpi-label">Gross Profit</p>
                <p class="kpi-value">$<?php echo number_format($gross_profit, 2); ?></p>
            </div>

            <div class="kpi-card border-purple">
                <p class="kpi-label">Net Profit</p>
                <p class="kpi-value <?php echo ($net_profit < 0) ? 'text-red' : ''; ?>">
                    $<?php echo number_format($net_profit, 2); ?></p>
            </div>

            <div class="kpi-card border-yellow">
                <p class="kpi-label">Remaining Assets</p>
                <p class="kpi-value">$<?php echo number_format($total_assets, 2); ?></p>
            </div>

        </div>

        <!-- CHART 7 -->
        <div class="card">
            <h2>Monthly Sales and Expenses </h2>
            <canvas id="monthlyChart"></canvas>
        </div>

        <script>
            /* ==============================
                       7. MONTHLY DATA
            ============================== */
            const months =
                <?php echo json_encode($month_labels); ?>;
            const monthlySales =
                <?php echo json_encode($sales_monthly); ?>;
            const monthlyExpenses =
                <?php echo json_encode($expenses_monthly); ?>;

        </script>

        <script src="analytics.js"></script>

</body>

</html>