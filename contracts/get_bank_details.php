<?php
require '../resources/db_config.php';

if (isset($_GET['instructor_id'])) {
    $id = intval($_GET['instructor_id']);

    $stmt = $conn->prepare("SELECT bank_details FROM instructors WHERE inst_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($bank_details);
    $stmt->fetch();

    echo json_encode(['bank_details' => $bank_details ?: '']);
    $stmt->close();
}
?>
