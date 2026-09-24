<?php
// Database Connection
include $_SERVER['DOCUMENT_ROOT'] . '/EXINS/db_connection.php';

// Check if form is submitted 
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Create Variable
    $id = $_POST['id'];
    $category_id = $_POST['expense_category'];
    $expense_date = $_POST['expense_date'];
    $amount = $_POST['amount'];
    $description = $_POST['description'];

    $image_path = $_POST['old_image'];

    // Check if a new file was uploaded
    if (isset($_FILES['myfile']) && $_FILES['myfile']['error'] == 0) {
        $target_dir = "../../uploads/expense/";
        $up_expense = time() . "_" . basename($_FILES['myfile']['name']);
        $image_path = $target_dir . $up_expense;
        move_uploaded_file($_FILES['myfile']['tmp_name'], $image_path);
    }

    // Use prepared statements top prevent sql injection
    $stmt = $conn->prepare("UPDATE expenses SET 
    category_id = ?, 
    expense_date = ?, 
    amount = ?, 
    image_path = ?, 
    description = ? 
    WHERE id = ?");

    $stmt->bind_param(
        "isdssi",
        $category_id,
        $expense_date,
        $amount,
        $image_path,
        $description,
        $id
    );

    if ($stmt->execute()) {
        // Back to Page
        echo "Expense Changed Successfully! <br>
        <a href='management_expense.php'>Go back</a>";
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
    $stmt->close();
}
$conn->close();
?>