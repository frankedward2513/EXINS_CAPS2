<?php
session_start();
unset($_SESSION['customer_id']);
unset($_SESSION['customer_name']);
unset($_SESSION['customer_email']);
session_destroy();
header("Location: ../../system_customer.php");
exit();
?>