<?php
include 'resources/db_config.php';

$ord_id = $_POST['ord_id'] ?? '';

if (!empty($ord_id)) {
    $sql = "SELECT customers.cust_name 
            FROM customers 
            INNER JOIN orders ON customers.cust_id = orders.cust_id 
            WHERE orders.ord_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $ord_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $customer = $result->fetch_assoc();
        echo json_encode(['cust_name' => $customer['cust_name']]);
    } else {
        echo json_encode(['error' => 'Customer not found']);
    }
} else {
    echo json_encode(['error' => 'Order ID not provided']);
}
?>
