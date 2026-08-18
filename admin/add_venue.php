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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $conn->prepare("INSERT INTO venues (ven_name, ven_address, count_id, city_id) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssii", $_POST['ven_name'], $_POST['ven_address'], $_POST['count_id'], $_POST['city_id']);

    if ($stmt->execute()) {
        header("Location: venues.php?success=Venue added successfully");
        exit();
    } else {
        $error = $stmt->error;
    }
}

$countries = $conn->query("SELECT * FROM countries ORDER BY count_name");
$cities = $conn->query("SELECT * FROM cities ORDER BY city_name");
?>

<h3>Add New Venue <i class="fa-solid fa-location-dot fa-2xl"></i></h3>
<?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>

<form method="POST" style="max-width:600px;">
     <div class="input-group">
    <label>Venue Name</label>
    <input type="text" name="ven_name" required>
 </div>
     <div class="input-group">
    <label>Address</label>
    <textarea name="ven_address" rows="3"></textarea>
 </div>
     <div class="input-group">
    <label>Country</label>
    <select name="count_id" required>
        <option value="">Select Country</option>
        <?php while($c = $countries->fetch_assoc()): ?>
            <option value="<?= $c['count_id'] ?>"><?= $c['count_name'] ?></option>
        <?php endwhile; ?>
    </select>
 </div>
     <div class="input-group">
    <label>City</label>
    <select name="city_id">
        <option value="">Select City (Optional)</option>
        <?php while($c = $cities->fetch_assoc()): ?>
            <option value="<?= $c['city_id'] ?>"><?= $c['city_name'] ?></option>
        <?php endwhile; ?>
    </select>
 </div>
     
    <button type="submit" class="btn btn-success">Save Venue</button>
</form>

<?php include 'footer.php'; ?>