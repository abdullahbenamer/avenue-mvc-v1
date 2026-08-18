<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['user_name'])) {
    header("Location: login.php");
    exit();
}

// Check if the user has the required role
if ($_SESSION['user_role'] != 'ACCOUNTANT') {
    header("Location: unauthorized.php");
    exit();
}

require_once 'resources/db_config.php';
include 'resources/header.php';

$due_id = isset($_GET['due_id']) ? intval($_GET['due_id']) : 0;

$stmt = $conn->prepare("
    SELECT 
        id.due_id, id.due_amount, id.paid_amount, id.instructor_id,
        i.full_name, i.inst_portrait 
    FROM instructor_dues id
    JOIN instructors i ON id.instructor_id = i.inst_id
    WHERE id.due_id = ?
");
$stmt->bind_param("i", $due_id);
$stmt->execute();
$due = $stmt->get_result()->fetch_assoc();

if (!$due) {
    echo "Due record not found.";
    exit();
}

// Define variables for convenience
$instructor_id = $due['instructor_id'];
$instructor_name = $due['full_name'];
$portrait_file = $due['inst_portrait'] ?? 'instructor_male.jpg';
$portrait_path = file_exists("search/photo_uploads/$portrait_file") ? "search/photo_uploads/$portrait_file" : "search/photo_uploads/instructor_male.jpg";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Instructor Payment</title>
     <link rel="stylesheet" href="resources/styles.css">
    <style>
      

        h2, h3 {
            color: #cc0a0b;
        }

        .instructor-box {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 1.5rem;
        }

        .instructor-box img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #aaa;
        }

        .instructor-box span {
            font-size: 1rem;
            color: #000;
        }

        .instructor-box a {
            color: #0055aa;
            text-decoration: none;
        }

        .instructor-box a:hover {
            text-decoration: underline;
        }

        form {
            background-color: #fff;
            padding: 1rem;
            border-radius: 8px;
            border: 1px solid #ccc;
        }

        label {
            display: block;
            margin-top: 1rem;
            font-weight: bold;
        }

        input[type="number"],
        textarea {
            width: 100%;
            padding: 8px;
            margin-top: 4px;
            font-size: 1rem;
            border: 1px solid #999;
            border-radius: 6px;
        }

        input[type="submit"] {
            margin-top: 1.2rem;
            padding: 0.6rem 1.2rem;
            font-size: 1rem;
            background-color: #007BFF;
            color: #fff;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #0056b3;
        }

        .info-text {
            margin: 0.5rem 0;
        }
    </style>
</head>
<body>

<br>
    <h3>Instructor Financial Status</h3>
<br>

    <form method="POST" action="update_due_payment.php">
          <h3>Update Payment</h3>
          <br>
         <div class="instructor-box">
        <img src="<?php echo $portrait_path; ?>" alt="Instructor Portrait">
        <span>
            INSTRUCTOR:
            <b>
                <a href="search/view_single.php?id=<?php echo $instructor_id; ?>" target="_blank">
                    <?php echo strtoupper(htmlspecialchars($instructor_name)); ?>
                </a>
            </b>
        </span>
    </div>
        <input type="hidden" name="due_id" value="<?php echo $due_id; ?>">

        <p class="info-text"><b>Total Due:</b> $<?php echo number_format($due['due_amount'], 2); ?></p>
        <p class="info-text"><b>Already Paid:</b> $<?php echo number_format($due['paid_amount'], 2); ?></p>

        <label for="new_payment">New Payment Amount ($):</label>
        <input type="number" name="new_payment" min="0" step="0.01" required>

        <label for="remarks">Remarks (optional):</label>
        <textarea name="remarks" rows="3"></textarea>

        <input type="submit" value="Submit Payment">
    </form>

    <br>
    
    <?php include 'resources/footer.php'; ?>
