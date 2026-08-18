<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

// Check login
if (!isset($_SESSION['user_name'])) {
    header("Location: ../login.php");
    exit();
}

require_once '../resources/db_config.php';

// Role check
if ($_SESSION['user_role'] != 'ADMIN' && $_SESSION['user_role'] != 'ACCOUNTANT') {
    header("Location: ../unauthorized.php");
    exit();
}

$user_role = $_SESSION['user_role'];

// Handle delete
if (isset($_GET['delete']) && ($user_role == 'ADMIN' || $user_role == 'ACCOUNTANT')) {
    $cust_id = $_GET['delete'];
    $sql = "DELETE FROM customers WHERE cust_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $cust_id);
    try {
        if ($stmt->execute()) {
            $_SESSION['flash_message'] = "✅ Customer deleted successfully.";
        } else {
            $_SESSION['flash_message'] = "❌ Can't delete customer due to dependencies.";
        }
    } catch (mysqli_sql_exception $e) {
        if (strpos($e->getMessage(), 'foreign key constraint fails') !== false) {
            $_SESSION['flash_message'] = "❌ Can't delete customer because it's linked to other records.";
        } else {
            $_SESSION['flash_message'] = "❌ Error: " . $e->getMessage();
        }
    }
    header("Location: customers.php");
    exit();
}

// Fetch customers
$sql = "SELECT * FROM `customers` ORDER BY `cust_name`";
$result = $conn->query($sql);
if ($result === false) {
    echo "Error: " . $conn->error;
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Management</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&family=Cairo&family=Almarai&family=Noto+Sans+Arabic&display=swap" rel="stylesheet">
   
    <style>
    .grid-container {
    display: grid;
      max-width: 98vw;
    margin: auto;
    grid-template-columns:
        30px         /* ID */
        2.2fr          /* Customer Name */
        1.1fr          /* Customer CODE */
        1.5fr        /* Contact */
         1.2fr        /* Mobile */
        2.5fr          /* Address */
        1.5fr        /* Telephone */
        2fr          /* Email */
        1.2fr;         /* Action */
    gap: 0;
    font-family: 'Tajawal', 'Cairo', 'Almarai', 'Noto Sans Arabic', 'Segoe UI', Tahoma, sans-serif;
    font-size: 14px;
    line-height: 1.5;
    color: #333;
}

    .grid-header {
    background-color: #CCC;
    font-weight: bold;
}

   .grid-header, .grid-item {
    padding: 3px;
    box-sizing: border-box;
    }

        .grid-item:nth-child(18n+1),
        .grid-item:nth-child(18n+2),
        .grid-item:nth-child(18n+3),
        .grid-item:nth-child(18n+4),
        .grid-item:nth-child(18n+5),
        .grid-item:nth-child(18n+6),
        .grid-item:nth-child(18n+7),
        .grid-item:nth-child(18n+8),
        .grid-item:nth-child(18n+9) {
            background-color: #EEE;
        }

    @media (max-width: 768px) {
    .grid-container {
        display: block;
    }

    .grid-header,
    .grid-item {
        display: block;
        width: 100%;
        text-align: left;
    }
}

   .grid-address {
    white-space: pre-line;        /* preserves line breaks without spacing issues */
    text-align: justify;          /* makes paragraph look cleaner */
    vertical-align: top;          /* aligns text to the top of the grid cell */
    display: block;               /* ensures alignment applies */
    padding-top: 6px;             /* optional fine-tune padding */
}

  .btn-edit {
    background-color: green;
    color: white;
    padding: 3px 5px;
    font-size: 0.7rem;
    border: none;
    border-radius: 3px;
    text-decoration: none;
    display: inline-block;
    margin-right: 3px;
    cursor: pointer;
}

.btn-edit:hover {
    background-color: #45a049;
}

.btn-delete {
    background-color: #d9534f;
    color: white;
    padding: 3px 5px;
    font-size: 0.7rem;
    border: none;
    border-radius: 3px;
    text-decoration: none;
    display: inline-block;
    cursor: pointer;
}

.btn-delete:hover {
    background-color: #c9302c;
}

    </style>
</head>
<body>
<?php include 'header.php'; ?>

<div class="about_title" style="margin: 1rem 25%;">
    <!-- Flash Message (appears once only) -->
    <?php
    if (isset($_SESSION['flash_message'])):
        $flash = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        ?>
        <p style="color: <?= strpos($flash, '✅') === 0 ? 'green' : 'red' ?>; font-weight: 600;">
            <?= htmlspecialchars($flash) ?>
        </p>
    <?php endif; ?>
</div>

<!-- Customer list -->
<div class="about_title" style="margin: 1rem 25%;">
    <h3>List of CUSTOMERS <i class="fa-solid fa-building fa-2xl"></i></h3>
    <div>
    <a href="add_customer.php"
       style="padding: 10px 20px; font-weight: 500; color: white; background-color: green; border-radius: 4px; text-decoration: none;">
        <i class="fa-solid fa-user-plus"></i> Add New Customer
    </a>
     <div style="display: flex; align-items: center; margin: 0.1rem;">
    <form method="post" action="export_customers.php" style="border: none; margin: 0;">
        <button type="submit" name="export_excel" style="padding: 15px 25px; font-weight: 500; color: white; background-color: dodgerblue; border: none; border-radius: 4px; text-decoration: none; margin-left: 10px;">
            <i class="fa-solid fa-file-excel"></i> Export to Excel
        </button>
    </form>
</div>
  </div>
</div>
<br>

<div class="grid-container">
    <!-- Header Row -->
    <div class="grid-header">ID</div>
    <div class="grid-header">CUSTOMER NAME</div>
    <div class="grid-header">CODE</div>
    <div class="grid-header">CONTACTS</div>
     <div class="grid-header">MOBILE</div>
    <div class="grid-header">MAIL ADDRESS</div>
    <div class="grid-header">TELEPHONE</div>
    <div class="grid-header">EMAIL</div>
    <div class="grid-header">ACTION</div>

    <!-- Data Rows -->
    <?php while ($row = $result->fetch_assoc()): ?>
        <div class="grid-item"><?= $row['cust_id']; ?></div>
        <div class="grid-item"><?= htmlspecialchars($row['cust_name']); ?></div>
        <div class="grid-item"><?= htmlspecialchars($row['cust_code']); ?></div>
        <div class="grid-item"><?= htmlspecialchars($row['cust_contact']); ?></div>
        <div class="grid-item"><?= htmlspecialchars($row['cust_mobile']); ?></div>
  <div class="grid-item grid-address"><?= htmlspecialchars($row['cust_address']); ?></div>
         <div class="grid-item">
            <?php
            $tel_list = explode(',', $row['cust_telephone']);
            foreach ($tel_list as $number) {
                echo htmlspecialchars(trim($number)) . '<br>';
            }
            ?></div>
                <div class="grid-item"><?= htmlspecialchars($row['cust_email']); ?></div>
        <div class="grid-item">
           <a href="edit_customer.php?id=<?= $row['cust_id']; ?>" class="btn-edit">Edit</a>
           <a href="customers.php?delete=<?= $row['cust_id']; ?>" onclick="return confirm('Are you sure you want to delete this customer?');" class="btn-delete">Delete</a>
    </div>
    <?php endwhile; ?>
</div>

<?php include 'footer.php'; ?>

