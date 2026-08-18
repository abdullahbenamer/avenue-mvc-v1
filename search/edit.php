<?php
ob_start();
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Only ADMIN or ACCOUNTANT can access
if (!isset($_SESSION['user_name']) || 
    ($_SESSION['user_role'] != 'ADMIN' && $_SESSION['user_role'] != 'ACCOUNTANT')) {
    header("Location: ../unauthorized.php");
    exit();
}

require '../resources/db_config.php';

// Get instructor ID from URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("No instructor ID provided.");
}
$instructor_id = intval($_GET['id']);

// Fetch instructor data
$stmt = $conn->prepare("SELECT * FROM instructors WHERE inst_id = ?");
$stmt->bind_param("i", $instructor_id);
$stmt->execute();
$result = $stmt->get_result();
$instructor = $result->fetch_assoc();

if (!$instructor) {
    die("Instructor not found.");
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name     = $_POST['full_name'];
    $email         = $_POST['email'];
    $mobile        = $_POST['mobile'];
    $major         = $_POST['major'];
    $interests     = $_POST['interests'];
    $keywords      = $_POST['keywords'];
    $bank_details  = $_POST['bank_details'];

    $update_sql = "UPDATE instructors 
                   SET full_name=?, email=?, mobile=?, major=?, interests=?, keywords=?, bank_details=? 
                   WHERE inst_id=?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param(
        "sssssssi",
        $full_name,
        $email,
        $mobile,
        $major,
        $interests,
        $keywords,
        $bank_details,
        $instructor_id
    );

    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>Instructor profile updated successfully.</div>";
        // Refresh instructor data
        $stmt = $conn->prepare("SELECT * FROM instructors WHERE inst_id = ?");
        $stmt->bind_param("i", $instructor_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $instructor = $result->fetch_assoc();
    } else {
        echo "<div class='alert alert-danger'>Error updating profile: " . $conn->error . "</div>";
    }
}

include('header.php');
?>

<div class="container mt-4">
    <h3>Edit Instructor Profile</h3>
    <form method="POST">

        <div class="mb-3">
            <label class="form-label">Full Name</label>
            <input type="text" name="full_name" class="form-control"
                   value="<?= htmlspecialchars($instructor['full_name']) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control"
                   value="<?= htmlspecialchars($instructor['email']) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Mobile</label>
            <input type="text" name="mobile" class="form-control"
                   value="<?= htmlspecialchars($instructor['mobile']) ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Major</label>
            <input type="text" name="major" class="form-control"
                   value="<?= htmlspecialchars($instructor['major']) ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Interests</label>
            <textarea name="interests" class="form-control" rows="4"><?= htmlspecialchars($instructor['interests']) ?></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Keywords</label>
            <textarea name="keywords" class="form-control" rows="3"><?= htmlspecialchars($instructor['keywords']) ?></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Bank Details</label>
            <textarea name="bank_details" class="form-control" rows="3"><?= htmlspecialchars($instructor['bank_details']) ?></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Update Profile</button>
    </form>
</div>

<?php include('footer.php'); ?>
