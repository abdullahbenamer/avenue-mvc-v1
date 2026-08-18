<?php
include 'resources/db_config.php';

$quot_id = $_GET['quot_id'];
$response = [];

$query = "SELECT c.cust_id, c.cust_name 
          FROM quotations q 
          JOIN customers c ON q.cust_id = c.cust_id 
          WHERE q.quot_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $quot_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $response[] = $row;
}

echo json_encode($response);
?>
