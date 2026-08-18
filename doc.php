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

// Fetch dropdown data
$sql_orders = "SELECT ord_id, cust_id, ord_subject FROM orders ORDER BY ord_date DESC";
$result_orders = $conn->query($sql_orders);

$sql_courses = "
    SELECT 
        c.course_id,
        c.course_title,
        c.course_title_a,
        c.course_duration,
        c.course_uod,
        c.week,
        c.cat_id,
        cat.cat_name,
        cat.cat_code
    FROM courses c
    JOIN categories cat ON c.cat_id = cat.cat_id
    ORDER BY c.course_title
";

$result_courses = $conn->query($sql_courses);

$sql_categories = "SELECT cat_id, cat_name, cat_code FROM categories";
$result_categories = $conn->query($sql_categories);

$sql_venues = "SELECT ven_id, ven_name FROM venues ORDER BY ven_name";
$result_venues = $conn->query($sql_venues);

// Handle form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ord_id       = intval($_POST['ord_id']);
    $course_id    = intval($_POST['course_id']);
    $trainees     = intval($_POST['trainees']);
    $duration     = trim($_POST['duration']);
    $quot_date    = $_POST['quot_date'];
    $cat_id       = intval($_POST['cat_id']);
    $cost         = intval($_POST['cost']); // ensure integral number
    $ven_id       = intval($_POST['ven_id']);
    $introduction = trim($_POST['introduction']);
    $objectives   = trim($_POST['objectives']);
    $audiences    = trim($_POST['audiences']);
    $outlines     = trim($_POST['outlines']);

    // Fetch customer ID
    $sql_customer = "SELECT cust_id FROM orders WHERE ord_id = ?";
    $stmt = $conn->prepare($sql_customer);
    $stmt->bind_param("i", $ord_id);
    $stmt->execute();
    $result_customer = $stmt->get_result();
    $customer = $result_customer->fetch_assoc();
    $cust_id = $customer['cust_id'] ?? 0;

    // Fetch category code
    $sql_category_code = "SELECT cat_code FROM categories WHERE cat_id = ?";
    $stmt_category = $conn->prepare($sql_category_code);
    $stmt_category->bind_param("i", $cat_id);
    $stmt_category->execute();
    $result_category = $stmt_category->get_result();
    $category = $result_category->fetch_assoc();
    $cat_code = $category['cat_code'] ?? '';

    // Generate reference number
    $year = substr($quot_date, 2, 2);
    $prefix = "02";
    $serial_number_query = "SELECT MAX(quot_id) as max_id FROM quotations";
    $serial_result = $conn->query($serial_number_query);
    $serial_row = $serial_result->fetch_assoc();
    $serial_number = intval($serial_row['max_id']) + 1;

    $quot_ref = $year . '-' . $prefix . '-' . $cust_id . '-' . $cat_code . $serial_number;

  $temp_ref = "TEMP";  // Insert first with a temporary quot_ref (placeholder)

$stmt = $conn->prepare("INSERT INTO quotations 
    (quot_ref, ord_id, cust_id, cat_id, trainees, duration, cost, course_id, quot_date, ven_id, introduction, objectives, audiences, outlines) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

$stmt->bind_param(
    'siiiisdisissss',
    $temp_ref,
    $ord_id,
    $cust_id,
    $cat_id,
    $trainees,
    $duration,
    $cost,
    $course_id,
    $quot_date,
    $ven_id,
    $introduction,
    $objectives,
    $audiences,
    $outlines
);

if ($stmt->execute()) {
    $last_id = $stmt->insert_id; // this is the real quot_id

    // Now generate quot_ref using the REAL quot_id
    $year = substr($quot_date, 2, 2);
    $prefix = "02";
    $quot_ref = $year . '-' . $prefix . '-' . $cust_id . '-' . $cat_code . $last_id;

    // Update the row with the final reference
    $update = $conn->prepare("UPDATE quotations SET quot_ref = ? WHERE quot_id = ?");
    $update->bind_param('si', $quot_ref, $last_id);
    $update->execute();

    header("Location: quotations/quotation_view.php?quot_id=$last_id");
    exit();
} else {
        echo "Error: " . $conn->error;
    }
}

ob_end_flush();
?>

<div class="about_title" style="margin: 1rem 25% 1rem 25%;">
    <h3>Create a New Quotation <i class="fa-solid fa-file-contract fa-2xl"></i></h3>
</div>

