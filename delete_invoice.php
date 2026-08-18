<?php
include('db_config.php');
$inv_id = $_GET['id'];
$sql = "DELETE FROM invoices WHERE inv_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $inv_id);
if ($stmt->execute()) {
    header("Location: invoices.php");
} else {
    echo "Error deleting record: " . $stmt->error;
}
?>
