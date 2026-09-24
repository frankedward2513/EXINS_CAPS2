<?php
session_start();
unset($_SESSION['owner_id']);
unset($_SESSION['owner_email']);
session_destroy();
header("Location: ../system.php");
exit();
?>