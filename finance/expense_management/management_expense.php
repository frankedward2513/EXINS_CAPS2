<?php
// Database Connection
include $_SERVER['DOCUMENT_ROOT'] . '/EXINS/db_connection.php';

// Fetch Categories for Options
$category_result = $conn->query("SELECT ID, CATEGORY_NAME FROM expense_category");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Meta -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Title Page -->
    <title>Expense Management</title>

    <!-- External Styles -->
    <link rel="stylesheet" href="../../styles/form.css">
</head>

<body>
    <form action="process_expense.php" enctype="multipart/form-data" method="POST">

        <!-- Form Title -->
        <p>Expense Management Form</p>

        <!-- Expense Category -->
        <label for="expense_category">Expense Category:</label><br>
        <select name="expense_category" id="expense_category" required>
            <option value="">- - Select Category - -</option>
            <?php
            if ($category_result && $category_result->num_rows > 0) {
                while ($cat = $category_result->fetch_assoc()) {
                    echo "<option value= '{$cat['ID']}'>{$cat['CATEGORY_NAME']}</option>";
                }
            }
            ?>
        </select><br>

        <!-- Date -->
        <label for="expense_date">Date:</label><br>
        <input type="date" id="expense_date" name="expense_date" value="<?php echo date('Y-m-d'); ?>" required><br>

        <!-- Amount -->
        <label for="amount">Amount:</label><br>
        <input type="number" step="0.01" name="amount" id="amount" required><br>

        <!-- Receipt Image -->
        <label for="myfile">Receipt Image (Optional):</label><br>
        <input type="file" id="myfile" name="myfile"><br>

        <!-- Description -->
        <label for="description">Description (Optional):</label><br>
        <input type="text" id="description" name="description"><br>

        <!-- Submit / Cancel -->
        <div id="submitorcancel">
            <input type="submit" value="SUBMIT" id="submit">
            <input type="reset" value="CANCEL" id="reset">
        </div>
    </form>

    <hr>

    <!-- Database -->
    <?php
    include 'table_expense.php';
    ?>
</body>

</html>