<?php
// Database Connection
include $_SERVER['DOCUMENT_ROOT'] . '/EXINS/db_connection.php';

$id = $_GET['id'];

// Fetch the Existing Data
$stmt = $conn->prepare("SELECT * FROM bales WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$bale = $result->fetch_assoc();
?>

<!-- External Style -->
<link rel="stylesheet" href="../../styles/form.css">

<!-- Form -->
<form action="update_bale.php" method="POST">

    <!-- Form Title -->
    <p>Edit Bale Form</p>

    <input type="hidden" name="id" value="<?php echo $bale['id']; ?>">

    <!-- Bale Name -->
    <label>Bale Name: </label><br>
    <input type="text" name="bale_name" value="<?php echo $bale['bale_name']; ?>" required><br><br>

    <!-- Quantity -->
    <label>Quantity: </label><br>
    <input type="number" name="b_quantity" value="<?php echo $bale['b_quantity']; ?>" required><br><br>

    <!-- Total Bale Price -->
    <label>Total Bale Price: </label><br>
    <input type="number" step="0.01" name="b_price" value="<?php echo $bale['b_price']; ?>" required><br><br>

    <!-- Submit -->
    <input type="submit" value="UPDATE">

</form>