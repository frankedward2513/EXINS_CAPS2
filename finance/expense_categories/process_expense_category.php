<?php
// Database Connection
include $_SERVER['DOCUMENT_ROOT'] . '/EXINS/db_connection.php';

// check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Create Variables
    $expense_category = $_POST['expense_category'];

    // Use prepared statements to prevent SQL Injection
    $stmt = $conn->prepare("INSERT INTO expense_category 
    (CATEGORY_NAME) 
    VALUES (?)");

    $stmt->bind_param(
        "s",
        $expense_category
    );

    if ($stmt->execute()) {
        // Back to Page
        echo "Expense Category Added Successfully! <br>
        <a href='management_expense_category.php'>Go back</a>";
        exit();
    } else {
        echo $stmt->error;
    }
    $stmt->close();
}
$conn->close();
?>