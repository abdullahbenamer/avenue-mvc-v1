<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'resources/db_config.php';

if (isset($_GET['quot_instance_id']) && isset($_GET['cust_id']) && isset($_GET['course_id'])) {
    $quot_instance_id = $_GET['quot_instance_id'];
    $cust_id = $_GET['cust_id'];
    $course_id = $_GET['course_id'];

    // Fetch course dates related to this quotation instance, customer, and course
    $query = "SELECT DISTINCT start_date 
              FROM quotation_instances 
              WHERE instance_id = ? AND cust_id = ? AND course_id = ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iii", $quot_instance_id, $cust_id, $course_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $dates = [];
    while ($row = $result->fetch_assoc()) {
        $dates[] = $row;
    }

    // Return the dates in JSON format
    echo json_encode($dates);
}
?>
