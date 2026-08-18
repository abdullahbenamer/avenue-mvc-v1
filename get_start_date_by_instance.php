<?php
include 'resources/db_config.php';

$instance_id = intval($_GET['instance_id']);

$query = $conn->prepare("SELECT MIN(start_date) AS start_date FROM quotation_participants WHERE instance_id = ?");
$query->bind_param("i", $instance_id);
$query->execute();
$query->bind_result($start_date);
$query->fetch();
$query->close();

echo json_encode(['start_date' => $start_date]);
