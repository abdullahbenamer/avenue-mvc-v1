<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
if (!isset($_SESSION['user_name'])) {
    header("Location: login.php");
    exit();
}

// Check if the user has the required role
if ($_SESSION['user_role'] != 'ADMIN' && $_SESSION['user_role'] != 'ACCOUNTANT' && $_SESSION['user_role'] != 'USER') {
    header("Location: unauthorized.php");
    exit();
}

include 'resources/db_config.php';

$search = isset($_GET['search']) ? trim($_GET['search']) : '';

$sql = "SELECT quotations.quot_id, quotations.quot_ref, quotations.ord_id, customers.cust_code, 
               quotations.trainees, quotations.duration, courses.course_title, courses.course_title_a, 
               quotations.quot_date, quotations.file_path, orders.ord_id
        FROM quotations  
        INNER JOIN orders ON quotations.ord_id = orders.ord_id
        INNER JOIN customers ON quotations.cust_id = customers.cust_id
        INNER JOIN courses ON quotations.course_id = courses.course_id";

if ($search !== '') {
    $escaped = $conn->real_escape_string($search);
    $sql .= " WHERE quotations.quot_ref LIKE '%$escaped%' 
              OR courses.course_title LIKE '%$escaped%'";
}

$sql .= " ORDER BY quotations.quot_id DESC";

// $sql .= " ORDER BY quotations.quot_date DESC";

$result = $conn->query($sql);

// Check for SQL errors or no results
if ($result === false) {
    echo "Error: " . $conn->error;
    exit();
} 

// Delete Quotation
if (isset($_GET['del'])) {
    $quot_id = $_GET['del'];

    // Check if there are related invoices
    $sql_check_invoices = "SELECT * FROM invoices WHERE quot_id=$quot_id";
    $result_check_invoices = mysqli_query($conn, $sql_check_invoices);

    // Check if there are related quotation instances
    $sql_check_instances = "SELECT * FROM quotation_instances WHERE quot_id=$quot_id";
    $result_check_instances = mysqli_query($conn, $sql_check_instances);

    if (mysqli_num_rows($result_check_invoices) > 0) {
        // If there are related invoices, display an error message
        echo "<script>alert('Cannot delete quotation as it has related invoices.');</script>";
        header('Location: read_doc.php'); // Redirect to the main page without 'del' parameter
    } elseif (mysqli_num_rows($result_check_instances) > 0) {
        // If there are related quotation instances, display an error message
        echo "<script>alert('Cannot delete quotation as it has related quotation instances.');</script>";
        header('Location: read_doc.php'); // Redirect to the main page without 'del' parameter
    } else {
        // If no related records, proceed with deletion
        $sql_delete = "DELETE FROM quotations WHERE quot_id=$quot_id";
        if (mysqli_query($conn, $sql_delete)) {
            header('Location: read_doc.php'); // Redirect to the main page after successful deletion
        } else {
            echo "Error deleting record: " . mysqli_error($conn);
            header('Location: read_doc.php'); // Redirect even if the deletion fails
        }
    }
    exit();
}
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
.quote-grid-container {
    width: 97%;
    margin: 0.5rem auto;
    display: flex;
    flex-direction: column;
    gap: 0.3rem;
    font-family: Arial, sans-serif;
}

.quote-grid-header, .quote-grid-row {
    display: grid;
    grid-template-columns: 16% 5% 7% 5% 44% 8% 5% 5% 5%;
    gap: 0.5rem;
    align-items: center;
    padding: 0.5rem 0.5rem;
    font-size: 0.8rem;
    border-bottom: 1px solid #ccc;
}

.quote-grid-header {
    font-weight: bold;
    background-color: #e0e0e0;
}

.quote-grid-row {
    background-color: #fff;
    transition: background 0.2s ease;
}

.quote-grid-row:hover {
    background-color: #E9E9E9;
}

 .arabic-title {
            font-family: 'Tajawal', sans-serif;
            direction: rtl;
            font-size: 1rem;
        }

.edit_btn {
    padding: 4px 6px;
    background: #007BFF;
    color: white;
    border-radius: 4px;
    text-decoration: none;
    font-size: 0.8rem;
}

.edit_btn:hover {
    background: #0056b3;
}
</style>
    <title>QUOTATIONS</title>
</head>
<body>
   <?php include 'resources/header.php'; ?>
    <div class="about_title">
        <p>
    <h3>List of QUOTATIONS <i class="fa-solid fa-file-contract fa-2xl"></i></h3>
    </p>
    <div>
        <a href="doc.php"><button type="submit" class="btn btn-primary">NEW QUOTATION <i class="fa-solid fa-plus fa-2x"></i></button></a>
        <br>
         <a href="create_quot_instance.php"><button type="submit" class="btn2">ENROLLMENT [TRAINEES GROUP]  <i class="fa-solid fa-users fa-2x"></i></button></a>
         <a href="view_instance.php"><button type="submit" class="btn1">List All GROUPS <i class="fa-solid fa-people-group fa-2x"></i></button></a>
              </div>
</div>

<!-- SEARCH -->
<form method="GET" action="" style="margin: 1rem 25% 1rem 25%; display: flex; gap: 0.5rem;border: none;">
    <input type="text" name="search" placeholder="Search by Quotation ID or Course Title" 
           value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>" 
           style="flex: 1; padding: 0.5rem;">
    <button type="submit" style="padding: 0.5rem;border-radius:4px;">Search for Quotation(s)</button>
    <a href="read_doc.php" style="padding: 0.5rem; background: #ccc; text-decoration: none;border-radius:4px;">Reset</a>
</form>

    
<div class="quote-grid-container">
    <div class="quote-grid-header">
        <div>QUOTATION REF#</div>
        <div>ORDER</div>
        <div>CUSTOMER</div>
        <div>DURATION <br><b>(DAYS Only)</b></div>
        <div>COURSE TITLE</div>
        <div>ISSUE DATE</div>
        <div>FILE</div>
         <div>VIEW</div>
        <div>ACTION</div>
    </div>

    <?php while ($row = mysqli_fetch_array($result)) { ?>
        <div class="quote-grid-row">
            <div><?php echo htmlspecialchars($row['quot_ref']); ?></div>
            <div><?php echo 'ORD-' . htmlspecialchars($row['ord_id']); ?></div>
            <!--<div><?php //echo htmlspecialchars($row['cust_name']); ?></div>-->
              <div><?php echo htmlspecialchars($row['cust_code']); ?></div>
            <div><?php echo htmlspecialchars($row['duration']); ?></div>
            <div>
    <span class="arabic-title"><?php echo htmlspecialchars($row['course_title_a']); ?></span><br>
    <?php echo strtoupper(htmlspecialchars($row['course_title'])); ?>
</div>
            <div><?php echo htmlspecialchars($row['quot_date']); ?></div>
            <div>
                <?php if (!empty($row['file_path'])) {
                    echo '<a href="' . htmlspecialchars($row['file_path']) . '" target="_blank">File</a>';
                } else {
                    echo 'No file';
                } ?>
                  </div>
                    <div><a href="quotations/quotation_view.php?quot_id=<?php echo $row['quot_id']; ?>" target="_blank" class="edit_btn">View</a></div>
           <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'ADMIN'): ?>
    <div>
        <a href="edit_doc.php?edit=<?php echo $row['quot_id']; ?>" class="edit_btn">Edit</a>
    </div>
<?php endif; ?>
        </div>
    <?php } ?>
</div>

<?php
include 'resources/footer.php';
?>
