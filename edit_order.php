<?php
ob_start();
session_start();
if (!isset($_SESSION['user_name'])) {
    header("Location: login.php");
    exit();
}

include 'resources/header.php';
include 'resources/db_config.php'; // Assuming this file contains the database connection

// Check if Order ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<div class='header'>Order ID not provided.</div>";
    exit();
}

$order_id = $_GET['id'];

// Retrieve details of the order with the provided ID
$sql = "SELECT * FROM orders WHERE ord_id = $order_id";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) == 0) {
    echo "<div class='header'>Order not found.</div>";
    exit();
}

$row = mysqli_fetch_assoc($result);

// Fetch customer names from the 'customers' table
$customer_query = "SELECT cust_id, cust_name FROM customers ORDER BY cust_name";
$customer_result = mysqli_query($conn, $customer_query);

// Fetch Employee names from the 'users' table
$user_query = "SELECT user_id, user_name FROM users ORDER BY user_name";
$user_result = mysqli_query($conn, $user_query);

// Fetch order types
$ord_type_query = "SELECT * FROM `ord_types` ORDER BY `type_name`";
$ord_type_result = $conn->query($ord_type_query);

// Handle form submission to update order details
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $ord_type_id = $_POST['ord_type_id'];
    $customer = $_POST['customer'];
    $user_id = $_POST['usr_name'];
    $subject = $_POST['subject'];
    $details = $_POST['details'];
    $date = $_POST['date'];

    // Handle file upload if a file is selected
    if (!empty($_FILES['upload']['name'])) {
        $file_name = $_FILES['upload']['name'];
        $file_tmp = $_FILES['upload']['tmp_name'];
        $file_type = $_FILES['upload']['type'];
        $file_size = $_FILES['upload']['size'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        // Validate file type and size
        $allowed_extensions = array('pdf');
        $max_file_size = 15 * 1024 * 1024; // 10MB

        if (!in_array($file_ext, $allowed_extensions) || $file_size > $max_file_size) {
            echo "Error: Unsupported file type or file size exceeds the limit 10MB.";
            exit();
        }

        $file_path = 'uploads/' . uniqid('file_') . '.' . $file_ext;

        if (!move_uploaded_file($file_tmp, $file_path)) {
            echo "Error uploading file.";
            exit();
        }
    } else {
        $file_path = $row['ord_file']; // Retain the existing file path if no file is uploaded
    }

    // Update order details in the database
    $update_sql = "UPDATE orders SET ord_type_id = '$ord_type_id', cust_id = '$customer', user_id = '$user_id', ord_subject = '$subject', ord_details = '$details', ord_date = '$date', ord_file = '$file_path' WHERE ord_id = $order_id";
    $update_result = mysqli_query($conn, $update_sql);

    if ($update_result) {
        // Redirect to details page after successful update
        header("Location: order_details.php?id=$order_id");
        exit();
    } else {
        echo "Error updating order details: " . mysqli_error($conn);
    }
}
ob_end_flush();
?>

<div class="about" style="margin: 1rem 25% 1rem 25%;">
    <p>
        <?php
        echo "You are logged as <b>";
        print_r($_SESSION['user_role']);
        echo "</b>";
        echo " | User: ";
        echo "<b>";
        print_r($_SESSION['user_name']);
        echo "</b>";
        ?>
    </p>

</div>

<div class="about_title" style="margin: 1rem 25% 1rem 25%;">
   
<p>
    <h3>Edit Order</h3>
    </p>
    <br>
    
    <p><i class="fa-solid fa-file fa-2xl"></i></p>

    
</div>


<form method="post" enctype="multipart/form-data">
<div class="input-group">
        <label for="ord_type_id">Order Type:</label>
        <select id="ord_type_id" name="ord_type_id" required>
            <?php while ($ord_type_row = mysqli_fetch_assoc($ord_type_result)) { ?>
                <option value="<?php echo $ord_type_row['ord_type_id']; ?>" <?php if ($ord_type_row['ord_type_id'] == $row['ord_type_id']) echo 'selected="selected"'; ?>><?php echo $ord_type_row['type_name']; ?></option>
            <?php } ?>
        </select>
    </div>

    <div class="input-group">
        <label for="customer">Customer:</label>
        <select id="customer" name="customer" required>
            <?php while ($customer_row = mysqli_fetch_assoc($customer_result)) { ?>
                <option value="<?php echo $customer_row['cust_id']; ?>" <?php if ($customer_row['cust_id'] == $row['cust_id']) echo 'selected="selected"'; ?>><?php echo $customer_row['cust_name']; ?></option>
            <?php } ?>
        </select>
    </div>
    <div class="input-group">
        <label for="usr_name">Assignee:</label>
        <select id="usr_name" name="usr_name" required>
            <?php while ($user_row = mysqli_fetch_assoc($user_result)) { ?>
                <option value="<?php echo $user_row['user_id']; ?>" <?php if ($user_row['user_id'] == $row['user_id']) echo 'selected="selected"'; ?>><?php echo $user_row['user_name']; ?></option>
            <?php } ?>
        </select>
    </div>
    <div class="input-group">
        <label for="subject">Subject:</label>
        <input type="text" id="subject" name="subject" value="<?php echo $row['ord_subject']; ?>" required>
    </div>
    <div class="input-group">
        <label for="date">Date:</label>
        <input type="date" id="date" name="date" value="<?php echo $row['ord_date']; ?>" required>
    </div>
    <div class="input-group">
        <label for="details">Details:</label>
        <textarea id="details" name="details" required style="height: 150px; width: 100%;"><?php echo $row['ord_details']; ?></textarea>
    </div>
    <div class="input-group">
        <label for="upload" class="custom-file-upload">Upload file</label>
        <input type="file" name="upload" id="upload" accept=".pdf">
    </div>
    <br>
    <div class="input-group">
        <button type="submit" name="update" class="btn">Update Order</button>
    </div>
</form>


<?php include 'resources/footer.php'; ?>
