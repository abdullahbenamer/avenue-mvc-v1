<?php
ob_start(); // Start output buffering
session_start();
require_once '../resources/db_config.php';
include 'header.php';

if (!isset($_SESSION['user_name'])) {
    header("Location: ../login.php");
    exit();
}

if ($_SESSION['user_role'] != 'ADMIN' && $_SESSION['user_role'] != 'ACCOUNTANT') {
    header("Location: ../unauthorized.php");
    exit();
}

$count_id = $_GET['id'] ?? null;

if (!$count_id) {
    echo "<p style='color:red;'>Invalid country ID.</p>";
    exit();
}

// Fetch current country
$stmt = $conn->prepare("SELECT * FROM countries WHERE count_id = ?");
$stmt->bind_param("i", $count_id);
$stmt->execute();
$result = $stmt->get_result();
$country = $result->fetch_assoc();

if (!$country) {
    echo "<p style='color:red;'>Country not found.</p>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_name = trim($_POST['count_name']);

    if (!empty($new_name)) {
        $stmt = $conn->prepare("UPDATE countries SET count_name = ? WHERE count_id = ?");
        $stmt->bind_param("si", $new_name, $count_id);

        if ($stmt->execute()) {
            $_SESSION['flash_message'] = "✅ Country updated successfully.";
            header("Location: countries.php");
            exit();
        } else {
            $error = $stmt->error;
        }
    } else {
        $error = "Country name cannot be empty.";
    }
}
?>

<h3 style="margin: 1rem 25%;">Edit Country <i class="fa-solid fa-globe fa-2xl"></i></h3>
<?php if (isset($error)) echo "<p style='color:red; margin:1rem 25%; font-weight:bold;'>$error</p>"; ?>

<form method="POST" style="max-width: 600px; margin: 1rem 25%;">
    <div class="input-group">
        <label for="count_name">Country Name</label><br>
        <input type="text" name="count_name" id="count_name" value="<?= htmlspecialchars($country['count_name']) ?>" required style="width: 100%; padding: 6px;"><br><br>
    </div>

    <button type="submit" class="btn btn-primary"> Update Country</button>
</form>

<?php include 'footer.php'; ?>
<?php ob_end_flush(); ?>
