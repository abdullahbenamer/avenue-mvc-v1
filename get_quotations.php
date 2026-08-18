<?php
include 'resources/db_config.php';

$response = [];

$query = "SELECT quot_id, quot_ref FROM quotations";
$result = $conn->query($query);

while ($row = $result->fetch_assoc()) {
    $response[] = $row;
}

echo json_encode($response);
?>
