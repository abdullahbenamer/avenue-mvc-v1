<?php
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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $count_name = trim($_POST['count_name']);

    if (!empty($count_name)) {
        $stmt = $conn->prepare("INSERT INTO countries (count_name) VALUES (?)");
        $stmt->bind_param("s", $count_name);

        if ($stmt->execute()) {
            $_SESSION['flash_message'] = "✅ Country added successfully.";
            header("Location: countries.php");
            exit();
        } else {
            $error = $stmt->error;
        }
    } else {
        $error = "Country name is required.";
    }
}
?>

<h3 style="margin: 1rem 25%;">Add New Country <i class="fa-solid fa-globe fa-2xl"></i></h3>
<?php if (isset($error)) echo "<p style='color:red; margin:1rem 25%; font-weight:bold;'>$error</p>"; ?>

<form method="POST" style="max-width: 600px; margin: 1rem 25%;">
    <div class="input-group">
        <label for="count_name">Country Name</label><br>
        <input type="text" name="count_name" id="count_name" required style="width: 100%; padding: 6px;"><br><br>
    </div>

    <button type="submit" class="btn btn-primary">Save Country</button>
</form>

<?php include 'footer.php'; ?>
