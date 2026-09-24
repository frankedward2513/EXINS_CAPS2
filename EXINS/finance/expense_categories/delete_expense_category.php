<?php
// Database Connection
include $_SERVER['DOCUMENT_ROOT'] . '/EXINS/db_connection.php';

$id = $_GET['id'] ?? 0;

if ($id > 0) {
    $stmt = $conn->prepare("DELETE FROM expense_category WHERE ID = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

$conn->close();
header("Location: management_expense_category.php");
exit();
?>