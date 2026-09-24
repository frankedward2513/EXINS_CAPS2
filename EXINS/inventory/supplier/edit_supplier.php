<?php
// Database Connection
include $_SERVER['DOCUMENT_ROOT'] . '/EXINS/db_connection.php';

$id = $_GET['id'];

// Fetch the Existing Data
$stmt = $conn->prepare("SELECT * FROM supplier WHERE supplier_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$supplier = $result->fetch_assoc();
?>

<!-- External Styles -->
<link rel="stylesheet" href="../../styles/form.css">

<!-- Form -->
<form action="update_supplier.php" method="POST">

    <!-- Form Title -->
    <p>Edit Supplier Form</p>

    <input type="hidden" name="supplier_id" value="<?php echo $supplier['supplier_id']; ?>">

    <!-- Supplier Name -->
    <label>Supplier Name:</label><br>
    <input type="text" name="supplier_name" value="<?php echo $supplier['supplier_name']; ?>"><br><br>

    <!-- Contact Person -->
    <label>Contact Person:</label><br>
    <input type="text" name="contact_person" value="<?php echo $supplier['contact_person']; ?>"><br><br>

    <!-- Phone -->
    <label>Phone:</label><br>
    <input type="text" name="phone" value="<?php echo $supplier['phone']; ?>"><br><br>

    <!-- Email -->
    <label>Email:</label><br>
    <input type="email" name="email" value="<?php echo $supplier['email']; ?>"><br><br>

    <!-- Address -->
    <label>Address:</label><br>
    <input type="text" name="address" value="<?php echo $supplier['address']; ?>"><br><br>

    <!-- Submit -->
    <input type="submit" value="UPDATE">

</form>