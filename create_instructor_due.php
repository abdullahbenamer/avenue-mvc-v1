<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();

if (!isset($_SESSION['user_name'])) {
    header("Location: login.php");
    exit();
}

include 'resources/db_config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: instructor_dues.php");
    exit();
}

/* -------------------- INPUTS -------------------- */
$instance_ids = $_POST['instance_id']; // ARRAY
$instructor_id = intval($_POST['instructor_id']);
$course_date = $_POST['course_date'];
$num_participants = intval($_POST['num_participants']);
$days = intval($_POST['num_days']);
$due_amount = floatval($_POST['instructor_due']);

$conn->begin_transaction();

try {

    // 1️⃣ Create ONE due
    $stmt = $conn->prepare("
        INSERT INTO instructor_dues
        (instructor_id, course_date, num_participants, days, due_amount)
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("isidd", $instructor_id, $course_date, $num_participants, $days, $due_amount);
    $stmt->execute();

    $due_id = $conn->insert_id;

    // 2️⃣ Insert MULTIPLE program rows
    $stmt = $conn->prepare("
        INSERT INTO instructor_due_instances
        (due_id, instance_id, quot_id, course_id)
        SELECT ?, ti.instance_id, q.quot_id, q.course_id
        FROM training_instances ti
        JOIN quotations q ON q.instance_id = ti.instance_id
        WHERE ti.instance_id = ?
    ");

    foreach ($instance_ids as $instance_id) {
        $stmt->bind_param("ii", $due_id, $instance_id);
        $stmt->execute();
    }

    $conn->commit();

    $_SESSION['flash_success'] = "Instructor due created successfully.";
    header("Location: instructor_due_list.php");
    exit();

} catch (Exception $e) {
    $conn->rollback();
    die("Error saving due: " . $e->getMessage());
}