<form method="POST">
    <div class="input-group">
        <label for="ord_id">Order:</label>
        <select name="ord_id" id="ord_id" class="form-control" required>
            <option value="">Select an Order</option>
            <?php while ($row = $result_orders->fetch_assoc()) { ?>
                <option value="<?= $row['ord_id'] ?>">
                    <?= $row['ord_id'] . " - " . htmlspecialchars($row['ord_subject']) ?>
                </option>
            <?php } ?>
        </select>
    </div>

    <div class="input-group">
        <label for="cust_name">Customer:</label>
        <input type="text" name="cust_name" id="cust_name" class="form-control" readonly>
    </div>

    <div class="input-group">
        <label for="course_id">Course Title:</label>
        <select name="course_id" id="course_id" class="form-control" required>
    <option value="">Select a Course</option>
    <?php while ($row = $result_courses->fetch_assoc()) { ?>
        <option 
            value="<?= $row['course_id'] ?>"
            data-duration="<?= $row['course_duration'] ?>"
            data-cat-id="<?= $row['cat_id'] ?>"
            data-cat-code="<?= htmlspecialchars($row['cat_code']) ?>"
        >
            <?= htmlspecialchars($row['course_title']) ?> - 
            <?= htmlspecialchars($row['course_title_a']) ?>
        </option>
    <?php } ?>
</select>

<!--hidden input for category-->
<input type="hidden" name="cat_id" id="cat_id">
    </div>

      <div class="input-group">
        <label for="trainees">Number of Trainees:</label>
        <input type="number" name="trainees" id="trainees" class="form-control" style="width:150px;">
    </div>

    <div class="input-group">
        <label for="duration">Duration:</label><div style="color:red; font-size:0.8rem">[In Days]</div>
        <input type="number" name="duration" id="duration" class="form-control" style="width:150px;" readonly required>
    </div>

    <div class="input-group">
        <label for="quot_date">Quotation Date:</label>
        <input type="date" name="quot_date" id="quot_date" value="<?= date('Y-m-d') ?>" required>
    </div>

    <div class="input-group">
        <label for="cost">Cost:</label>
        <input type="number" name="cost" id="cost" class="form-control" min=50 step="1" style="width:150px;>
    </div>

    <div class="input-group">
        <label for="ven_id">Venue:</label>
        <select name="ven_id" id="ven_id" class="form-control" required>
            <option value="">Select a Venue</option>
            <?php while ($row = $result_venues->fetch_assoc()) { ?>
                <option value="<?= $row['ven_id'] ?>"><?= htmlspecialchars($row['ven_name']) ?></option>
            <?php } ?>
        </select>
    </div>

    <!-- Quotation Entries -->
    <div class="input-group">
        <label for="introduction">Course Introduction:</label>
        <textarea name="introduction" id="introduction" class="form-control" style="width:600px; height:200px;" required></textarea>
    </div>

    <div class="input-group">
        <label for="objectives">Objectives:</label>
        <textarea name="objectives" id="objectives" class="form-control" style="width:600px; height:200px;" required></textarea>
    </div>

    <div class="input-group">
        <label for="audiences">Target Audience:</label>
        <textarea name="audiences" id="audiences" class="form-control" style="width:400px; height:200px;"></textarea>
    </div>

    <div class="input-group">
        <label for="outlines">Course Outlines:</label>
        <textarea name="outlines" id="outlines" class="form-control" style="width:600px; height:1000px;"></textarea>
    </div>

    <button type="submit" class="btn btn-primary">Save Quotation</button>
</form>

<script>
    $(document).ready(function(){
        $('#ord_id').change(function(){
            var ord_id = $(this).val();
            if(ord_id !== '') {
                $.ajax({
                    type: 'POST',
                    url: 'fetch_customer.php',
                    data: { ord_id: ord_id },
                    success: function(response){
                        var data = JSON.parse(response);
                        if (data.error) {
                            alert(data.error);
                        } else {
                            $('#cust_name').val(data.cust_name);
                        }
                    }
                });
            } else {
                $('#cust_name').val('');
            }
        });
    });

    // Auto-fill the course duration
    document.getElementById('course_id').addEventListener('change', function () {
        var selected = this.options[this.selectedIndex];
        var duration = selected.getAttribute('data-duration');
        if (duration) {
            document.getElementById('duration').value = duration;
        }
    });
</script>
<script>
document.getElementById('course_id').addEventListener('change', function () {
    var selected = this.options[this.selectedIndex];
    var catId = selected.getAttribute('data-cat-id');

    if (catId) {
        document.getElementById('cat_id').value = catId;
    }
});
</script>

<?php include 'resources/footer.php'; ?>
