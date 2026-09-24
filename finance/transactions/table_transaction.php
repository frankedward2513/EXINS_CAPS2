<?php
// Database Connection
include $_SERVER['DOCUMENT_ROOT'] . '/EXINS/db_connection.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Meta -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Title Page -->
    <title>Document</title>

    <!-- External Styles -->
    <link rel="stylesheet" href="../../styles/databse.css">
</head>

<body>
    <table>
        <!-- Table Headers -->
        <tr>
            <th>ID</th>
            <th>Customer</th>
            <th>Product ID</th>
            <th>Quantity</th>
            <th>Amount</th>
            <th>Transaction Type</th>
            <th>Remarks</th>
            <th>Date</th>
            <th>Actions</th>
        </tr>

        <?php
        // Fetch Data in Database
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                    <td>{$row['id']}</td>
                    <td>{$row['customer_name']}</td>
                    <td>{$row['product_id']}</td>
                    <td>{$row['quantity']}</td>
                    <td>" . number_format($row['amount'], 2) . "</td>
                    <td>{$row['transaction_type']}</td>
                    <td>{$row['remarks']}</td>
                    <td>{$row['transaction_date']}</td>
                    <td>
                    <a href='edit_transaction.php?id={$row['id']}'>Edit</a>
                </tr>";
            }
        } else {
            echo "<tr>
                <td colspan='9'>No transactions found.</td>
            </tr>";
        }
        $conn->close();
        ?>
    </table>
</body>

</html>