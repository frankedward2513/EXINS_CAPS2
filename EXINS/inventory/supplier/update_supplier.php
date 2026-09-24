<?php
// Database Connection
include $_SERVER['DOCUMENT_ROOT'] . '/EXINS/db_connection.php';

// Chcck if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Create Variable
    $supplier_id = $_POST['supplier_id'];
    $supplier_name = $_POST['supplier_name'];
    $contact_person = $_POST['contact_person'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $address = $_POST['address'];

    $stmt = $conn->prepare("UPDATE supplier SET 
    supplier_name = ?, 
    contact_person = ?, 
    phone = ?, email = ?, 
    address = ? 
    WHERE supplier_id = ?");

    $stmt->bind_param(
        "sssssi",
        $supplier_name,
        $contact_person,
        $phone,
        $email,
        $address,
        $supplier_id
    );

    if ($stmt->execute()) {
        // Back to page
        echo "Supplier Changed Successfully! <br>
        <a href='management_supplier.php'>Go back</a>";
        exit();
    } else {
        echo $stmt->error;
    }
    $stmt->close();
}
$conn->close();
?>