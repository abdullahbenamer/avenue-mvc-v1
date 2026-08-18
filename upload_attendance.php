<?php
session_start();
require_once 'resources/db_config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $instance_id = intval($_POST['instance_id']);
    $upload_dir = 'uploads/attendance/';
    $allowed_types = ['pdf', 'xlsx', 'xls', 'jpg', 'jpeg', 'png'];

    if (isset($_FILES['attendance_file']) && $_FILES['attendance_file']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['attendance_file']['tmp_name'];
        $original_name = basename($_FILES['attendance_file']['name']);
        $file_ext = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));

        if (in_array($file_ext, $allowed_types)) {
            // Fetch and delete old file if exists
            $check_sql = "SELECT attendance_sheet FROM quotation_instances WHERE instance_id = $instance_id";
            $result = $conn->query($check_sql);
            if ($result && $row = $result->fetch_assoc()) {
                $old_file = $row['attendance_sheet'];
                if ($old_file && file_exists($upload_dir . $old_file)) {
                    unlink($upload_dir . $old_file);
                }
            }

            // Save new file
            $new_file_name = "attend_" . $instance_id . "_" . time() . "." . $file_ext;
            $destination = $upload_dir . $new_file_name;
            if (!file_exists($upload_dir)) mkdir($upload_dir, 0777, true);

            if (move_uploaded_file($file_tmp, $destination)) {
                $stmt = $conn->prepare("UPDATE quotation_instances SET attendance_sheet = ? WHERE instance_id = ?");
                $stmt->bind_param("si", $new_file_name, $instance_id);
                if ($stmt->execute()) {
                    header("Location: view_instance.php?msg=upload_success");
                    exit();
                } else {
                    echo "DB update failed.";
                }
            } else {
                echo "Failed to move uploaded file.";
            }
        } else {
            echo "Invalid file type.";
        }
    } else {
        echo "File upload error.";
    }
} else {
    echo "Invalid request.";
}
?>
