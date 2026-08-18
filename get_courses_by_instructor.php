<?php
require 'resources/db_config.php';

$instructor_id = isset($_GET['instructor_id']) ? intval($_GET['instructor_id']) : 0;
header('Content-Type: application/json');

if ($instructor_id > 0) {
    $sql = "
        SELECT 
            qt.quot_id, 
            qt.quot_ref, 
            c.course_title, 
            qi.instance_id
        FROM quotation_instructors qins
        JOIN quotation_instances qi ON qins.instance_id = qi.instance_id
        JOIN quotations qt ON qi.quot_id = qt.quot_id
        JOIN courses c ON qt.course_id = c.course_id
        WHERE qins.instructor_id = ?
        GROUP BY qi.instance_id
        ORDER BY c.course_title
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $instructor_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $courses = [];
    while ($row = $result->fetch_assoc()) {
        $courses[] = $row;
    }

    echo json_encode($courses);
} else {
    echo json_encode([]);
}

// old code

// include 'resources/db_config.php';

// if (isset($_GET['instructor_id'])) {
//     $instructor_id = intval($_GET['instructor_id']);

//     $stmt = $conn->prepare("
//         SELECT qt.quot_id, qt.quot_ref, c.course_title, qi.instance_id
//         FROM quotation_instances qi
//         JOIN quotations qt ON qi.quot_id = qt.quot_id
//         JOIN courses c ON qt.course_id = c.course_id
//         WHERE qi.instructor_id = ?
//         ORDER BY c.course_title
//     ");
//     $stmt->bind_param("i", $instructor_id);
//     $stmt->execute();
//     $result = $stmt->get_result();

//     $courses = [];
//     while ($row = $result->fetch_assoc()) {
//         $courses[] = $row;
//     }

//     header('Content-Type: application/json');
//     echo json_encode($courses);
// }

