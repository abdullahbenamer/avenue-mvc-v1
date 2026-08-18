<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
 include 'resources/db_config.php'; // activated for testing
 header('Content-Type: application/json'); // Set content type to JSON at the beginning


$response = ['error' => 'An unexpected error occurred.'];

try {
    if (isset($_GET['quot_instance_id'])) {
        $quot_instance_id = $_GET['quot_instance_id'];

        $sql = "SELECT qi.instance_id, q.quot_id, q.course_id, qi.cust_id, q.duration, q.cost, c.cust_name, co.course_title
                FROM quotation_instances qi
                JOIN quotations q ON qi.quot_id = q.quot_id
                JOIN customers c ON qi.cust_id = c.cust_id
                JOIN courses co ON q.course_id = co.course_id
                WHERE qi.instance_id = ?";
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            throw new Exception('Prepare failed: ' . $conn->error);
        }

        $stmt->bind_param("i", $quot_instance_id);
        if (!$stmt->execute()) {
            throw new Exception('Execute failed: ' . $stmt->error);
        }

        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $response = $result->fetch_assoc(); // Fetch the data
        } else {
            $response = ['error' => 'No data found'];
        }

        $stmt->close();
    } else {
        $response = ['error' => 'Invalid request'];
    }
} catch (Exception $e) {
    $response = ['error' => $e->getMessage()];
}

echo json_encode($response);
$conn->close();
?>
