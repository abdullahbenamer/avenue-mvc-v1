<?php
// edit_venue.php
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

$ven_id = $_GET['id'] ?? null;
if (!$ven_id) {
    echo "Venue ID is required.";
    exit();
}

$stmt = $conn->prepare("SELECT * FROM venues WHERE ven_id = ?");
$stmt->bind_param("i", $ven_id);
$stmt->execute();
$result = $stmt->get_result();
$venue = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $conn->prepare("UPDATE venues SET ven_name = ?, ven_address = ?, count_id = ?, city_id = ? WHERE ven_id = ?");
    $stmt->bind_param("ssiii", $_POST['ven_name'], $_POST['ven_address'], $_POST['count_id'], $_POST['city_id'], $ven_id);
    if ($stmt->execute()) {
        header("Location: venues.php?success=Venue updated successfully");
        exit();
    } else {
        $error = $stmt->error;
    }
}

$countries = $conn->query("SELECT * FROM countries ORDER BY count_name");
$cities = $conn->query("SELECT * FROM cities ORDER BY city_name");
?>

<h3>Edit Venue <i class="fa-solid fa-location-dot fa-2xl"></i></h3>
<?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>

<form method="POST" style="max-width:600px;">
     <div class="input-group">
    <label>Venue Name</label>
    <input type="text" name="ven_name" value="<?= htmlspecialchars($venue['ven_name']) ?>" required>
 </div>
     <div class="input-group">
    <label>Address</label>
    <textarea name="ven_address" rows="3"><?= htmlspecialchars($venue['ven_address']) ?></textarea>
 </div>
     <div class="input-group">
    <label>Country</label>
    <select name="count_id" required>
        <?php while($c = $countries->fetch_assoc()): ?>
            <option value="<?= $c['count_id'] ?>" <?= $venue['count_id'] == $c['count_id'] ? 'selected' : '' ?>><?= $c['count_name'] ?></option>
        <?php endwhile; ?>
    </select>
 </div>
     <div class="input-group">
    <label>City</label>
    <select name="city_id">
        <option value="">Select City (Optional)</option>
        <?php while($c = $cities->fetch_assoc()): ?>
            <option value="<?= $c['city_id'] ?>" <?= $venue['city_id'] == $c['city_id'] ? 'selected' : '' ?>><?= $c['city_name'] ?></option>
        <?php endwhile; ?>
    </select>
 </div>
     
    <button type="submit" class="btn btn-primary">Update Venue</button>
</form>

<?php include 'footer.php'; ?>
