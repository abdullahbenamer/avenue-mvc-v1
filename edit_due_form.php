<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['user_name'])) {
    header("Location: login.php");
    exit();
}

require_once 'resources/db_config.php';
include 'resources/header.php';

if (!isset($_SESSION['user_name']) || $_SESSION['user_role'] !== 'ACCOUNTANT') {
    header("Location: unauthorized.php");
    exit();
}

$due_id = isset($_GET['due_id']) ? intval($_GET['due_id']) : 0;

// Fetch existing due data
$stmt = $conn->prepare("
    SELECT 
        d.*,
        i.full_name AS instructor_name,
        i.inst_portrait,

        GROUP_CONCAT(
            DISTINCT CONCAT(
                c.course_title,
                ' (G',
                idi.instance_id,
                ')'
            )
            ORDER BY idi.instance_id
            SEPARATOR ', '
        ) AS courses,

        GROUP_CONCAT(
            DISTINCT cst.cust_code
            SEPARATOR ', '
        ) AS customers

    FROM instructor_dues d
    JOIN instructors i ON d.instructor_id = i.inst_id
    LEFT JOIN instructor_due_instances idi ON d.due_id = idi.due_id
    LEFT JOIN quotations q ON idi.quot_id = q.quot_id
    LEFT JOIN courses c ON idi.course_id = c.course_id
    LEFT JOIN customers cst ON q.cust_id = cst.cust_id

    WHERE d.due_id = ?
    GROUP BY d.due_id
");


$stmt->bind_param("i", $due_id);
$stmt->execute();
$due = $stmt->get_result()->fetch_assoc();

if (!$due) {
    die("Due record not found.");
}
if ($due['paid_amount'] > 0) {
    die("Cannot edit a due that has payments.");
}

// Fetch dropdown data
$courses = $conn->query("SELECT course_id, course_title FROM courses ORDER BY course_title ASC");
$customers = $conn->query("SELECT cust_id, cust_code FROM customers ORDER BY cust_code ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Due Record</title>
    <style>
    
        .form-container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            padding: 2rem;
            border-radius: 8px;
        }
        input, select {
            width: 100%;
            padding: 0.5rem;
            margin-bottom: 1rem;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
   
        button {
            padding: 0.6rem 1.2rem;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <br>
    <div class="form-container">
    <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
    <img src="search/photo_uploads/<?php echo $due['inst_portrait'] ?: 'instructor_male.jpg'; ?>" alt="Instructor Portrait"
         style="width: 70px; height: 70px; border-radius: 50%; object-fit: cover; border: 1px solid #aaa;">
    <div>
        <h3 style="margin: 0;"><?php echo strtoupper(htmlspecialchars($due['instructor_name'] ?? 'Instructor')); ?></h3>
        <p style="margin: 2px 0; font-size: 0.85rem; color: #555;">Due ID: <strong>#<?php echo $due['due_id']; ?></strong></p>
    </div>
</div>
<h3>Edit Due Record</h3>

        <form action="update_due_process.php" method="POST">
            <input type="hidden" name="due_id" value="<?php echo $due['due_id']; ?>">

            <label>Training Programs</label>
<div style="padding:8px;background:#f4f4f4;border-radius:4px;margin-bottom:1rem;">
    <?php echo htmlspecialchars($due['courses'] ?? '—'); ?>
</div>

<label>Customers</label>
<div style="padding:8px;background:#f4f4f4;border-radius:4px;margin-bottom:1rem;">
    <?php echo htmlspecialchars($due['customers'] ?? '—'); ?>
</div>

            <label>Start Date</label>
            <input type="date" name="course_date" value="<?php echo substr($due['course_date'], 0, 10); ?>" required>

            <label>Number of Trainees</label>
            <input type="number" name="num_participants" min="1" value="<?php echo $due['num_participants']; ?>" required>

            <label>Days</label>
            <input type="number" name="days" min="1" value="<?php echo $due['days']; ?>" required>

            <label>Due Amount ($)</label>
            <input type="number" step="0.01" name="due_amount" value="<?php echo $due['due_amount']; ?>" required>

            <button type="submit">Update Due</button>
        </form>
    </div>
    <?php include 'resources/footer.php'; ?>