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
    $ven_id = $_GET['delete'];
    $sql = "DELETE FROM venues WHERE ven_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $ven_id);
    try {
        if ($stmt->execute()) {
            $_SESSION['flash_message'] = "✅ Venue deleted successfully.";
        } else {
            $_SESSION['flash_message'] = "❌ Can't delete venue due to dependencies.";
        }
    } catch (mysqli_sql_exception $e) {
        if (strpos($e->getMessage(), 'foreign key constraint fails') !== false) {
            $_SESSION['flash_message'] = "❌ Can't delete venue because it's linked to other records.";
        } else {
            $_SESSION['flash_message'] = "❌ Error: " . $e->getMessage();
        }
    }
    header("Location: venues.php");
    exit();
}

// Fetch venues with country and city names
$sql = "SELECT v.*, c.count_name, ci.city_name
        FROM venues v
        LEFT JOIN countries c ON v.count_id = c.count_id
        LEFT JOIN cities ci ON v.city_id = ci.city_id
        ORDER BY ven_name";
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
    <title>Venue Management</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="admin_css/styles.css">
    </head>
<body>
<?php include 'header.php'; ?>

<div class="about_title" style="margin: 1rem 25%;">
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
<div class="about_title" style="margin: 1rem 25%;">
    <h3>List of VENUES <i class="fa-solid fa-location-dot fa-2xl"></i></h3>
    <br>
    <a href="add_venue.php"
       style="padding: 10px 20px; font-weight: 500; color: white; background-color: green; border-radius: 4px; text-decoration: none;">
        <i class="fa-solid fa-plus"></i> Add New Venue <i class="fa-solid fa-location-dot"></i>
    </a>
</div>
<br>
<div class="grid-container">
    <div class="grid-cell header">ID</div>
    <div class="grid-cell header">Venue Name</div>
    <div class="grid-cell header">Address</div>
    <div class="grid-cell header">Country</div>
    <div class="grid-cell header">City</div>
    <div class="grid-cell header">Action</div>

    <?php while ($row = $result->fetch_assoc()): ?>
        <div class="grid-cell"><?= $row['ven_id']; ?></div>
        <div class="grid-cell"><?= htmlspecialchars($row['ven_name']); ?></div>
        <div class="grid-cell"><?= htmlspecialchars($row['ven_address']); ?></div>
        <div class="grid-cell"><?= htmlspecialchars($row['count_name']); ?></div>
        <div class="grid-cell"><?= htmlspecialchars($row['city_name']); ?></div>
        <div class="grid-cell">
            <a href="edit_venue.php?id=<?= $row['ven_id']; ?>" class="btn-edit">Edit</a>
            <a href="venues.php?delete=<?= $row['ven_id']; ?>" onclick="return confirm('Delete this venue?');" class="btn-delete">Delete</a>
        </div>
    <?php endwhile; ?>
</div>



<?php include 'footer.php'; ?>
