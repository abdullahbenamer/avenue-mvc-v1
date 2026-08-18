<?php
include 'resources/db_config.php';

if (isset($_GET['quot_id'])) {
    $quot_id = $_GET['quot_id'];

    $sql = "SELECT q.quot_id, q.course_id, q.cust_id, q.duration, q.cost, q.trainees, q.ven_id, c.cust_name, co.course_title 
            FROM quotations q
            JOIN customers c ON q.cust_id = c.cust_id
            JOIN courses co ON q.course_id = co.course_id
            WHERE q.quot_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $quot_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $quotation = $result->fetch_assoc();

    echo json_encode($quotation);
}
?>
