<?php
include 'resources/db_config.php';

if (isset($_GET['instance_id']) && isset($_GET['cust_id'])) {
    $instance_id = intval($_GET['instance_id']);
    $cust_id = intval($_GET['cust_id']);
    
    $query = "SELECT course_id, course_title FROM courses WHERE instance_id = ? AND cust_id = ?";
    
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("ii", $instance_id, $cust_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $courses = [];
        while ($row = $result->fetch_assoc()) {
            $courses[] = $row;
        }
        
        header('Content-Type: application/json');
        echo json_encode($courses);
    } else {
        echo json_encode(["error" => "Failed to prepare statement."]);
    }
} else {
    echo json_encode(["error" => "Instance ID or Customer ID not provided."]);
}
?>