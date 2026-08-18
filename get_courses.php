<?php
include 'resources/db_config.php';

$quot_id = $_GET['quot_id'];
$cust_id = $_GET['cust_id'];
$response = [];

$query = "SELECT c.course_id, c.course_title 
          FROM quotations q 
          JOIN courses c ON q.course_id = c.course_id 
          WHERE q.quot_id = ? AND q.cust_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $quot_id, $cust_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $response[] = $row;
}

echo json_encode($response);
?>
