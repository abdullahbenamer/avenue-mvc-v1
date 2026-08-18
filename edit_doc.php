<?php
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
if (!isset($_SESSION['user_name'])) {
    header("Location: login.php"); 
    exit();
}

include 'resources/header.php';
include 'resources/db_config.php';

// Initialize variables
$quot_id = $cust_id = $cat_id = $trainees = $duration = $cost = $course_id = $quot_date = $ven_id = "";
$file_path = $introduction = $objectives = $audiences = $outlines = "";

// Fetch reference number to edit
if (isset($_GET['edit'])) {
    $quot_id = $_GET['edit'];
    $sql = "SELECT * FROM quotations WHERE quot_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $quot_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $cust_id      = $row['cust_id'];
        $cat_id       = $row['cat_id'];
        $trainees     = $row['trainees'];
        $duration     = $row['duration'];
        $cost         = $row['cost'];
        $course_id    = $row['course_id'];
        $quot_date    = $row['quot_date'];
        $file_path    = $row['file_path'];
        $quot_ref     = $row['quot_ref'];
        $ven_id       = $row['ven_id'];
        $introduction = $row['introduction'];
        $objectives   = $row['objectives'];
        $audiences    = $row['audiences'];
        $outlines     = $row['outlines'];
    }
}

// Fetch dropdown data
$customer_result = $conn->query("SELECT * FROM customers ORDER BY cust_name");
$category_result = $conn->query("SELECT * FROM categories ORDER BY cat_name");
$course_result   = $conn->query("SELECT * FROM courses ORDER BY course_title");
$venue_result    = $conn->query("SELECT * FROM venues ORDER BY ven_name");

// Update record
if (isset($_POST['update'])) {
    $quot_id      = $_POST['quot_id'];
    $cust_id      = $_POST['cust_id'];
    $cat_id       = $_POST['cat_id'];
    $trainees     = $_POST['trainees'];
    $duration     = $_POST['duration'];
    $cost         = $_POST['cost'];
    $course_id    = $_POST['course_id'];
    $quot_date    = $_POST['quot_date'];
    $ven_id       = $_POST['ven_id'];
    $introduction = $_POST['introduction'];
    $objectives   = $_POST['objectives'];
    $audiences    = $_POST['audiences'];
    $outlines     = $_POST['outlines'];

    // File upload handling
    if ($_FILES['file']['error'] == UPLOAD_ERR_OK && $_FILES['file']['type'] == 'application/pdf') {
        $file_name = uniqid() . '_' . $_FILES['file']['name'];
        $file_tmp  = $_FILES['file']['tmp_name'];
        $file_path = 'uploads/' . $file_name;
        move_uploaded_file($file_tmp, $file_path);
    } elseif (!empty($_POST['existing_file_path'])) {
        $file_path = $_POST['existing_file_path'];
    }

    // Generate quotation ref
    $cat_code = "";
    $cat_stmt = $conn->prepare("SELECT cat_code FROM categories WHERE cat_id = ?");
    $cat_stmt->bind_param("i", $cat_id);
    $cat_stmt->execute();
    $cat_result = $cat_stmt->get_result();
    if ($cat_row = $cat_result->fetch_assoc()) {
        $cat_code = $cat_row['cat_code'];
    }

    $year = substr($quot_date, 2, 2);
    $prefix = "02";
    $result = $conn->query("SELECT MAX(quot_id) AS max_id FROM quotations");
    $row = $result->fetch_assoc();
    $serial_number = $row['max_id'] + 1;
    $quot_ref = "$year-$prefix-$cust_id-$cat_code$serial_number";

    // Build update statement
$sql = "UPDATE quotations SET 
            cust_id=?, cat_id=?, trainees=?, duration=?, cost=?, course_id=?, 
            quot_date=?, quot_ref=?, ven_id=?, 
            introduction=?, objectives=?, audiences=?, outlines=?";

if (!empty($file_path)) {
    $sql .= ", file_path=?";
}
$sql .= " WHERE quot_id=?";

$stmt = $conn->prepare($sql);

if (!empty($file_path)) {
    $stmt->bind_param(
        "iiiidississsssi", 
        $cust_id, $cat_id, $trainees, $duration, $cost, $course_id,
        $quot_date, $quot_ref, $ven_id,
        $introduction, $objectives, $audiences, $outlines,
        $file_path, $quot_id
    );
} else {
    $stmt->bind_param(
        "iiiidississssi", 
        $cust_id, $cat_id, $trainees, $duration, $cost, $course_id,
        $quot_date, $quot_ref, $ven_id,
        $introduction, $objectives, $audiences, $outlines,
        $quot_id
    );
}

if ($stmt->execute()) {
    header('Location: read_doc.php');
    exit();
} else {
    echo "Error updating record: " . $stmt->error;
}
}
ob_end_flush();
?>

