<?php
//Database Connection
include $_SERVER['DOCUMENT_ROOT'] . '/EXINS/db_connection.php';

$id = $_GET['id'] ?? 0;

if ($id > 0) {
    $stmt = $conn->prepare("DELETE FROM supplier WHERE supplier_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

$conn->close();
header("Location: management_supplier.php");
exit();
?>