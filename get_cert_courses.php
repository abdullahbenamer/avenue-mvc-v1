<?php
include 'resources/db_config.php';

$instance_id = $_GET['instance_id'];
$cust_id = $_GET['cust_id'];

// Fetch courses related to the selected instance and customer
$query = "SELECT DISTINCT c.course_id, c.course_title
          FROM quotation_participants qp
          JOIN courses c ON c.course_id = qp.course_id
          WHERE qp.instance_id = ? AND qp.cust_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $instance_id, $cust_id);
$stmt->execute();
$result = $stmt->get_result();

$courses = [];
while ($row = $result->fetch_assoc()) {
    $courses[] = $row;
}

echo json_encode($courses);
?>
