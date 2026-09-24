<?php
// Database Connection
include $_SERVER['DOCUMENT_ROOT'] . '/EXINS/db_connection.php';

$id = $_GET['id'];

// Fetch the Existing Data
$stmt = $conn->prepare("SELECT * FROM transactions WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
?>

<!-- External Styles -->
<link rel="stylesheet" href="../../styles/form.css">

<body>
    <form action="update_transaction.php" method="POST">

        <!-- Form Title -->
        <h2>Edit Transaction</h2>

        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">

        <!-- Customer Name -->
        <label>Customer Name</label>
        <input type="text" name="customer_name" required value="<?php echo $row['customer_name']; ?>">

        <!-- Product ID -->
        <label>Product ID</label>
        <input type="number" name="product_id" required value="<?php echo $row['product_id']; ?>">

        <!-- Quantity -->
        <label>Quantity</label>
        <input type="number" name="quantity" required min="1" value="<?php echo $row['quantity']; ?>">

        <!-- Amount -->
        <label>Amount</label>
        <input type="number" step="0.01" name="amount" required value="<?php echo $row['amount']; ?>">

        <!-- Transaction Type -->
        <label>Transaction Type</label>
        <select name="transaction_type">

            <option value="Sold" <?php if ($row['transaction_type'] == "Sold")
                echo "selected"; ?>>
                Sold
            </option>

            <option value="Returned" <?php if ($row['transaction_type'] == "Returned")
                echo "selected"; ?>>
                Returned
            </option>

            <option value="Damaged" <?php if ($row['transaction_type'] == "Damaged")
                echo "selected"; ?>>
                Damaged
            </option>

            <option value="Lost" <?php if ($row['transaction_type'] == "Lost")
                echo "selected"; ?>>
                Lost
            </option>

        </select>

        <!-- Remarks -->
        <label>Remarks</label>
        <textarea name="remarks" rows="4"><?php echo $row['remarks']
        ; ?></textarea>

        <!-- Transaction Date -->
        <label>Transaction Date</label>
        <input type="datetime-local" name="transaction_date"
            value="<?php echo date('Y-m-d\TH:i', strtotime($row['transaction_date'])); ?>">

        <!-- Submit -->
        <button type="submit" class="btn-save">
            Update Transaction
        </button>

    </form>

    <a href="transactions.php" class="btn-back">
        Back to Transactions
    </a>

    </div>

</body>

</html>