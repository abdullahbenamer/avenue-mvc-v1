<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

if (!isset($_SESSION['user_name'])) {
    header("Location: ../login.php");
    exit();
}

require_once '../resources/db_config.php';

if ($_SESSION['user_role'] != 'ADMIN' && $_SESSION['user_role'] != 'ACCOUNTANT') {
    header("Location: ../unauthorized.php");
    exit();
}

$user_role = $_SESSION['user_role'];

// Handle delete
if (isset($_GET['delete']) && ($user_role == 'ADMIN' || $user_role == 'ACCOUNTANT')) {
    $count_id = $_GET['delete'];
    $sql = "DELETE FROM countries WHERE count_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $count_id);
    try {
        if ($stmt->execute()) {
            $_SESSION['flash_message'] = "✅ Country deleted successfully.";
        } else {
            $_SESSION['flash_message'] = "❌ Can't delete country due to dependencies.";
        }
    } catch (mysqli_sql_exception $e) {
        if (strpos($e->getMessage(), 'foreign key constraint fails') !== false) {
            $_SESSION['flash_message'] = "❌ Can't delete country because it's linked to other records.";
        } else {
            $_SESSION['flash_message'] = "❌ Error: " . $e->getMessage();
        }
    }
    header("Location: countries.php");
    exit();
}

// Fetch data
$sql = "SELECT * FROM countries ORDER BY count_name";
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
    <title>Country Management</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../resources/styles.css">
    <style>
        .grid-container {
            display: grid;
            grid-template-columns: 10% 70% 20%;
            gap: 1px;
            margin: 0 auto;
            max-width: 50%;
        }

        .grid-header {
            background-color: #CCC;
            padding: 5px;
            font-size: 0.85rem;
            font-weight: bold;
            text-align: center;
        }

        .grid-item {
            background-color: #fff;
            padding: 5px;
            font-size: 0.85rem;
            text-align: left;
        }

     
.row-even {
    background-color: #EEE;
}

.row-odd {
    background-color: #FFF;
}

        @media (max-width: 768px) {
            .grid-container {
                grid-template-columns: 1fr;
            }
            .grid-header,
            .grid-item {
                text-align: center;
            }
        }
        
         .btn-add {
            background-color: green;
            color: white;
            padding: 10px 20px;
            font-size: 0.9rem;
            border: none;
            border-radius: 4px;
            text-decoration: none;
            margin-right: 4px;
        }

        .btn-add:hover {
            background-color: #45a049;
            
        }

        .btn-edit {
            background-color: green;
            color: white;
            padding: 4px 7px;
            font-size: 0.75rem;
            border: none;
            border-radius: 3px;
            text-decoration: none;
            margin-right: 4px;
        }

        .btn-edit:hover {
            background-color: #45a049;
        }

        .btn-delete {
            background-color: #d9534f;
            color: white;
            padding: 4px 7px;
            font-size: 0.75rem;
            border: none;
            border-radius: 3px;
            text-decoration: none;
        }

        .btn-delete:hover {
            background-color: #c9302c;
        }
    </style>
</head>
<body>
<?php include 'header.php'; ?>

<div class="about_title" style="margin: 1rem 25%;">
    <?php if (isset($_SESSION['flash_message'])): ?>
        <p style="color: <?= strpos($_SESSION['flash_message'], '✅') === 0 ? 'green' : 'red' ?>; font-weight: 600;">
            <?= htmlspecialchars($_SESSION['flash_message']); unset($_SESSION['flash_message']); ?>
        </p>
    <?php endif; ?>
</div>

<div class="about_title" style="margin: 1rem 25%;">
    <h3>List of COUNTRIES <i class="fa-solid fa-globe"></i></h3>
    <br>
    <a href="add_country.php" class="btn-add">
        <i class="fa-solid fa-plus"></i> Add New Country
    </a>
</div>
<br>

<div class="grid-container">
    <div class="grid-header">ID</div>
    <div class="grid-header">Country Name</div>
    <div class="grid-header">Action</div>

  <?php
$row_num = 0;
while ($row = $result->fetch_assoc()):
    $row_class = ($row_num % 2 == 0) ? 'row-even' : 'row-odd';
?>
    <div class="grid-item <?= $row_class ?>"><?= $row['count_id']; ?></div>
    <div class="grid-item <?= $row_class ?>"><?= htmlspecialchars($row['count_name']); ?></div>
    <div class="grid-item <?= $row_class ?>">
        <a href="edit_country.php?id=<?= $row['count_id']; ?>" class="btn-edit">Edit</a>
        <a href="countries.php?delete=<?= $row['count_id']; ?>" onclick="return confirm('Delete this country?');" class="btn-delete">Delete</a>
    </div>
<?php
$row_num++;
endwhile;
?>

</div>

<?php include 'footer.php'; ?>