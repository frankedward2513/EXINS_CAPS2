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
    <title>Expense Categories</title>

    <!-- External Styles -->
    <link rel="stylesheet" href="../../styles/form.css">
</head>

<body>
    <form action="process_expense_category.php" method="POST">

        <!-- Form Title -->
        <h1>Expense Category</h1>

        <!-- Expense Category -->
        <label for="expense_category">Expense Category Name: </label>
        <input type="text" id="expense_category" name="expense_category" required>

        <!-- Submit / Cancel -->
        <div id="submitorcancel">
            <input type="submit" value="SUBMIT" id="submit">
            <input type="reset" value="CANCEL" id="reset">
        </div>

    </form>

    <hr>

    <!-- Database -->
    <?php
    include 'table_expense_category.php';
    ?>

</body>

</html>