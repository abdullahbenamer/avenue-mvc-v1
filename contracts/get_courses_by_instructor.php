<?php 
include('../resources/db_config.php');

if (isset($_GET['instructor_id'])) {
    $instructorId = intval($_GET['instructor_id']);

    $query = "
        SELECT DISTINCT c.course_id, c.course_title
        FROM quotation_instructors qi
        INNER JOIN quotation_instances qinst ON qi.instance_id = qinst.instance_id
        INNER JOIN quotations q ON qinst.quot_id = q.quot_id
        INNER JOIN courses c ON q.course_id = c.course_id
        WHERE qi.instructor_id = ?
        ORDER BY c.course_title
    ";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $instructorId);
    $stmt->execute();
    $result = $stmt->get_result();

    $courses = [];
    while ($row = $result->fetch_assoc()) {
        $courses[] = $row;
    }

    header('Content-Type: application/json');
    echo json_encode($courses);
}
?>
