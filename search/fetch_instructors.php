<?php
include '../resources/db_config.php';

$sql = "SELECT full_name FROM instructors";
$result = $conn->query($sql);

$instructors = [];
while ($row = $result->fetch_assoc()) {
    $instructors[] = ["value" => $row["full_name"], "text" => $row["full_name"]];
}

echo json_encode($instructors);
$conn->close();
?>
