<?php
session_start();
require_once 'resources/db_config.php';

// Check authentication and role
if (!isset($_SESSION['user_name']) || $_SESSION['user_role'] !== 'ACCOUNTANT') {
    header("Location: unauthorized.php");
    exit();
}

// Validate input
if (!isset($_GET['due_id']) || !is_numeric($_GET['due_id'])) {
    header("Location: instructor_due_list.php?error=invalid_id");
    exit();
}

$due_id = intval($_GET['due_id']);

// Check if record exists
$check = $conn->prepare("SELECT due_id FROM instructor_dues WHERE due_id = ?");
$check->bind_param("i", $due_id);
$check->execute();
$check->store_result();

if ($check->num_rows === 0) {
    $check->close();
    header("Location: instructor_due_list.php?error=notfound");
    exit();
}
$check->close();

// Perform deletion
$stmt = $conn->prepare("DELETE FROM instructor_dues WHERE due_id = ?");
$stmt->bind_param("i", $due_id);

if ($stmt->execute()) {
    header("Location: instructor_due_list.php?deleted=1");
    exit();
} else {
    header("Location: instructor_due_list.php?error=deletion_failed");
    exit();
}
?>
