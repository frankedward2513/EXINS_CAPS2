<?php
include $_SERVER['DOCUMENT_ROOT'] . '/EXINS/db_connection.php';

// Display checkout form
$pid = isset($_POST['pid']) ? $_POST['pid'] : '';
$pname = isset($_POST['pname']) ? $_POST['pname'] : '';
$price = isset($_POST['price']) ? $_POST['price'] : '0.00';
$current_date = date('Y-m-d'); // Get current date in YYYY-MM-DD format
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link rel="stylesheet" href="../styles/checkout.css">
</head>

<body>

    <form action="process_trans.php" method="POST" id="checkoutForm">
        <input type="hidden" name="action" value="confirm_purchase">
        <input type="hidden" name="pid" value="<?php echo $pid; ?>">
        <input type="hidden" name="pname" value="<?php echo $pname; ?>">

        <!-- Product Name -->
        <p><strong>Product:</strong> <?php echo $pname; ?></p><br>

        <!-- Customer Name  -->
        <label for="customer_name">Customer Name:</label><br>
        <input type="text" name="customer_name" id="customer_name" required placeholder="Enter customer name"><br>

        <!-- Transaction Date -->
        <label for="transaction_date">Transaction Date:</label><br>
        <input type="date" name="transaction_date" id="transaction_date" value="<?php echo $current_date; ?>"
            required><br>

        <!-- Quantity -->
        <label for="qty">Quantity:</label><br>
        <input type="number" name="qty" id="qty" value="1" min="1" oninput="calculateTotal()"><br>

        <!-- Transaction Type -->
        <label for="transaction_type">Transaction Type:</label><br>
        <select name="transaction_type" id="transactionType">
            <option value="Sold" selected>Sold</option>
            <option value="Returned">Returned</option>
            <option value="Damaged">Damaged</option>
            <option value="Lost">Lost</option>
        </select><br>

        <!-- Price Per Unit -->
        <label for="pricePerUnit">Price Per Unit:</label><br>
        <input type="number" step="0.01" name="pricePerUnit" id="pricePerUnit" value="<?php echo $price; ?>"
            oninput="calculateTotal()"><br>

        <!-- Total Amount -->
        <label for="totalAmount"><strong>Total Amount:</strong></label><br>
        <input type="number" step="0.01" name="totalAmount" id="totalAmount" readonly><br>

        <!-- Amount Tendered -->
        <label for="tendered">Amount Tendered:</label><br>
        <input type="number" step="0.01" name="tendered" id="tendered" oninput="calculateChange()"><br>

        <!-- Change -->
        <label for="changeDisplay"><strong>Change:</strong></label>
        <h2 name="changeDisplay" id="changeDisplay">0.00</h2>

        <!-- Description -->
        <label for="remarks">Remarks:</label><br>
        <textarea name="remarks" id="remarks" rows="4" placeholder="Optional remarks"></textarea><br>

        <div id="submitorcancel">
            <input type="submit" id="submit" value="Confirm Transaction">
        </div>

    </form>

    <script>
        window.onload = function () {
            calculateTotal();
        };

        function calculateTotal() {
            const qty = parseFloat(document.getElementById('qty').value) || 0;
            const price = parseFloat(document.getElementById('pricePerUnit').value) || 0;
            const total = qty * price;
            document.getElementById('totalAmount').value = total.toFixed(2);
            calculateChange();
        }

        function calculateChange() {
            const total = parseFloat(document.getElementById('totalAmount').value) || 0;
            const tendered = parseFloat(document.getElementById('tendered').value) || 0;
            const change = tendered - total;
            const display = document.getElementById('changeDisplay');
            display.innerHTML = change.toFixed(2);
        }
    </script>

</body>

</html>