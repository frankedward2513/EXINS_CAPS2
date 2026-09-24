<?php
// Database Connection
include $_SERVER['DOCUMENT_ROOT'] . '/EXINS/db_connection.php';
/* =====================================================
7.1 MONTHLY SALES
===================================================== */
$sql = "
SELECT
DATE_FORMAT(transaction_date, '%Y-%m') AS month,
SUM(amount) AS total
FROM transactions
WHERE transaction_type = 'Sold'
GROUP BY month
ORDER BY month
";

$result = mysqli_query($conn, $sql);

$monthly_sales = [];

while ($row = mysqli_fetch_assoc($result)) {
    $monthly_sales[$row['month']] = (float) $row['total'];
}

/* =====================================================
7.2 MONTHLY EXPENSES
===================================================== */
$sql = "
SELECT
DATE_FORMAT(expense_date, '%Y-%m') AS month,
SUM(amount) AS total
FROM expenses
GROUP BY month
ORDER BY month
";

$result = mysqli_query($conn, $sql);

$monthly_expenses = [];

while ($row = mysqli_fetch_assoc($result)) {
    $monthly_expenses[$row['month']] = (float) $row['total'];
}


/* =====================================================
MONTHLY LABELS
===================================================== */

$months = array_unique(
    array_merge(
        array_keys($monthly_sales),
        array_keys($monthly_expenses)
    )
);

sort($months);

$month_labels = [];
$sales_monthly = [];
$expenses_monthly = [];

foreach ($months as $month) {
    $month_labels[] = date('M Y', strtotime($month . '-01'));
    $sales_monthly[] = $monthly_sales[$month] ?? 0;
    $expenses_monthly[] = $monthly_expenses[$month] ?? 0;
}
?>