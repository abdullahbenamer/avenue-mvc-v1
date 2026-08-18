<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start(); // Start the session

if (!isset($_SESSION['user_name'])) {
    header("Location: login.php");
    exit();
}

include 'resources/db_config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $instructor_id = filter_input(INPUT_POST, 'instructor_id', FILTER_VALIDATE_INT);
    $instance_id = filter_input(INPUT_POST, 'instance_id', FILTER_VALIDATE_INT);
    $course_date = $_POST['course_date'] ?? null;
    $num_participants = filter_input(INPUT_POST, 'num_participants', FILTER_VALIDATE_INT);
    $num_days = filter_input(INPUT_POST, 'num_days', FILTER_VALIDATE_INT);
    $instructor_due = filter_input(INPUT_POST, 'instructor_due', FILTER_VALIDATE_FLOAT);

    if (!$instructor_id || !$instance_id || !$course_date || !$num_participants || !$num_days || !$instructor_due) {
        header("Location: instructor_dues.php?error=1");
        exit();
    }

    // Get quot_id and course_id from instance_id
    $inst_query = $conn->prepare("SELECT quot_id FROM quotation_instances WHERE instance_id = ?");
    $inst_query->bind_param("i", $instance_id);
    $inst_query->execute();
    $inst_result = $inst_query->get_result();
    $inst_data = $inst_result->fetch_assoc();
    $inst_query->close();

    if (!$inst_data) {
        header("Location: instructor_dues.php?error=1");
        exit();
    }

    $quot_id = $inst_data['quot_id'];

    $courseStmt = $conn->prepare("SELECT course_id FROM quotations WHERE quot_id = ?");
    $courseStmt->bind_param("i", $quot_id);
    $courseStmt->execute();
    $courseResult = $courseStmt->get_result();
    $courseRow = $courseResult->fetch_assoc();
    $courseStmt->close();

    if (!$courseRow) {
        die("Invalid quotation ID: $quot_id");
    }

    $course_id = $courseRow['course_id'];

    // Check for duplicates
    $checkStmt = $conn->prepare("SELECT due_id FROM instructor_dues WHERE instructor_id = ? AND quot_id = ? AND course_date = ?");
    $checkStmt->bind_param("iis", $instructor_id, $quot_id, $course_date);
    $checkStmt->execute();
    $checkStmt->store_result();
    if ($checkStmt->num_rows > 0) {
        header("Location: instructor_single_dues.php?inst_id=$instructor_id&duplicate=1");
        exit();
    }
    $checkStmt->close();

    // Insert due record
    $stmt = $conn->prepare("INSERT INTO instructor_dues 
        (instructor_id, quot_id, instance_id, course_id, course_date, num_participants, days, due_amount, paid_amount, payment_status) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, 0.00, 'unpaid')");

    $stmt->bind_param("iiiisiii", 
        $instructor_id, $quot_id, $instance_id, $course_id, 
        $course_date, $num_participants, $num_days, $instructor_due
    );

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Instructor due successfully created.";
        header("Location: instructor_due_list.php?success=1");
        exit();
    } else {
        header("Location: instructor_dues.php?error=1");
        exit();
    }
} else {
    header("Location: instructor_dues.php");
    exit();
}

?>
