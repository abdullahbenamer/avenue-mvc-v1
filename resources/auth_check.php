<?php
if (session_status() === PHP_SESSION_NONE) {
session_start();
}
$allowed_roles = ['ADMIN', 'ACCOUNTANT'];
if (!isset($_SESSION['user_role']) || !in_array($_SESSION['user_role'], $allowed_roles)) {
header("Location: ../login.php");
exit();
}

