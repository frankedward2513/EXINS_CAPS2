<?php
// Database Connection
include $_SERVER['DOCUMENT_ROOT'] . '/EXINS/db_connection.php';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Create Variables
    $supplier_name = $_POST['supplier_name'];
    $contact_person = $_POST['contact_person'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $address = $_POST['address'];

    // Use prepared statements to prevent SQL Injection 
    $stmt = $conn->prepare("INSERT INTO supplier
    (supplier_name, contact_person, phone, email, address) 
    VALUES (?, ?, ?, ?, ?)");

    $stmt->bind_param(
        "sssss",
        $supplier_name,
        $contact_person,
        $phone,
        $email,
        $address
    );

    if ($stmt->execute()) {
        // Back to page
        echo "Supplier Added Successfully! <br>
        <a href='management_supplier.php'>Go back</a>";
        exit();
    } else {
        echo $stmt->error;
    }
    $stmt->close();
}
$conn->close();
?>