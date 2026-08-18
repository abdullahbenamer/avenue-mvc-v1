<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['user_name'])) {
    header("Location: login.php");
    exit();
}

include 'resources/db_config.php';

if (isset($_GET['due_id'])) {
    $due_id = intval($_GET['due_id']); // Make sure to use intval for security

    // Prepare the delete query
    $query = "DELETE FROM instructor_dues WHERE due_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $due_id);

    if ($stmt->execute()) {
        header("Location: instructor_due_list.php?success=1");
        exit();
    } else {
        // Log error details
        error_log("Deletion error: " . $stmt->error);
        echo "Error: " . $stmt->error; // This will show the error on the screen
    }
} else {
    // If due_id is not set, redirect back to the list with an error
    header("Location: instructor_due_list.php?error=1");
    exit();
}
?>
