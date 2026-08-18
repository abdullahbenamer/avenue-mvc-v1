<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
if (!isset($_SESSION['user_name'])) {
    header("Location: login.php");
    exit();
}

// THIS FILE IS invoices.php
include 'resources/db_config.php';
include 'resources/header.php';

// Initialize variables
$inv_id = $quot_instance_id = $course_id = $cust_id = $duration = $cost = $trainees = $inv_date = $ven_id = $inv_file = $total = "";

// Fetch invoice data for editing if available
if (isset($_GET['edit'])) {
    $inv_id = $_GET['edit'];
    $sql = "SELECT * FROM invoices WHERE inv_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $inv_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $invoice = $result->fetch_assoc();

    // Populate form variables
    $quot_instance_id = $invoice['quot_instance_id'];
    $course_id = $invoice['course_id'];
    $cust_id = $invoice['cust_id'];
    $duration = $invoice['duration'];
    $cost = $invoice['cost'];
    $trainees = $invoice['trainees'];
    $inv_date = $invoice['inv_date'];
    $ven_id = $invoice['ven_id'];
    $inv_file = $invoice['inv_file'];
    $total = $invoice['total'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create/Update Invoice</title>
    <script>
        // JavaScript to fetch quotation instance data using AJAX
        function fetchQuotationInstanceData(quot_instance_id) {
            if (quot_instance_id === "") {
                return;
            }

            fetch(`get_quotation_instance_data.php?quot_instance_id=${quot_instance_id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        console.error('Error fetching data:', data.error);
                        return;
                    }
                    // Populate the form fields with data from the response
                    document.getElementById('quot_id').value = data.quot_id || '';
                    document.getElementById('cust_name').value = data.cust_name || '';
                    document.getElementById('course_title').value = data.course_title || '';
                    document.getElementById('duration').value = data.duration || '';
                    document.getElementById('cost').value = data.cost || '';
                    document.getElementById('course_id').value = data.course_id || '';
                    document.getElementById('cust_id').value = data.cust_id || '';
                })
                .catch(error => console.error('Error fetching data:', error));
        }
    </script>
    <script>
    function updateTotal() {
        const cost = parseFloat(document.getElementById('cost').value) || 0;
        const trainees = parseInt(document.getElementById('trainees').value) || 0;
        const total = cost * trainees;
        document.getElementById('total').value = total;
    }
</script>
</head>
<body>
    
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
    <h3>Create/Update Invoice <i class="fa-solid fa-file-invoice fa-2xl"></i></h3>
    </div>
    
                                      <!-- THE FORM -->
    
    <form method="POST" enctype="multipart/form-data" action="process_invoice.php">
        <input type="hidden" name="inv_id" value="<?php echo $inv_id; ?>">
        <input type="hidden" name="course_id" id="course_id" value="<?php echo $course_id; ?>" required>
        <input type="hidden" name="cust_id" id="cust_id" value="<?php echo $cust_id; ?>" required>
        <input type="hidden" name="quot_id" id="quot_id" value="<?php echo $quot_id; ?>" required>

        <!-- Quotation Instance Selection -->
         <div class="input-group">
        <label for="quot_instance_id">Quotation Reference:</label>
        <select name="quot_instance_id" id="quot_instance_id" onchange="fetchQuotationInstanceData(this.value)" required>
            <option value="">Select Group</option>
            <?php
            // Fetch instances for dropdown
            $sql = "SELECT instance_id, instance_ref FROM quotation_instances ORDER BY instance_id DESC";
            $result = $conn->query($sql);
            while ($row = $result->fetch_assoc()) {
                $selected = ($row['instance_id'] == $quot_instance_id) ? "selected" : "";
                echo "<option value='{$row['instance_id']}' $selected>{$row['instance_ref']}-({$row['instance_id']})</option>";
            }
            ?>
        </select>
        
        </div>
        
         <div class="input-group">
        <!-- Customer Name -->
        <label for="cust_name">Customer Name:</label>
        <input type="text" name="cust_name" id="cust_name" value="<?php echo $cust_id; ?>" readonly required>

   </div>
        
         <div class="input-group">
            <!-- Course Title -->
        <label for="course_title">Course Title:</label>
        <input type="text" name="course_title" id="course_title" value="<?php echo $course_id; ?>" readonly required>
   </div>
        
         <div class="input-group">
        <!-- Duration -->
        <label for="duration">Duration:</label>
        <input type="text" name="duration" id="duration" value="<?php echo $duration; ?>" required><br>
   </div>
        
        <!-- Cost -->
    <div class="input-group">
        <label for="cost">Cost:</label>
        <input type="text" name="cost" id="cost" value="<?php echo $cost; ?>" required oninput="updateTotal()">
    </div>

    <!-- Trainees -->
    <div class="input-group">
        <label for="trainees">Trainees:</label>
        <input type="text" name="trainees" id="trainees" value="<?php echo $trainees; ?>" required oninput="updateTotal()">
    </div>

    <!-- Total -->
    <div class="input-group">
        <label for="total">Total:</label>
        <input type="text" name="total" id="total" value="<?php echo $total; ?>" readonly>
    </div>

         <div class="input-group">
        <!-- Invoice Date -->
        <label for="inv_date">Invoice Date:</label>
        <input type="date" name="inv_date" value="<?php echo isset($inv_date) ? $inv_date : date('Y-m-d'); ?>" required>
   </div>
        
         <div class="input-group">
        <!-- Training Venue -->
        <label for="ven_id">Training Venue:</label>
        <select name="ven_id" id="ven_id" required>
            <option value="">Select Venue</option>
            <?php
            $sql = "SELECT ven_id, ven_name FROM venues";
            $result = $conn->query($sql);
            while ($row = $result->fetch_assoc()) {
                $selected = ($row['ven_id'] == $ven_id) ? "selected" : "";
                echo "<option value='{$row['ven_id']}' $selected>{$row['ven_name']}</option>";
            }
            ?>
        </select>
   </div>
        
         <div class="input-group">
         <!-- Invoice File Upload -->
        <label for="inv_file">Upload Invoice File:</label>
        <input type="file" name="inv_file" id="inv_file">
        <?php if ($inv_file) : ?>
            <p>Current file: <a href="<?php echo $inv_file; ?>" target="_blank">View</a></p>
        <?php endif; ?>
        </div

        <!-- Submit Button -->
        <input type="submit" class="btn btn-primary" value="Save Invoice">
    </form>

<?php 
include 'resources/footer.php'; 
?>
