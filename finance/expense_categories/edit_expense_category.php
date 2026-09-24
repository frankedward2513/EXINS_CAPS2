<?php
// Database Connection
include $_SERVER['DOCUMENT_ROOT'] . '/EXINS/db_connection.php';

$id = $_GET['id'];

// Fetch the Existing Data
$stmt = $conn->prepare("SELECT CATEGORY_NAME FROM expense_category WHERE ID = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$expense_category = $result->fetch_assoc();
?>

<link rel="stylesheet" href="../../styles/form.css">

<!-- Form -->
<form action="update_expense_category.php" method="POST">

    <!-- Form Title -->
    <p>Edit Category Form</p>

    <!-- ID -->
    <input type="hidden" name="id" value="<?php echo $id; ?>">

    <!-- Expense Category Name -->
    <label>Edit Expense Category Name:</label>
    <input type="text" name="new_name" value="<?php echo $expense_category['CATEGORY_NAME']; ?>">

    <!-- Submit -->
    <input type="submit" value="UPDATE">

</form>