<div class="about_title" style="margin: 1rem 25% 1rem 25%;">
    <h3>Edit Quotation <i class="fa-solid fa-edit fa-2xl"></i></h3>
</div>

<form method="post" action="" enctype="multipart/form-data">
    <input type="hidden" name="quot_id" value="<?php echo $quot_id; ?>">
    <input type="hidden" name="existing_file_path" value="<?php echo $file_path; ?>">

    <div class="input-group">
        <label>Customer</label>
        <select name="cust_id" required>
            <?php while ($row = $customer_result->fetch_assoc()) { ?>
                <option value="<?php echo $row['cust_id']; ?>" <?php if ($row['cust_id'] == $cust_id) echo 'selected'; ?>>
                    <?php echo strtoupper($row['cust_name']); ?>
                </option>
            <?php } ?>
        </select>
    </div>

    <div class="input-group">
        <label>Category</label>
        <select name="cat_id" required>
            <?php while ($row = $category_result->fetch_assoc()) { ?>
                <option value="<?php echo $row['cat_id']; ?>" <?php if ($row['cat_id'] == $cat_id) echo 'selected'; ?>>
                    <?php echo $row['cat_name']; ?>
                </option>
            <?php } ?>
        </select>
    </div>

    <div class="input-group">
        <label>Number of Trainees</label>
        <input type="number" name="trainees" value="<?php echo htmlspecialchars($trainees); ?>" required>
    </div>

    <div class="input-group">
        <label>Duration</label>
        <input type="text" name="duration" value="<?php echo htmlspecialchars($duration); ?>" required>
    </div>

    <div class="input-group">
        <label>Cost</label>
        <input type="number" name="cost" value="<?php echo htmlspecialchars($cost); ?>" required>
    </div>

    <div class="input-group">
        <label>Course</label>
        <select name="course_id" required>
            <?php while ($row = $course_result->fetch_assoc()) { ?>
                <option value="<?php echo $row['course_id']; ?>" <?php if ($row['course_id'] == $course_id) echo 'selected'; ?>>
                    <?php echo $row['course_title']; ?>
                </option>
            <?php } ?>
        </select>
    </div>

    <div class="input-group">
        <label>Quotation Date</label>
        <input type="date" name="quot_date" value="<?php echo $quot_date; ?>" required>
    </div>

    <div class="input-group">
        <label>Venue</label>
        <select name="ven_id" required>
            <?php while ($row = $venue_result->fetch_assoc()) { ?>
                <option value="<?php echo $row['ven_id']; ?>" <?php if ($row['ven_id'] == $ven_id) echo 'selected'; ?>>
                    <?php echo $row['ven_name']; ?>
                </option>
            <?php } ?>
        </select>
    </div>

    <div class="input-group">
        <label>Introduction:</label>
        <textarea name="introduction" rows="3" required><?php echo htmlspecialchars($introduction); ?></textarea>
    </div>

    <div class="input-group">
        <label>Objectives:</label>
        <textarea name="objectives" rows="3" required><?php echo htmlspecialchars($objectives); ?></textarea>
    </div>

    <div class="input-group">
        <label>Target Audience:</label>
        <textarea name="audiences" rows="2"><?php echo htmlspecialchars($audiences); ?></textarea>
    </div>

    <div class="input-group">
        <label>Outlines:</label>
        <textarea name="outlines" rows="4"><?php echo htmlspecialchars($outlines); ?></textarea>
    </div>

    <div class="input-group">
        <label>Upload File (PDF):</label>
        <input type="file" name="file">
    </div>

    <div class="input-group">
        <button type="submit" name="update" class="btn btn-primary">Update Quotation</button>
    </div>
</form>

<?php include 'resources/footer.php'; ?>
