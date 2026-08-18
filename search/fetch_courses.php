<?php
include '../resources/db_config.php';

$sql = "SELECT course_title FROM courses";
$result = $conn->query($sql);

$courses = [];
while ($row = $result->fetch_assoc()) {
    $courses[] = ["value" => $row["course_title"], "text" => $row["course_title"]];
}

echo json_encode($courses);
$conn->close();
?>
