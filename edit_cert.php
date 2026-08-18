<?php
ob_start();
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
if (!isset($_SESSION['user_name'])) {
    header("Location: login.php");
    exit();
}

include 'resources/header.php';
include 'resources/db_config.php';

if (isset($_GET['edit'])) {
    $cert_id = $_GET['edit'];
    $query = "SELECT c.*, cust.cust_name, co.course_title, v.ven_id, v.ven_name 
              FROM certificates c
              JOIN customers cust ON c.cust_id = cust.cust_id
              JOIN courses co ON c.course_id = co.course_id
              JOIN venues v ON c.ven_id = v.ven_id  -- Added venue join
              WHERE c.cert_id=?";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        die("Error preparing statement: " . $conn->error);
    }
    $stmt->bind_param("i", $cert_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $certificate = $result->fetch_assoc();
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $cert_id = $_POST['cert_id'];
    $full_name = $_POST['full_name'];
    $cust_id = $_POST['cust_id'];
    $course_id = $_POST['course_id'];
    $cert_date = $_POST['cert_date'];
    $start_date = $_POST['start_date'];
    $ven_id = $_POST['ven_id'];  // Added venue ID

    // Handle file upload
    if (isset($_FILES['cert_file']) && $_FILES['cert_file']['error'] == 0) {
        $allowed_ext = array('pdf');
        $file_ext = pathinfo($_FILES['cert_file']['name'], PATHINFO_EXTENSION);

        if ($_FILES['cert_file']['size'] <= 5242880 && in_array($file_ext, $allowed_ext)) {
            $unique_file_name = uniqid('cert_', true) . '.' . $file_ext;
            $file_path = 'cert_uploads/' . $unique_file_name;

            if (move_uploaded_file($_FILES['cert_file']['tmp_name'], $file_path)) {
                // Update the certificate record with the file path
                $query = "UPDATE certificates SET full_name=?, cust_id=?, course_id=?, cert_date=?, start_date=?, cert_file=?, ven_id=? WHERE cert_id=?";
                $stmt = $conn->prepare($query);
                if (!$stmt) {
                    die("Error preparing statement: " . $conn->error);
                }
                $stmt->bind_param("sissssii", $full_name, $cust_id, $course_id, $cert_date, $start_date, $file_path, $ven_id, $cert_id);
            } else {
                die("Error uploading the file.");
            }
        } else {
            die("File size exceeds 5MB or invalid file type.");
        }
    } else {
        // Update without file
        $query = "UPDATE certificates SET full_name=?, cust_id=?, course_id=?, cert_date=?, start_date=?, ven_id=? WHERE cert_id=?";
        $stmt = $conn->prepare($query);
        if (!$stmt) {
            die("Error preparing statement: " . $conn->error);
        }
        $stmt->bind_param("sisssii", $full_name, $cust_id, $course_id, $cert_date, $start_date, $ven_id, $cert_id);
    }

    if ($stmt->execute()) {
        header("Location: read_cert.php");
        exit();
    } else {
        die("Error updating certificate: " . $stmt->error);
    }

    $stmt->close();
}
ob_end_flush();
?>

<div class="about_title" style="margin: 1rem 25% 1rem 25%;">
    <h3>Edit Certificate</h3>
    </div>
    <div>
    <form action="edit_cert.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="cert_id" value="<?= $certificate['cert_id'] ?>">
        
        <div class="input-group">
            <label for="full_name">Full Name</label>
            <input type="text" id="full_name" name="full_name" value="<?= $certificate['full_name'] ?>" required>
        </div>
        
        <div class="input-group">
            <label for="cust_name">Customer Name</label>
            <select id="cust_name" name="cust_id" required>
                <option value="<?= $certificate['cust_id'] ?>" selected><?= $certificate['cust_name'] ?></option>
                <?php
                // Fetch all customers for the dropdown
                $query = "SELECT cust_id, cust_name FROM customers";
                $result = $conn->query($query);
                while ($row = $result->fetch_assoc()) {
                    echo "<option value='" . $row['cust_id'] . "'>" . $row['cust_name'] . "</option>";
                }
                ?>
            </select>
        </div>

        <div class="input-group">
            <label for="course_title">Course Title</label>
            <select id="course_title" name="course_id" required>
                <option value="<?= $certificate['course_id'] ?>" selected><?= $certificate['course_title'] ?></option>
                <?php
                // Fetch all courses for the dropdown
                $query = "SELECT course_id, course_title FROM courses";
                $result = $conn->query($query);
                while ($row = $result->fetch_assoc()) {
                    echo "<option value='" . $row['course_id'] . "'>" . $row['course_title'] . "</option>";
                }
                ?>
            </select>
        </div>

        <div class="input-group">
            <label for="ven_name">Venue</label> <!-- Added venue dropdown -->
            <select id="ven_name" name="ven_id" required>
                <option value="<?= $certificate['ven_id'] ?>" selected><?= $certificate['ven_name'] ?></option>
                <?php
                // Fetch all venues for the dropdown
                $query = "SELECT ven_id, ven_name FROM venues";
                $result = $conn->query($query);
                while ($row = $result->fetch_assoc()) {
                    echo "<option value='" . $row['ven_id'] . "'>" . $row['ven_name'] . "</option>";
                }
                ?>
            </select>
        </div>

        <div class="input-group">
            <label for="cert_date">Certificate Date</label>
            <input type="date" id="cert_date" name="cert_date" value="<?= $certificate['cert_date'] ?>" required>
        </div>

        <div class="input-group">
            <label for="start_date">Start Date</label>
            <input type="date" id="start_date" name="start_date" value="<?= $certificate['start_date'] ?>" required>
        </div>

        <!--<div class="input-group">-->
        <!--    <label for="cert_file">Upload PDF (Max size: 5MB)</label>-->
        <!--    <input type="file" id="cert_file" name="cert_file" accept=".pdf">-->
        <!--</div>-->

        <button type="submit" class="btn btn-primary">Update Certificate</button>
    </form>
</div>

<?php
include 'resources/footer.php';
?>
