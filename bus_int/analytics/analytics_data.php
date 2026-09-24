<?php
// Database Connection
include $_SERVER['DOCUMENT_ROOT'] . '/EXINS/db_connection.php';

/* =====================================================
   1. REMAINING INVENTORY BY CATEGORY
===================================================== */
$sql = "
    SELECT 
        c.CATEGORY_NAME,
        SUM(p.quantity) AS total_quantity
    FROM products p
    INNER JOIN product_category c
        ON p.category_id = c.ID
    GROUP BY c.ID, c.CATEGORY_NAME
    ORDER BY total_quantity DESC
";

$result = mysqli_query($conn, $sql);

$inventory_categories = [];
$inventory_quantity = [];

while ($row = mysqli_fetch_assoc($result)) {
    $inventory_categories[] = $row['CATEGORY_NAME'];
    $inventory_quantity[] = (int) $row['total_quantity'];
}

/* =====================================================
   2. SALES BY CATEGORY
===================================================== */
$sql = "
    SELECT 
        c.CATEGORY_NAME,
        SUM(t.amount) AS total_sales
    FROM transactions t
    INNER JOIN products p
        ON t.product_id = p.id
    INNER JOIN product_category c
        ON p.category_id = c.ID
    WHERE t.transaction_type = 'Sold'
    GROUP BY c.ID, c.CATEGORY_NAME
    ORDER BY total_sales DESC
";

$result = mysqli_query($conn, $sql);

$sales_categories = [];
$sales_amount = [];

while ($row = mysqli_fetch_assoc($result)) {
    $sales_categories[] = $row['CATEGORY_NAME'];
    $sales_amount[] = (float) $row['total_sales'];
}

/* =====================================================
   3. EXPENSES BY CATEGORY
===================================================== */
$sql = "
    SELECT 
        ec.CATEGORY_NAME,
        SUM(e.amount) AS total_expenses
    FROM expenses e
    INNER JOIN expense_category ec
        ON e.category_id = ec.ID
    GROUP BY ec.ID, ec.CATEGORY_NAME
    ORDER BY total_expenses DESC
";

$result = mysqli_query($conn, $sql);

$expense_categories = [];
$expense_amount = [];

while ($row = mysqli_fetch_assoc($result)) {
    $expense_categories[] = $row['CATEGORY_NAME'];
    $expense_amount[] = (float) $row['total_expenses'];
}

/* =====================================================
   4. TRANSACTION TYPES
===================================================== */
$sql = "
    SELECT 
        transaction_type,
        SUM(quantity) AS total_quantity
    FROM transactions
    GROUP BY transaction_type
";

$result = mysqli_query($conn, $sql);

$transaction_types = [
    'Sold' => 0,
    'Returned' => 0,
    'Damaged' => 0,
    'Lost' => 0
];

while ($row = mysqli_fetch_assoc($result)) {

    $type = ucfirst(strtolower(trim(
        $row['transaction_type']
    )));

    if (isset($transaction_types[$type])) {
        $transaction_types[$type] =
            (int) $row['total_quantity'];
    }
}

/* =====================================================
   5. TOP CUSTOMERS
===================================================== */
$sql = "
    SELECT
        customer_name,
        SUM(amount) AS total_amount
    FROM transactions
    WHERE transaction_type = 'Sold'

    AND customer_name IS NOT NULL
    AND TRIM(customer_name) != ''
    GROUP BY customer_name
    ORDER BY total_amount DESC
    LIMIT 10
";

$result = mysqli_query($conn, $sql);

$customer_names = [];
$customer_amount = [];

while ($row = mysqli_fetch_assoc($result)) {
    $customer_names[] = $row['customer_name'];
    $customer_amount[] = (float) $row['total_amount'];
}


/* =====================================================
   6. SLOW MOVING PRODUCTS
===================================================== */
$sql = "
    SELECT
        p.product_name,
        p.quantity,
        DATEDIFF(
            CURDATE(), 
            COALESCE(
                MAX(
                    CASE 
                        WHEN t.transaction_type = 'Sold' 
                        THEN DATE(t.transaction_date) 
                    END
                ), 
                DATE(p.created_at)
            )
        ) AS days_inactive
    FROM products p
    LEFT JOIN transactions t
        ON p.id = t.product_id
    WHERE p.quantity > 0
    GROUP BY
        p.id,
        p.product_name,
        p.quantity,
        p.created_at
    ORDER BY days_inactive DESC
    LIMIT 10
";

$result = mysqli_query($conn, $sql);

$slow_products = [];
$slow_days = [];

while ($row = mysqli_fetch_assoc($result)) {
    $slow_products[] = $row['product_name'];
    $slow_days[] = (int) $row['days_inactive'];
}


/* =====================================================
   7. MONTHLY SALES
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
    $monthly_sales[$row['month']] =
        (float) $row['total'];
}


/* =====================================================
   8. MONTHLY EXPENSES
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
    $monthly_expenses[$row['month']] =
        (float) $row['total'];
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

    $month_labels[] =
        date('M Y', strtotime($month . '-01'));

    $sales_monthly[] =
        $monthly_sales[$month] ?? 0;

    $expenses_monthly[] =
        $monthly_expenses[$month] ?? 0;
}

/* =====================================================
   9. INVENTORY VALUE BY CATEGORY
===================================================== */

$sql = "
    SELECT
        c.CATEGORY_NAME,
        SUM(
            p.quantity * b.price_per_piece
        ) AS inventory_value
    FROM products p
    INNER JOIN product_category c
        ON p.category_id = c.ID
    INNER JOIN bales b
        ON p.bale_id = b.id
    WHERE p.quantity > 0
    GROUP BY c.ID, c.CATEGORY_NAME
    ORDER BY inventory_value DESC
";

$result = mysqli_query($conn, $sql);

$inventory_value_categories = [];
$inventory_values = [];

while ($row = mysqli_fetch_assoc($result)) {

    $inventory_value_categories[] =
        $row['CATEGORY_NAME'];

    $inventory_values[] =
        (float) $row['inventory_value'];
}

?>