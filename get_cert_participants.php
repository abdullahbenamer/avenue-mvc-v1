<?php
include 'resources/db_config.php';

$instance_id = $_GET['instance_id'];
$cust_id = $_GET['cust_id'];
$course_id = $_GET['course_id'];

// Fetch participants related to the selected course, customer, and instance
$query = "SELECT qp.part_id, qp.full_name 
          FROM quotation_participants qp
          WHERE qp.instance_id = ? AND qp.cust_id = ? AND qp.course_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("iii", $instance_id, $cust_id, $course_id);
$stmt->execute();
$result = $stmt->get_result();

$participants = [];
while ($row = $result->fetch_assoc()) {
   $participants[] = $row;
}

echo json_encode($participants);
?>
