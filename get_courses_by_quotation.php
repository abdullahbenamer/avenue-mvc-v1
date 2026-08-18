<?php
include 'resources/db_config.php';

header('Content-Type: application/json'); // Set the response content type to JSON

if (isset($_GET['quot_id'])) {
    $quot_id = intval($_GET['quot_id']);
    
    // Prepare the query to fetch duration and course information
    $query = "SELECT q.duration, c.course_id, c.course_title 
              FROM quotations q 
              JOIN courses c ON q.course_id = c.course_id 
              WHERE q.quot_id = ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $quot_id);
    $stmt->execute();
    
    $result = $stmt->get_result();
    $courses = '';
    $duration = '';

    // Fetch the data and create the course options and duration
    if ($row = $result->fetch_assoc()) {
        $courses .= "<option value='" . $row['course_id'] . "'>" . $row['course_title'] . "</option>";
        $duration = $row['duration'];
    }

    // Output as JSON
    echo json_encode(['courses' => $courses, 'duration' => $duration]);
} else {
    echo json_encode(['error' => 'Invalid quotation ID']);
}

exit();
