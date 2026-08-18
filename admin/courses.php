<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

if (!isset($_SESSION['user_name'])) {
    header("Location: ../login.php");
    exit();
}
// courses.php
require_once '../resources/db_config.php';

if ($_SESSION['user_role'] != 'ADMIN' && $_SESSION['user_role'] != 'ACCOUNTANT') {
    header("Location: ../unauthorized.php");
    exit();
}

$user_role = $_SESSION['user_role'];

if (isset($_GET['delete']) && ($user_role == 'ADMIN' || $user_role == 'ACCOUNTANT')) {
    $course_id = $_GET['delete'];
    $sql = "DELETE FROM courses WHERE course_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $course_id);
//   $stmt->bind_param("ssssis", $course_title, $course_title_a, $course_duration, $course_uod, $course_cat, $course_id);
    try {
        if ($stmt->execute()) {
            $_SESSION['flash_message'] = "✅ Course deleted successfully.";
        } else {
            $_SESSION['flash_message'] = "❌ Can't delete course due to dependencies.";
        }
    } catch (mysqli_sql_exception $e) {
        $_SESSION['flash_message'] = "❌ Error: " . $e->getMessage();
    }
    header("Location: courses.php");
    exit();
}

$sql = "SELECT c.*, cat.cat_name 
FROM courses c
JOIN categories cat
WHERE c.cat_id = cat.cat_id
ORDER BY course_title";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../resources/styles.css">
    <title>Courses</title>
    <style>
        .grid-container {
            display: grid;
            grid-template-columns: 3% 40% 30% 3% 4% 10% 7%;
            gap: 1px;
            max-width: 98%;
            margin: 0 auto;
        }
        .grid-header, .grid-item {
            padding: 3px;
            font-size: 0.8rem;
        }
        .grid-header {
            background-color: #BBB;
            font-weight: bold;
            text-align: center;
        }
        
                .grid-item {
            background-color: #fff;
        }
        
        /* Zebra striping for every odd row */
.grid-item:nth-child(14n+8),
.grid-item:nth-child(14n+9),
.grid-item:nth-child(14n+10),
.grid-item:nth-child(14n+11),
.grid-item:nth-child(14n+12),
.grid-item:nth-child(14n+13),
.grid-item:nth-child(14n+14) {
    background-color: #DDD; /* light gray background */
}

        
        .btn-edit, .btn-delete {
            font-size: 0.7rem;
            padding: 3px 5px;
            border-radius: 3px;
            text-decoration: none;
            color: white;
            margin-right: 4px;
        }
        .btn-edit { background-color: green; }
        .btn-edit:hover { background-color: #45a049; }
        .btn-delete { background-color: #d9534f; }
        .btn-delete:hover { background-color: #c9302c; }
        
        .arabic-title {
    font-family: 'Tajawal', sans-serif;
    font-size: 0.9rem;
    direction: rtl; /* Optional: use if Arabic text is right-to-left */
}
    </style>
</head>
<body>
<?php include 'header.php'; ?>

<div style="margin: 1rem 25%;">
    <?php if (isset($_SESSION['flash_message'])): ?>
        <p style="color: <?= strpos($_SESSION['flash_message'], '✅') === 0 ? 'green' : 'red' ?>; font-weight: 600;">
            <?= htmlspecialchars($_SESSION['flash_message']); unset($_SESSION['flash_message']); ?>
        </p>
    <?php endif; ?>
</div>

<div style="margin: 1rem 25%;">
    <h3>List of COURSES <i class="fa-solid fa-book fa-2xl"></i></h3>
    <a href="add_course.php" style="padding: 10px 20px; font-weight: 500; color: white; background-color: green; border-radius: 4px; text-decoration: none;">
        <i class="fa-solid fa-plus"></i> Add New Course  <i class="fa-solid fa-book"></i></a>
    <!-- export to excel -->
    <br>
    <div style="margin-top: 30px;">
    <a href="export_courses_excel.php" 
   style="padding: 10px 20px; font-weight: 500; color: white; background-color: dodgerblue; border-radius: 4px; text-decoration: none; margin-left: 10px;">
   <i class="fa-solid fa-file-excel"></i> Export to Excel
</a>
</div>

</div>
<br>

<div class="grid-container">
    <div class="grid-header">ID</div>
    <div class="grid-header">Title</div>
    <div class="grid-header">Arabic Title</div>
    <div class="grid-header">Dur.</div>
    <div class="grid-header">Unit</div>
    <div class="grid-header">Category</div>
    <div class="grid-header">Action</div>

    <?php while ($row = $result->fetch_assoc()): ?>
        <div class="grid-item"><?= $row['course_id']; ?></div>
        <div class="grid-item"><?= htmlspecialchars($row['course_title']); ?></div>
        <div class="grid-item arabic-title"><?= htmlspecialchars($row['course_title_a']); ?></div>
        <div class="grid-item"><?= htmlspecialchars($row['course_duration']); ?></div>
        <div class="grid-item"><?= htmlspecialchars($row['course_uod']); ?></div>
        <div class="grid-item"><?= htmlspecialchars($row['cat_name']); ?></div>
        <div class="grid-item">
            <a href="edit_course.php?id=<?= $row['course_id']; ?>" class="btn-edit">Edit</a>
            <a href="courses.php?delete=<?= $row['course_id']; ?>" onclick="return confirm('Delete this course?');" class="btn-delete">Delete</a>
        </div>
    <?php endwhile; ?>
</div>

<?php include 'footer.php'; ?>
</body>
</html>
