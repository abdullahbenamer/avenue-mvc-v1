<?php
include 'resources/db_config.php';

$instance_id = $_GET['instance_id'];

// Fetch customer related to the selected instance
$query = "SELECT DISTINCT c.cust_id, c.cust_name
          FROM quotation_instances qi
          JOIN customers c ON c.cust_id = qi.cust_id
          WHERE qi.instance_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $instance_id);
$stmt->execute();
$result = $stmt->get_result();

$customers = [];
while ($row = $result->fetch_assoc()) {
    $customers[] = $row;
}

echo json_encode($customers);
?>
