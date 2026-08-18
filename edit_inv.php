<?php
ob_start();
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['user_name'])) {
    header("Location: login.php");
    exit();
}

// Check user role
if ($_SESSION['user_role'] != 'ACCOUNTANT') {
    header("Location: unauthorized.php");
    exit();
}

include 'resources/header.php';
include 'resources/db_config.php';

// Handle form submission
if (isset($_POST['update'])) {
    $inv_id = $_POST['inv_id'];
    $quot_id = $_POST['quot_id'];
    $inv_date = isset($_POST['inv_date']) ? $_POST['inv_date'] : '';
    $cust_id = $_POST['cust_id'];
    $trainees = $_POST['trainees'];
    $cost = $_POST['cost'];
    $file_path = ''; // Initialize file path

    // Handle file upload
    $uploadDir = 'uploads/invoices/';
    if (!empty($_FILES['invoice_file']['name'])) {
        $fileType = strtolower(pathinfo($_FILES['invoice_file']['name'], PATHINFO_EXTENSION));
        $uniqueFileName = uniqid('invoice_') . '.' . $fileType;
        $uploadFile = $uploadDir . $uniqueFileName;

        $allowedExtensions = array('pdf', 'jpg', 'jpeg', 'png');
        $maxFileSize = 10 * 1024 * 1024; // 10MB

        if (!in_array($fileType, $allowedExtensions)) {
            echo "Error: Unsupported file type.";
            exit();
        } elseif ($_FILES['invoice_file']['size'] > $maxFileSize) {
            echo "Error: File size exceeds 10MB.";
            exit();
        }

        if (move_uploaded_file($_FILES['invoice_file']['tmp_name'], $uploadFile)) {
            $file_path = $uploadFile;
        } else {
            echo "Error uploading file.";
            exit();
        }
    }

    // Validate inputs
    if (empty($quot_id) || empty($inv_date) || empty($cust_id) || empty($trainees) || empty($cost)) {
        echo "All fields are required.";
        exit();
    }

    // Update invoices with prepared statement
    $update_sql = "UPDATE invoices SET quot_id = ?, inv_date = ?, cust_id = ?, trainees = ?, cost = ?";

    if (!empty($file_path)) {
        $update_sql .= ", inv_file = ?";
    }

    $update_sql .= " WHERE inv_id = ?";

    $stmt = $conn->prepare($update_sql);

    if (!empty($file_path)) {
        $stmt->bind_param('sssiisi', $quot_id, $inv_date, $cust_id, $trainees, $cost, $file_path, $inv_id);
    } else {
        $stmt->bind_param('sssiis', $quot_id, $inv_date, $cust_id, $trainees, $cost, $inv_id);
    }

    if ($stmt->execute()) {
        header("Location: read_inv.php");
        exit();
    } else {
        echo "Error updating invoice details: " . $stmt->error;
    }
}

// Fetch invoice details for editing
if (isset($_GET['edit']) && !empty($_GET['edit'])) {
    $inv_id = $_GET['edit'];
    $sql = "SELECT * FROM invoices WHERE inv_id = $inv_id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $customer_query = "SELECT cust_id, cust_name FROM customers";
        $customer_result = $conn->query($customer_query);
?>
        <div class='about' style='margin: 1rem 25% 1rem 25%;'>
            <p>
                <?php
                echo "You are logged as <b>" . $_SESSION['user_role'] . "</b>";
                echo " | User: <b>" . $_SESSION['user_name'] . "</b>";
                ?>
            </p>
        </div>

        <div class='about_title'>
            <h3>Edit Invoice</h3>
            <i class='fa-solid fa-edit fa-2x'></i>
        </div>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="inv_id" value="<?php echo $row['inv_id']; ?>">
            
            <div class="input-group">
                <label for="quot_id">Quotation Ref#:</label><br>
                <input type="text" id="quot_id" name="quot_id" value="<?php echo $row['quot_id']; ?>">
            </div>

            <div class="input-group">
                <label for="invoice_ref">Invoice Ref#:</label><br>
                <input type="text" id="invoice_ref" name="invoice_ref" value="<?php echo $row['quot_id'] . $row['inv_id']; ?>">
            </div>
            
            <div class="input-group">
                <label for="inv_date">Invoice Date:</label><br>
                <input type="date" id="inv_date" name="inv_date" value="<?php echo $row['inv_date']; ?>"><br>
            </div>
            
            <div class="input-group">
                <label for="cust_id">Customer Name:</label><br>
                <select id="cust_id" name="cust_id">
                    <?php while ($customer_row = $customer_result->fetch_assoc()) { ?>
                        <option value="<?php echo $customer_row['cust_id']; ?>" <?php if ($customer_row['cust_id'] == $row['cust_id']) echo "selected"; ?>><?php echo $customer_row['cust_name']; ?></option>
                    <?php } ?>
                </select>
            </div>

            <div class="input-group">
                <label for="trainees">Trainees:</label><br>
                <input type="text" id="trainees" name="trainees" value="<?php echo $row['trainees']; ?>"><br>
            </div>
            
            <div class="input-group">
                <label for="cost">Cost Per Trainee:</label><br>
                <input type="text" id="cost" name="cost" value="<?php echo $row['cost']; ?>"><br>
            </div>

            <div class="input-group">
                <label for="invoice_file" class="custom-file-upload">Upload Invoice</label><br>
                <input type="file" id="invoice_file" name="invoice_file"><br>
                <small>Upload PDF, JPG, JPEG, or PNG file (max 10MB)</small>
            </div>
            <div class="input-group">
                <button type="submit" name="update" class="btn">Update</button>
            </div>
        </form>
<?php
    } else {
        echo "Invoice not found.";
    }
} else {
    echo "Invalid request.";
}

include 'footer.php';

ob_end_flush();
?>
