<?php
// Database Connection
include $_SERVER['DOCUMENT_ROOT'] . '/EXINS/db_connection.php';

// Check iff fform is submittedd
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Create Variable
    $id = $_POST['id'];
    $new_name = $_POST['new_name'];

    $stmt = $conn->prepare("UPDATE expense_category SET 
    CATEGORY_NAME = ? 
    WHERE ID = ?");

    $stmt->bind_param(
        "si",
        $new_name,
        $id
    );

    if ($stmt->execute()) {
        echo "Expense Updated successfully! <br>
        <a href='management_expense_category.php'>Go back</a>";
        exit();
    } else {
        echo $stmt->error;
    }
    $stmt->close();
}
$conn->close();
?>