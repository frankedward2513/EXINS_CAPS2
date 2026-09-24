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
    <title>Expense Categories Management</title>

    <!-- External Styles -->
    <link rel="stylesheet" href="../../styles/database.css">
</head>

<body>

    <h3>Expense Categories Database</h3>
    <table border="1">

        <!-- Table Headers -->
        <tr>
            <th>ID</th>
            <th>Category Name</th>
            <th>Action</th>
        </tr>

        <?php

        // Fetch Data in Database
        $sql = "SELECT ID, CATEGORY_NAME FROM expense_category ORDER BY ID DESC";

        $result = $conn->query($sql);
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {

                echo "<tr> 
                    <td>{$row['ID']}</td>
                    <td>{$row['CATEGORY_NAME']}</td>

                    <td>
                        <a href='edit_expense_category.php?id={$row['ID']}'>Edit</a> |
                        <a href='delete_expense_category.php?id={$row['ID']}' 
                        onclick='return confirm(\"Are you sure you want to delete this category?\")'>Delete</a>
                    </td>
                </tr>";

            }
        } else {
            echo "<tr>
                <td colspan='3'>No expense categories found.</td>
            </tr>";
        }
        $conn->close();
        ?>
    </table>
</body>

</html>