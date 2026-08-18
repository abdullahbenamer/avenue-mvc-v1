<?php
session_start();
ob_start();
require_once '../resources/db_config.php';
include 'header.php';

if (!isset($_SESSION['user_name'])) {
    header("Location: ../login.php");
    exit();
}

if ($_SESSION['user_role'] != 'ADMIN' && $_SESSION['user_role'] != 'ACCOUNTANT') {
    header("Location: ../unauthorized.php");
    exit();
}

$course_id = $_GET['id'] ?? null;
if (!$course_id) {
    echo "<p style='color:red;'>No course ID provided.</p>";
    exit();
}

// Fetch course data
$stmt = $conn->prepare("SELECT * FROM courses WHERE course_id = ?");
$stmt->bind_param("i", $course_id);
$stmt->execute();
$result = $stmt->get_result();
$course = $result->fetch_assoc();

if (!$course) {
    echo "<p style='color:red;'>Course not found.</p>";
    exit();
}

// Update logic
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $course_title = $_POST['course_title'];
    $course_title_a = $_POST['course_title_a'];
    $course_duration = $_POST['course_duration'];
    $course_uod = $_POST['course_uod'];
    $course_cat = $_POST['course_cat'];

    $stmt = $conn->prepare("UPDATE courses SET course_title=?, course_title_a=?, course_duration=?, course_uod=?, cat_id=? WHERE course_id=?");
   $stmt->bind_param("ssssii", $course_title, $course_title_a, $course_duration, $course_uod, $course_cat, $course_id);


    if ($stmt->execute()) {
        $_SESSION['flash_message'] = "✅ Course updated successfully.";
        header("Location: courses.php");
        exit();
    } else {
        echo "<p style='color:red;'>Error: " . $stmt->error . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Course</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        form.course-form {
            max-width: 600px;
            margin: 2rem auto;
            padding: 1.5rem;
            border-radius: 8px;
                  }

        .input-group {
            margin-bottom: 1.2rem;
        }

        .input-group label {
            font-weight: 600;
            display: block;
            margin-bottom: 0.4rem;
        }

        .input-group input,
        .input-group select {
            width: 100%;
            padding: 8px 10px;
            font-size: 0.95rem;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .btn-edit {
            background-color: green;
            color: white;
            padding: 8px 16px;
            font-size: 0.9rem;
            border: none;
            border-radius: 4px;
            text-decoration: none;
            cursor: pointer;
            margin-right: 8px;
        }

        .btn-edit:hover {
            background-color: #45a049;
        }

        .btn-cancel {
            background-color: #6c757d;
            color: white;
            padding: 8px 16px;
            font-size: 0.9rem;
            border: none;
            border-radius: 4px;
            text-decoration: none;
            cursor: pointer;
        }

        .btn-cancel:hover {
            background-color: #5a6268;
        }
    </style>
</head>
<body>

<h2 style="text-align:center; margin-top: 2rem;">Edit Course <i class="fa fa-book"></i></h2>

<form method="POST" class="course-form">
    <div class="input-group">
        <label for="course_title">Course Title (English):</label>
        <input type="text" name="course_title" id="course_title" value="<?= htmlspecialchars($course['course_title']) ?>" required>
    </div>

    <div class="input-group">
        <label for="course_title_a">Course Title (Arabic):</label>
        <input type="text" name="course_title_a" id="course_title_a" value="<?= htmlspecialchars($course['course_title_a']) ?>" required>
    </div>

    <div class="input-group">
        <label for="course_duration">Course Duration:</label>
        <input type="number" name="course_duration" id="course_duration" value="<?= htmlspecialchars($course['course_duration']) ?>" required>
    </div>

    <div class="input-group">
        <label for="course_uod">Unit of Duration:</label>
        <select name="course_uod" id="course_uod" required>
            <?php
            $units = ['HOURS','DAYS','WEEKS','MONTHS','NONE'];
            foreach ($units as $unit) {
                $selected = ($course['course_uod'] === $unit) ? 'selected' : '';
                echo "<option value=\"$unit\" $selected>$unit</option>";
            }
            ?>
        </select>
    </div>

    <div class="input-group">
        <label for="course_cat">Course Category:</label>
        <select name="course_cat" id="course_cat" required>
            <?php
            $cat_sql = "SELECT * FROM categories";
            $cat_result = $conn->query($cat_sql);
            while ($cat = $cat_result->fetch_assoc()) {
                $selected = ($cat['cat_id'] == $course['cat_id']) ? 'selected' : '';
                echo "<option value=\"{$cat['cat_id']}\" $selected>{$cat['cat_name']}</option>";
            }
            ?>
        </select>
    </div>

    <button type="submit" class="btn-edit"><i class="fa fa-save"></i> Update Course</button>
    <a href="courses.php" class="btn-cancel"><i class="fa fa-times"></i> Cancel</a>
</form>

<?php include 'footer.php'; ?>
<?php ob_end_flush(); ?>
