<?php
// Database Connection
include $_SERVER['DOCUMENT_ROOT'] . '/EXINS/db_connection.php';

// Check if form iss submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Create Variables
    $category_id = $_POST['expense_category'];
    $expense_date = $_POST['expense_date'];
    $amount = $_POST['amount'];
    $description = $_POST['description'];

    $image_path = "";

    // Handle optional file upload for receipt
    if (isset($_FILES['myfile']) && $_FILES['myfile']['error'] == 0) {
        $target_dir = "../../uploads/expense/";
        $file_name = time() . "_" . basename($_FILES['myfile']['name']);
        $image_path = $target_dir . $file_name;
        move_uploaded_file($_FILES['myfile']['tmp_name'], $image_path);
    }

    // Use prepared statements to prevent SQL Injection
    $stmt = $conn->prepare("INSERT INTO expenses 
    (category_id, expense_date, amount, image_path, description) 
    VALUES (?, ?, ?, ?, ?)");

    $stmt->bind_param(
        "isdss",
        $category_id,
        $expense_date,
        $amount,
        $image_path,
        $description
    );

    if ($stmt->execute()) {
        echo "Expense added successfully! <br>
        <a href='management_expense.php'>Go back</a>";
        exit();
    } else {
        echo $stmt->error;
    }
    $stmt->close();
}
$conn->close();
?>