<?php
session_start();
include '../resources/db_config.php';

// If user is logged in, clear the remember_token in DB
if (isset($_SESSION['inst_id'])) {
    $inst_id = $_SESSION['inst_id'];

    $stmt = $conn->prepare("UPDATE instructors SET remember_token = NULL WHERE inst_id = ?");
    $stmt->bind_param("i", $inst_id);
    $stmt->execute();
}

// Destroy all session variables
$_SESSION = [];
session_unset();
session_destroy();

// Expire the remember_token cookie
if (isset($_COOKIE['remember_token'])) {
    setcookie("remember_token", "", time() - 3600, "/", "", false, true);
}

// Redirect back to login page
header("Location: login_instructor.php");
exit();
