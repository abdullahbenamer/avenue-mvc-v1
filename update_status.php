<?php
// session_start();
include 'resources/db_config.php'; 

if (!isset($_POST['ord_id']) || !isset($_POST['status'])) {
    echo "Invalid request.";
    exit();
}

$ord_id = $_POST['ord_id'];
$status = $_POST['status'];

// Update the status in the database
$sql = "UPDATE orders SET status = '$status' WHERE ord_id = $ord_id";
$result = mysqli_query($conn, $sql);

if ($result) {
    echo "Status updated successfully.";
} else {
    echo "Error updating status: " . mysqli_error($conn);
}
?>
