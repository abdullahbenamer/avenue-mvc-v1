<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
if (!isset($_SESSION['user_name'])) {
    header("Location: login.php");
    exit();
}

include 'resources/db_config.php';

if (isset($_GET['id'])) {
    $instance_id = $_GET['id'];

    // Delete statement
    $stmt = $conn->prepare("DELETE FROM quotation_instances WHERE instance_id = ?");
    $stmt->bind_param("i", $instance_id);

    if ($stmt->execute()) {
        echo "<script>alert('Quotation Instance deleted successfully'); window.location.href='view_instances.php';</script>";
    } else {
        echo "<script>alert('Error deleting Quotation Instance'); window.location.href='view_instances.php';</script>";
    }
} else {
    echo "<script>alert('Invalid access'); window.location.href='view_instances.php';</script>";
}
?>