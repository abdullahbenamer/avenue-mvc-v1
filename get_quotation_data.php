<?php
include 'resources/db_config.php';

// Check if quot_id is set
if (isset($_GET['quot_id'])) {
    $quot_id = $_GET['quot_id'];

    // Prepare and execute the SQL query
    $sql = "SELECT q.course_id, q.cust_id, q.duration, q.cost, q.trainees, c.cust_name, co.course_title
            FROM quotations q
            JOIN customers c ON q.cust_id = c.cust_id
            JOIN courses co ON q.course_id = co.course_id
            WHERE q.quot_id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $quot_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Fetch the data as an associative array
    if ($data = $result->fetch_assoc()) {
        // Return data as JSON
        echo json_encode($data);
    } else {
        // Return an empty JSON object if no data found
        echo json_encode([]);
    }
} else {
    // Return an empty JSON object if quot_id is not provided
    echo json_encode([]);
}
?>
