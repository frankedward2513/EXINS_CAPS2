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
    <title>Table Expense</title>

    <!-- External Styles -->
    <link rel="stylesheet" href="../../styles/database.css">
</head>

<body>
    <h3>Expense Database</h3>
    <table border="1">

        <!-- Table Headers -->
        <tr>
            <th>ID</th>
            <th>Category</th>
            <th>Date</th>
            <th>Amount</th>
            <th>Receipt</th>
            <th>Description</th>
            <th>Action</th>
        </tr>
        <?php
        // Join with expense_category to display category name instead of ID number
        $query = "SELECT e.id, ec.CATEGORY_NAME, e.expense_date, e.amount, e.image_path, e.description 
                  FROM expenses e 
                  LEFT JOIN expense_category ec ON e.category_id = ec.ID";
        $result = $conn->query($query);

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $imgPath = $row['image_path'];
                $receiptDisplay = !empty($imgPath) ? "<img src='{$imgPath}' width='100' height='100' alt='Receipt'>" : "No Image";

                echo "<tr>
                        <td>{$row['id']}</td>
                        <td>{$row['CATEGORY_NAME']}</td>
                        <td>{$row['expense_date']}</td>
                        <td>{$row['amount']}</td>
                        <td>{$receiptDisplay}</td>
                        <td>{$row['description']}</td>
                        <td>
                            <a href='edit_expense.php?id={$row['id']}'>Edit</a> | 
                            <a href='delete_expense.php?id={$row['id']}' 
                            onclick='return confirm(\"Are you sure you want to delete this expense?\")'>Delete</a>
                        </td>
                    </tr>";

            }
        } else {
            echo "<tr>
                <td colspan='6'>No expenses found.</td>
            </tr>";
        }
        $conn->close();
        ?>
    </table>
</body>

</html>