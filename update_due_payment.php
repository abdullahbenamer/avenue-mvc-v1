<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Only ACCOUNTANT can update payment
if (!isset($_SESSION['user_name']) || $_SESSION['user_role'] != 'ACCOUNTANT') {
    header("Location: unauthorized.php");
    exit();
}

require_once 'resources/db_config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $due_id = intval($_POST['due_id']);
    $new_payment = floatval($_POST['new_payment']);
    $remarks = trim($_POST['remarks']);

    // Fetch current due data
    $stmt = $conn->prepare("SELECT due_amount, paid_amount FROM instructor_dues WHERE due_id = ?");
    $stmt->bind_param("i", $due_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $due = $result->fetch_assoc();

    if (!$due) {
        echo "Error: Due record not found.";
        exit();
    }

    $total_paid = $due['paid_amount'] + $new_payment;
    $status = 'unpaid';
    if ($total_paid >= $due['due_amount']) {
        $total_paid = $due['due_amount']; // Prevent overpayment
        $status = 'paid';
    } elseif ($total_paid > 0) {
        $status = 'partial';
    }

    // Update the dues record
    $update = $conn->prepare("UPDATE instructor_dues SET paid_amount = ?, payment_status = ?, remarks = ? WHERE due_id = ?");
    $update->bind_param("dssi", $total_paid, $status, $remarks, $due_id);
    if ($update->execute()) {
        header("Location: instructor_due_list.php?msg=payment_updated");
        exit();
    } else {
        echo "Failed to update payment.";
    }
} else {
    echo "Invalid request method.";
}
?>

need html elemnts here................................