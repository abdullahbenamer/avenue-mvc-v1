<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ob_start();
session_start();
if (!isset($_SESSION['user_name'])) {
    header("Location: login.php");
    exit();
}

include 'resources/db_config.php';

// Fetch form data
$inv_id = isset($_POST['inv_id']) ? $_POST['inv_id'] : null;
$quot_id = $_POST['quot_id'];
$quot_instance_id = $_POST['quot_instance_id'];
$course_id = $_POST['course_id'];
$cust_id = $_POST['cust_id'];
$duration = $_POST['duration'];
$cost = $_POST['cost'];
$trainees = $_POST['trainees'];
$inv_date = $_POST['inv_date'];
$ven_id = $_POST['ven_id'];
$inv_file = ""; // Handle file upload if necessary

// Calculate total on the server-side
$total = $cost * $trainees;

// File upload logic
if (isset($_FILES['inv_file']) && $_FILES['inv_file']['error'] == 0) {
    $target_dir = "uploads/invoices/";

    // Get the file extension
    $file_extension = pathinfo($_FILES["inv_file"]["name"], PATHINFO_EXTENSION);

    // Generate a unique filename
    $unique_filename = uniqid() . '_' . time() . '.' . $file_extension;

    // Define the complete path
    $inv_file = $target_dir . $unique_filename;

    // Move the file to the target directory
    if (move_uploaded_file($_FILES["inv_file"]["tmp_name"], $inv_file)) {
        // Save the relative file path to the database
        $inv_file = $target_dir . $unique_filename;
    } else {
        echo "Error uploading file.";
        exit();
    }
}



// Validate that the quot_id exists in the quotations table
$check_sql = "SELECT quot_id FROM quotations WHERE quot_id = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("i", $quot_id);
$check_stmt->execute();
$check_stmt->store_result();

if ($check_stmt->num_rows === 0) {
    echo "Error: The selected quotation does not exist.";
    exit();
}
$check_stmt->close();

// Insert or update the invoice
if ($inv_id) {
    // Update
    $sql = "UPDATE invoices SET quot_id=?, quot_instance_id=?, course_id=?, cust_id=?, duration=?, cost=?, trainees=?, inv_date=?, ven_id=?, inv_file=?, total=? WHERE inv_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiiiisiisdi", $quot_id, $quot_instance_id, $course_id, $cust_id, $duration, $cost, $trainees, $inv_date, $ven_id, $inv_file, $total, $inv_id);
} else {
    // Insert
    $sql = "INSERT INTO invoices (quot_id, quot_instance_id, course_id, cust_id, duration, cost, trainees, inv_date, ven_id, inv_file, total) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiiisiisisd", $quot_id, $quot_instance_id, $course_id, $cust_id, $duration, $cost, $trainees, $inv_date, $ven_id, $inv_file, $total);
}

if ($stmt->execute()) {
    header("Location: read_inv.php");
    exit();
} else {
    echo "Error: " . $stmt->error;
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $inv_id = $_POST['inv_id'];
    $cost = (float) $_POST['cost'];
    $trainees = (int) $_POST['trainees'];
    $total = $cost * $trainees;  // Recalculate the total

    // Other invoice fields...

    // Prepare SQL for updating/inserting the invoice
    if ($inv_id) {
        // Update existing invoice
        $sql = "UPDATE invoices SET cost = ?, trainees = ?, total = ? WHERE inv_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ddii', $cost, $trainees, $total, $inv_id);
    } else {
        // Insert new invoice
        $sql = "INSERT INTO invoices (cost, trainees, total) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ddi', $cost, $trainees, $total);
    }

    $stmt->execute();
    header('Location: read_inv.php');
}

$stmt->close();
$conn->close();
ob_end_flush();
?>
