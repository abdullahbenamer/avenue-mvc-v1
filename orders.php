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

// Initialize variables
$ord_id = "";
$ord_type_id = "";
$cust_id = "";
$order_subject = "";
$details = "";
$date = date("Y-m-d");
$edit_state = false;

// Insert data into the orders table
if (isset($_POST['save'])) {
    // Retrieve form data
    $ord_type_id = $_POST['ord_type_id'];
    $cust_id = $_POST['customer'];
    $ord_subject = $_POST['ord_subject'];
    $ord_date = $_POST['ord_date'];
    $ord_details = mysqli_real_escape_string($conn, $_POST['ord_details']);
    $user_id = $_POST['user_id'];
    $ord_file = "";

    // Handle file upload if a file is selected
    if (!empty($_FILES['ord_file']['name'])) {
        $file_name = uniqid() . '_' . $_FILES['ord_file']['name'];
        $file_tmp = $_FILES['ord_file']['tmp_name'];
        $file_path = 'uploads/' . $file_name;
        move_uploaded_file($file_tmp, $file_path);
        $ord_file = $file_path;
    }

    // Insert data into the orders table
    $sql = "INSERT INTO orders (ord_type_id, cust_id, ord_subject, ord_date, ord_details, ord_file, user_id)
            VALUES ('$ord_type_id', '$cust_id', '$ord_subject', '$ord_date', '$ord_details', '$ord_file', '$user_id')";
    $result = mysqli_query($conn, $sql);
    if ($result) {
        header('location: read_orders.php');
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }
}

// Updating code
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
    $ord_id = $_POST['ord_id'];
    $ord_type_id = $_POST['ord_type_id'];
    $cust_id = $_POST['customer'];
    $ord_subject = $_POST['ord_subject'];
    $ord_date = $_POST['ord_date'];
    $ord_details = mysqli_real_escape_string($conn, $_POST['ord_details']);
    $user_id = $_POST['user_id'];
    $ord_file = "";

    // Handle file upload if a file is selected
    if (!empty($_FILES['ord_file']['name'])) {
        $file_name = uniqid() . '_' . $_FILES['ord_file']['name'];
        $file_tmp = $_FILES['ord_file']['tmp_name'];
        $file_path = 'uploads/' . $file_name;
        move_uploaded_file($file_tmp, $file_path);
        $ord_file = $file_path;
    } else {
        // Keep the existing file path if no new file is uploaded
        $ord_file = $_POST['existing_file_path'];
    }

    // Update order details in the database
    $update_sql = "UPDATE orders SET ord_type_id = '$ord_type_id', cust_id = '$cust_id', ord_subject = '$ord_subject', ord_date = '$ord_date', ord_details = '$ord_details', ord_file = '$ord_file', user_id = '$user_id' WHERE ord_id = $ord_id";
    $update_result = mysqli_query($conn, $update_sql);

    if ($update_result) {
        header("Location: read_orders.php?id=$ord_id");
        exit();
    } else {
        echo "Error updating order details: " . mysqli_error($conn);
    }
}

// Fetch customers
$customer_query = "SELECT * FROM `customers` ORDER BY `cust_name`";
$customer_result = $conn->query($customer_query);

// Fetch order types
$type_query = "SELECT * FROM `ord_types` ORDER BY `ord_type_id`";
$type_result = $conn->query($type_query);

ob_end_flush();
?>
<br>
<div class="about_title" style="margin: 1rem 25% 1rem 25%;">
    <p>
    <h3>Incoming / New Order  <i class="fa-solid fa-person-circle-question fa-2xl"></i></h3> 
 
</div>
<br>

<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" onsubmit="return validateForm()" enctype="multipart/form-data">
    <p>
    <h4>*All fields are required</h4>
    </p>
    <input type="hidden" name="ord_id" value="<?php echo $ord_id; ?>">
    <input type="hidden" name="existing_file_path" value="<?php echo $ord_file; ?>">

    <div class="input-group">
        <label>Order Type</label>
        <select name="ord_type_id" id="ord_type_id" required>
            <option value=""></option>
            <?php while ($row = mysqli_fetch_array($type_result)) { ?>
                <option value="<?php echo $row['ord_type_id']; ?>"><?php echo strtoupper($row['type_name']); ?></option>
            <?php } ?>
        </select>
    </div>

    <div class="input-group">
        <label>Customer</label>
        <select name="customer" id="customer" required>
            <option value=""></option>
            <?php while ($row = mysqli_fetch_array($customer_result)) { ?>
                <option value="<?php echo $row['cust_id']; ?>"><?php echo strtoupper($row['cust_name']); ?></option>
            <?php } ?>
        </select>
    </div>

    <div class="input-group">
        <label>Subject</label>
        <input type="text" name="ord_subject" required>
    </div>

    <div class="input-group">
        <label>Order Date</label>
        <input type="date" name="ord_date" value="<?php echo isset($ord_date) ? $ord_date : date('Y-m-d'); ?>"
        required>
    </div>
    
    <div class="input-group">
        <label for="details">Details</label>
        <textarea name="ord_details" id="ord_details" class="input-group" style="height: 150px; width: 100%;" placeholder="Write details ...."></textarea>
    </div>

    <div class="input-group">
        <label for="ord_file" class="custom-file-upload">Upload File</label>
        <input type="file" name="ord_file" id="ord_file" accept=".pdf,.jpg,.jpeg,.png">
        <p style="font-size: 12px; color:red;">Allowed files: pdf, Images: jpg, jpeg, png.</p>
    </div>

    <?php

    // Fetch users to assign to task
    $user_query = "SELECT user_id, full_name FROM users ORDER BY full_name";
    $user_result = mysqli_query($conn, $user_query);
    ?>

    <div class="input-group">
        <label>Task Assigned to:</label>
        <select name="user_id" id="user_id" required>
            <option value=""></option>
            <?php while ($row = mysqli_fetch_assoc($user_result)) { ?>
                <option value="<?php echo $row['user_id']; ?>"><?php echo strtoupper($row['full_name']); ?></option>
            <?php } ?>
        </select>
    </div>

    <div class="input-group">
        <button type="submit" name="save" class="btn">Save Order</button>
    </div>
</form>
<script>
    function validateForm() {
        var type = document.getElementById("ord_type_id").value;
        var customer = document.getElementById("customer").value;

        if (type === "") {
            alert("Please select an Order type!");
            return false;
        }

        if (customer === "") {
            alert("Please select a Customer!");
            return false;
        }

        return true;
    }
</script>
<?php include 'resources/footer.php'; ?>
