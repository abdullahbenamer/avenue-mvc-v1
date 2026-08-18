<?php
session_start();
require_once '../resources/db_config.php';

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=courses_export.xls");
header("Pragma: no-cache");
header("Expires: 0");

echo "<table border='1'>";
echo "<tr>
        <th>ID</th>
        <th>Title</th>
        <th>Arabic Title</th>
        <th>Duration</th>
        <th>Unit</th>
        <th>Category ID</th>
      </tr>";

$sql = "SELECT c.*, cat_name 
FROM courses c 
JOIN categories cat 
WHERE c.cat_id = cat.cat_id
ORDER BY course_title";
$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>{$row['course_id']}</td>";
    echo "<td>" . htmlspecialchars($row['course_title']) . "</td>";
    echo "<td>" . htmlspecialchars($row['course_title_a']) . "</td>";
    echo "<td>{$row['course_duration']}</td>";
    echo "<td>{$row['course_uod']}</td>";
    echo "<td>{$row['cat_name']}</td>";
    echo "</tr>";
}

echo "</table>";
exit;
?>
