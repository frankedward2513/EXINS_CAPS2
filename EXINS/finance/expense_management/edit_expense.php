<?php
// Database Connection
include $_SERVER['DOCUMENT_ROOT'] . '/EXINS/db_connection.php';

$id = $_GET['id'];

// Fetch the existing expense data
$stmt = $conn->prepare("SELECT * FROM expenses WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$expense = $result->fetch_assoc();
$stmt->close();

// Fetch expense categories for dropdown
$category_query = $conn->query("SELECT ID, CATEGORY_NAME FROM expense_category");
?>

<!-- External Styles -->
<link rel="stylesheet" href="../../styles/form.css">

<!-- Form -->
<form action="update_expense.php" enctype="multipart/form-data" method="POST">

    <!-- Form Title -->
    <p>Edit Expense Form</p>

    <input type="hidden" name="id" value="<?php echo $expense['id']; ?>">
    <input type="hidden" name="old_image" value="<?php echo $expense['image_path']; ?>">

    <!-- Expense Category -->
    <label for="expense_category">Expense Category:</label><br>
    <select name="expense_category" id="expense_category">
        <?php
        while ($row = $category_query->fetch_assoc()) {
            $selected = ($row['ID'] == $expense['category_id']) ? "selected" : "";
            echo "<option value='{$row['ID']}' {$selected}>{$row['CATEGORY_NAME']}</option>";
        }
        ?>
    </select><br>

    <!-- Date -->
    <label for="expense_date">Date:</label><br>
    <input type="date" name="expense_date" value="<?php echo $expense['expense_date']; ?>"><br>

    <!-- Amount -->
    <label for="amount">Amount:</label><br>
    <input type="number" step="0.01" name="amount" value="<?php echo $expense['amount']; ?>"><br>

    <!-- Image Receipt -->
    <label for="myfile">New Receipt Image</label><br>
    <input type="file" name="myfile"><br>
    <?php if (!empty($expense['image_path'])): ?>
        <p>Current Receipt:</p>
        <img src="<?php echo $expense['image_path']; ?>" width="100" height="100"><br>
    <?php endif; ?>

    <!-- Description -->
    <label for="description">Description (Optional):</label><br>
    <input type="text" name="description" value="<?php echo $expense['description']; ?>"><br>

    <!-- Suubmit -->
    <div id="submitorcancel">
        <input type="submit" value="UPDATE" id="submit">
        <a href="management_expense.php">Cancel</a>
    </div>

</form>