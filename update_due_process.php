<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['user_name']) || $_SESSION['user_role'] !== 'ACCOUNTANT') {
    header("Location: unauthorized.php");
    exit();
}

require_once 'resources/db_config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $due_id = intval($_POST['due_id']);
    $course_id = intval($_POST['course_id']);
    $cust_id = intval($_POST['cust_id']);
    $course_date = $_POST['course_date'];
    $num_participants = intval($_POST['num_participants']);
    $days = intval($_POST['days']);
    $due_amount = floatval($_POST['due_amount']);

    // Find the correct quotation ID that matches course + customer
    $quot_stmt = $conn->prepare("SELECT quot_id FROM quotations WHERE course_id = ? AND cust_id = ? LIMIT 1");
    $quot_stmt->bind_param("ii", $course_id, $cust_id);
    $quot_stmt->execute();
    $quot_result = $quot_stmt->get_result();

    if ($quot_result->num_rows > 0) {
        $quot_id = $quot_result->fetch_assoc()['quot_id'];

        // Update the due record
        $update_stmt = $conn->prepare("UPDATE instructor_dues 
            SET quot_id = ?, course_date = ?, num_participants = ?, days = ?, due_amount = ? 
            WHERE due_id = ?");
        $update_stmt->bind_param("isiddi", $quot_id, $course_date, $num_participants, $days, $due_amount, $due_id);

        if ($update_stmt->execute()) {
            header("Location: instructor_due_list.php?updated=1");
            exit();
        } else {
            header("Location: instructor_due_list.php?error=Update failed.");
            exit();
        }

    } else {
        header("Location: instructor_due_list.php?error=No matching quotation found.");
        exit();
    }

} else {
    header("Location: instructor_due_list.php");
    exit();
}
?>
