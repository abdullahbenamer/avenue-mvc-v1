<?php
include 'resources/db_config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $allowed_statuses = ['Pending', 'In_process', 'Paid', 'Cancelled'];

    $inv_id = (int)$_POST['inv_id'];
    $status = $_POST['status'];

    // Validate status
    if (!in_array($status, $allowed_statuses)) {
        die("Invalid status value");
    }

    // Update using prepared statement
    $sql = "UPDATE invoices SET status = ? WHERE inv_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('si', $status, $inv_id);

    if ($stmt->execute()) {
        header("Location: read_inv.php");
        exit();
    } else {
        echo "Error updating status: " . $conn->error;
    }
}
?>

