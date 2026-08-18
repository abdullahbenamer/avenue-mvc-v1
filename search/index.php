<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
if (!isset($_SESSION['user_name'])) {
  header("Location: ../login.php"); // Redirect if not logged in
  exit();
}

// Check if the logged-in user is an admin
// $is_admin = ($_SESSION['user_role'] == 'ADMIN' || $_SESSION['user_role'] == 'ACCOUNTANT');
$is_admin = ($_SESSION['user_role'] == 'ADMIN');

include '../resources/db_config.php';

?>
<?php include 'header.php'; ?>

<div class="container mt-5">

  <?php include('message.php') ?>

  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <h4><i class="fa-solid fa-person-chalkboard fa-2x"></i> List Instructors
            <a href="create.php" class="btn btn-primary float-end">Add Instructor</a>
          </h4>
        </div>
        <div class="card-body">
          <div class="table-responsive">
<div class="grid-table">
  <!-- Header -->
  <div class="grid-header">
    <div class="grid-cell"><strong>ID</strong></div>
    <div class="grid-cell"><strong>Photo</strong></div>
    <div class="grid-cell"><strong>Full Name</strong></div>
    <div class="grid-cell"><strong>Major</strong></div>
    <div class="grid-cell"><strong>Mobile</strong></div>
    <div class="grid-cell"><strong>Email</strong></div>
    <div class="grid-cell"><strong>Action</strong></div>
  </div>

<?php
$index = 0;
$query = "SELECT * FROM instructors ORDER BY full_name";
$query_run = mysqli_query($conn, $query);

if (mysqli_num_rows($query_run) > 0) {
  foreach ($query_run as $instructor) {
    $row_class = ($index % 2 === 0) ? 'even' : 'odd';
    $portrait = !empty($instructor['inst_portrait']) && file_exists("photo_uploads/" . $instructor['inst_portrait'])
      ? "photo_uploads/" . $instructor['inst_portrait']
      : "photo_uploads/instructor_male.jpg";
    ?>
    <div class="grid-row <?= $row_class ?>">
      <div class="grid-cell"><?= $instructor['inst_id']; ?></div>
      <div class="grid-cell">
        <div style="width: 45px; height: 45px; border-radius: 50%; overflow: hidden;">
          <img src="<?= $portrait ?>" alt="Portrait" style="width: 100%; height: 100%; object-fit: cover;">
        </div>
      </div>
      <div class="grid-cell name"><?= $instructor['full_name']; ?></div>
      <div class="grid-cell major"><?= $instructor['major']; ?></div>
      <div class="grid-cell"><?= $instructor['mobile']; ?></div>
      <div class="grid-cell email"><?= $instructor['email']; ?></div>
      <div class="grid-cell grid-actions">
        <a href="view_single.php?id=<?= $instructor['inst_id'] ?>" class="btn btn-info btn-sm">View</a>
        <!--<a href="edit.php?id=<?//= $instructor['inst_id'] ?>" class="btn btn-success btn-sm">Edit</a>-->
        <?php if ($is_admin) : ?>
          <form action="code.php" method="post" class="d-inline" onsubmit="return confirm('Are you sure?');">
            <button type="submit" name="delete" value="<?= $instructor['inst_id'] ?>" class="btn btn-danger btn-sm">Delete</button>
          </form>
        <?php endif; ?>
      </div>
    </div>
    <?php
    $index++;
  }
} else {
  echo "<div class='grid-row'><div class='grid-cell' style='grid-column: span 7; text-align:center;'>No Instructors Found</div></div>";
}
?>

</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<style>
.grid-table {
  display: grid;
  grid-template-columns: 4% 6% 23% 17% 12% 22% 15%;
  background: #f8f9fa;
  border: 1px solid #dee2e6;
  margin-top: 1rem;
  font-size: 0.8rem;
}
.grid-header, .grid-row {
  display: contents;
}
.grid-cell {
  border: 1px solid #dee2e6;
  padding: 5px;
  display: flex;
  align-items: center;
}
.grid-cell.name {
  font-weight: 600;
  color: #0000FF;
  text-transform: uppercase;
}
.grid-cell.major {
  text-transform: uppercase;
}
.grid-cell.email {
  text-transform: lowercase;
}
.grid-actions a,
.grid-actions form {
  margin-right: 4px;
}
.grid-row.even .grid-cell {
  background-color: #ffffff;
}

.grid-row.odd .grid-cell {
  background-color: #DDD;
}

</style>
<?php include('footer.php'); ?>
