<?php
include 'resources/db_config.php';

$quot_id = $_GET['quot_id'];
$cust_id = $_GET['cust_id'];
$course_id = $_GET['course_id'];
$response = [];

if ($quot_id && $cust_id && $course_id) {
    $query = "SELECT DISTINCT start_date FROM quotations WHERE quot_id = ? AND cust_id = ? AND course_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iii", $quot_id, $cust_id, $course_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $response[] = $row;
    }
    $stmt->close();
}

echo json_encode($response);
?>
