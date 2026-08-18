<?php
include 'resources/db_config.php';

if (isset($_GET['instance_id'])) {
    $instance_id = intval($_GET['instance_id']); // Ensure it's an integer

    // Adjusted query: Join 'quotation_instances' with 'quotation_participants' and 'customers' to fetch related customers
    $query = "
        SELECT c.cust_id, c.cust_name 
        FROM quotation_participants qp
        JOIN customers c ON qp.cust_id = c.cust_id
        WHERE qp.instance_id = ?";

    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("i", $instance_id); // Bind the parameter
        if ($stmt->execute()) { // Execute the statement
            $result = $stmt->get_result();
            $customers = [];
            while ($row = $result->fetch_assoc()) {
                $customers[] = $row; // Fetch all customers
            }
            header('Content-Type: application/json');
            echo json_encode($customers); // Return JSON response
        } else {
            echo json_encode(["error" => "Query execution failed: " . $stmt->error]); // Return error if execution fails
        }
        $stmt->close(); // Close the statement
    } else {
        echo json_encode(["error" => "Failed to prepare statement: " . $conn->error]); // Return error if preparation fails
    }
} else {
    echo json_encode(["error" => "Instance ID not provided."]); // Return error if instance_id is not set
}
?>
