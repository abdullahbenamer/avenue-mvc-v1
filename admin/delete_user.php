<?php
session_start();
require_once '../resources/db_config.php';
if (!isset($_SESSION['user_name']) || 
   ($_SESSION['user_role'] != 'ADMIN' && $_SESSION['user_role'] != 'ACCOUNTANT')) {
    header("Location: ../unauthorized.php");
    exit();
}

//// === delete_user.php === 
if (isset($_GET['user_id'])) {
    $user_id = intval($_GET['user_id']);
    $stmt = mysqli_prepare($conn, "DELETE FROM users WHERE user_id = ?");
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    header("Location: users.php");
    exit();
}
?>