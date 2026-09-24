<?php
include $_SERVER['DOCUMENT_ROOT'] . '/EXINS/db_connection.php';

// Collect filter values
$search_id = isset($_GET['id']) ? $conn->real_escape_string($_GET['id']) : '';
$search_customer = isset($_GET['customer']) ? $conn->real_escape_string($_GET['customer']) : '';
$search_pid = isset($_GET['pid']) ? $conn->real_escape_string($_GET['pid']) : '';
$search_qty = isset($_GET['qty']) ? $conn->real_escape_string($_GET['qty']) : '';
$search_amount = isset($_GET['amount']) ? $conn->real_escape_string($_GET['amount']) : '';
$search_type = isset($_GET['type']) ? $conn->real_escape_string($_GET['type']) : '';
$search_date = isset($_GET['date']) ? $conn->real_escape_string($_GET['date']) : '';

// Build query
$sql = "SELECT * FROM transactions WHERE 1=1";

// ID 
if (!empty($search_id))
    $sql .= " AND id='$search_id'";
// Customer Name
if (!empty($search_customer))
    $sql .= " AND customer_name LIKE '%$search_customer%'";
// Product ID
if (!empty($search_pid))
    $sql .= " AND product_id='$search_pid'";
// Quantity
if (!empty($search_qty))
    $sql .= " AND quantity='$search_qty'";
// Amount
if (!empty($search_amount))
    $sql .= " AND amount='$search_amount'";
// Type
if (!empty($search_type))
    $sql .= " AND transaction_type='$search_type'";
// Date
if (!empty($search_date))
    $sql .= " AND DATE(transaction_date)='$search_date'";

$sql .= " ORDER BY transaction_date DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Meta -->
    <meta charset="UTF-8">

    <!-- Title Page -->
    <title>Transaction History</title>

    <!-- External Styles -->
    <link rel="stylesheet" href="../../styles/transactions.css">
</head>

<body>
    <!-- Title -->
    <h2>Transaction History</h2>

    <!-- Search Field -->
    <form method="GET" class="filter-form">

        <!-- Transaction ID -->
        <input type="number" name="id" class="filter-input" placeholder="Transaction ID"
            value="<?php echo $search_id; ?>">

        <!-- Customer Name -->
        <input type="text" name="customer" class="filter-input" placeholder="Customer Name"
            value="<?php echo $search_customer; ?>">

        <!-- Product ID -->
        <input type="number" name="pid" class="filter-input" placeholder="Product ID"
            value="<?php echo $search_pid; ?>">

        <!-- Type -->
        <select name="type" class="filter-input">
            <option value="Sold" <?php if ($search_type == "Sold")
                echo "selected"; ?>>Sold</option>
            <option value="Returned" <?php if ($search_type == "Returned")
                echo "selected"; ?>>Returned</option>
            <option value="Damaged" <?php if ($search_type == "Damaged")
                echo "selected"; ?>>Damaged</option>
            <option value="Lost" <?php if ($search_type == "Lost")
                echo "selected"; ?>>Lost</option>
        </select>

        <!-- Date -->
        <input type="date" name="date" class="filter-input" value="<?php echo $search_date; ?>">

        <!-- Filter -->
        <button type="submit" class="filter-btn">Filter</button>

        <!-- Reset -->
        <a href="transactions.php">Clear</a>
    </form>

    <!-- Database -->
    <?php
    include 'table_transaction.php';
    ?>

</body>

</html>