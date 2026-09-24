<?php
include $_SERVER['DOCUMENT_ROOT'] . '/EXINS/db_connection.php';

// --- GET FILTER DATES ---
$start_date = $_GET['start_date'] ?? '';
$end_date = $_GET['end_date'] ?? '';

// Build optional date clauses for SQL
$transaction_date_filter = "";
$expense_date_filter = "";

if (!empty($start_date) && !empty($end_date)) {
    $s_safe = mysqli_real_escape_string($conn, $start_date);
    $e_safe = mysqli_real_escape_string($conn, $end_date);

    $transaction_date_filter = " AND transaction_date BETWEEN '$s_safe 00:00:00' AND '$e_safe 23:59:59'";
    $expense_date_filter = " AND expense_date BETWEEN '$s_safe 00:00:00' AND '$e_safe 23:59:59'";
}

// --- SQL QUERIES & EXECUTION ---

// 1. Total Customers
$query_customers = "SELECT COUNT(DISTINCT customer_name) AS total_customers FROM transactions WHERE 1=1" . $transaction_date_filter;
$result_customers = mysqli_query($conn, $query_customers);
$total_customers = mysqli_fetch_assoc($result_customers)['total_customers'] ?? 0;

// 2. Total Sales
$query_sales = "SELECT SUM(amount) AS total_sales FROM transactions WHERE transaction_type = 'Sold'" . $transaction_date_filter;
$result_sales = mysqli_query($conn, $query_sales);
$total_sales = mysqli_fetch_assoc($result_sales)['total_sales'] ?? 0;

// 3. Total Expenses
$query_expenses = "SELECT SUM(amount) AS total_expenses FROM expenses WHERE 1=1" . $expense_date_filter;
$result_expenses = mysqli_query($conn, $query_expenses);
$total_expenses = mysqli_fetch_assoc($result_expenses)['total_expenses'] ?? 0;

// 4. Total Gross Profit
$query_profit = "
    SELECT SUM(t.amount - (t.quantity * b.price_per_piece)) AS total_profit
    FROM transactions t
    INNER JOIN products p ON t.product_id = p.id
    INNER JOIN bales b ON p.bale_id = b.id
    WHERE t.transaction_type = 'Sold'
    $transaction_date_filter
";
$result_profit = mysqli_query($conn, $query_profit);

if (!$result_profit) {
    die("Query Error: " . mysqli_error($conn));
}

$row = mysqli_fetch_assoc($result_profit);
$gross_profit = $row['total_profit'] ?? 0;

// Net Profit Calculation
$net_profit = $gross_profit - $total_expenses;

// 5. Total Remaining Asset Amount
$query_assets = "
    SELECT SUM(p.quantity * b.price_per_piece) AS total_assets 
    FROM products p
    JOIN bales b ON p.bale_id = b.id
";
$result_assets = mysqli_query($conn, $query_assets);
$total_assets = mysqli_fetch_assoc($result_assets)['total_assets'] ?? 0;

// 6. Chart Data: Monthly Sales
$query_chart_sales = "SELECT DATE_FORMAT(transaction_date, '%Y-%m') AS period, SUM(amount) AS total FROM transactions WHERE transaction_type = 'Sold'" . $transaction_date_filter . " GROUP BY period ORDER BY period ASC";
$res_cs = mysqli_query($conn, $query_chart_sales);
$sales_by_month = [];
while ($row = mysqli_fetch_assoc($res_cs)) {
    $sales_by_month[$row['period']] = $row['total'];
}

// 7. Chart Data: Monthly Expenses
$query_chart_exp = "SELECT DATE_FORMAT(expense_date, '%Y-%m') AS period, SUM(amount) AS total FROM expenses WHERE 1=1" . $expense_date_filter . " GROUP BY period ORDER BY period ASC";
$res_ce = mysqli_query($conn, $query_chart_exp);
$expenses_by_month = [];
while ($row = mysqli_fetch_assoc($res_ce)) {
    $expenses_by_month[$row['period']] = $row['total'];
}

// Format and combine chart periods
$months = [];
$sales_data = [];
$expense_data = [];
$all_periods = array_unique(array_merge(array_keys($sales_by_month), array_keys($expenses_by_month)));
sort($all_periods);

foreach ($all_periods as $period) {
    $months[] = $period;
    $sales_data[] = $sales_by_month[$period] ?? 0;
    $expense_data[] = $expenses_by_month[$period] ?? 0;
}
?>