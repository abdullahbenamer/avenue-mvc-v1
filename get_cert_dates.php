<?php
include 'resources/db_config.php';

$instance_id = $_GET['instance_id'];
$cust_id = $_GET['cust_id'];
$course_id = $_GET['course_id'];

// Ensure all parameters are provided
if (!empty($instance_id) && !empty($cust_id) && !empty($course_id)) {
    // Query to fetch start dates based on the selected instance, customer, and course
    $query = "SELECT DISTINCT start_date FROM quotation_participants 
              WHERE instance_id = ? AND cust_id = ? AND course_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iii", $instance_id, $cust_id, $course_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $dates = [];
    while ($row = $result->fetch_assoc()) {
        $dates[] = $row['start_date'];
    }

    // Return JSON of start dates
    echo json_encode($dates);
} else {
    echo json_encode([]); // Return an empty array if parameters are missing
}
?>
