<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
require '../resources/db_config.php';
if (!isset($_SESSION['inst_id'])) {
    header("Location: login_instructor.php");
    exit();
}
$inst_id = $_SESSION['inst_id'];

// Fetch instructor
$query = "SELECT i.*, q.qual_title 
          FROM instructors i
          LEFT JOIN qualifications q ON i.qual_id = q.qual_id
          WHERE i.inst_id = $inst_id";
$result = mysqli_query($conn, $query);
$instructor = mysqli_fetch_assoc($result);

// Fetch courses taught by the instructor
$course_query = "SELECT courses.course_title 
                 FROM courses 
                 JOIN instructors_courses 
                 ON courses.course_id = instructors_courses.course_id
                 WHERE instructors_courses.inst_id = '$inst_id'";
$course_run = mysqli_query($conn, $course_query);

$courses = [];
if (mysqli_num_rows($course_run) > 0) {
    while ($course = mysqli_fetch_assoc($course_run)) {
        $courses[] = $course['course_title'];
    }
}





// Fallback portrait
$portrait = (!empty($instructor['inst_portrait']) && file_exists("photo_uploads/" . $instructor['inst_portrait']))
    ? "photo_uploads/" . $instructor['inst_portrait']
    : "photo_uploads/instructor_male.jpg";

// Fetch nation name
$nation_name = '';
if ($instructor['nation_id']) {
    $nq = mysqli_query($conn, "SELECT nation_name FROM nations WHERE nation_id = {$instructor['nation_id']}");
    if ($row = mysqli_fetch_assoc($nq)) {
        $nation_name = $row['nation_name'];
    }
}

// Fetch country name
$count_name = '';
if ($instructor['count_id']) {
    $cq = mysqli_query($conn, "SELECT count_name FROM countries WHERE count_id = {$instructor['count_id']}");
    if ($row = mysqli_fetch_assoc($cq)) {
        $count_name = $row['count_name'];
    }
}

// Fetch city name
$city_name = '';
if ($instructor['city_id']) {
    $ctq = mysqli_query($conn, "SELECT city_name FROM cities WHERE city_id = {$instructor['city_id']}");
    if ($row = mysqli_fetch_assoc($ctq)) {
        $city_name = $row['city_name'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Instructor Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="fa/css/all.min.css" rel="stylesheet">
    <style>
    strong {
        color: #CC0A0B; 
        font-size: 1.2rem;
        font-weight: bold;
    }
</style>
    
</head>
<body class="bg-light">
<?php include 'header_instructors.php'; ?>

<div class="container my-5">
    <div class="text-center mb-4">
        <img src="../resources/logo.png" width="200" alt="Logo">
    </div>

    <div class="card shadow">
        <div class="card-header bg-secondary text-white">
            <h4 class="mb-0">Welcome Instructor <?= htmlspecialchars($instructor['full_name']) ?> <i class="fa-solid fa-handshake fa-2x"></i></h4>
        </div>

        <div class="card-body">
            <div class="text-center mb-4">
                <img src="<?= htmlspecialchars($portrait) ?>" alt="Portrait" class="rounded-circle shadow" width="150" height="150">
            </div>

            <div class="row mb-3">
                <div class="col-md-6"><strong>Your ID:</strong> <?= $instructor['inst_id'] ?></div>
                  <div class="col-md-6"><i class="fa-solid fa-envelope fa-1x"></i> <?= htmlspecialchars($instructor['email']) ?></div>
                  
              
            </div>

            <div class="row mb-3">
                <div class="col-md-6"><strong>Full Name:</strong> <?= htmlspecialchars($instructor['full_name']) ?></div>
                <div class="col-md-6"><i class="fa-solid fa-mobile-screen fa-1x"></i> <?= htmlspecialchars($instructor['mobile']) ?></div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6"><strong>Major</strong> <i class="fa-solid fa-award fa-1x"></i>  <?= htmlspecialchars($instructor['major']) ?></div>
                 <div class="col-md-6"><strong>Arabic Level:</strong>  <?= htmlspecialchars($instructor['arabic_level']) ?></div>
                           </div>

            <div class="row mb-3">
                 <div class="col-md-6"><strong>Qualification</strong>  <i class="fa-solid fa-graduation-cap fa-1x"></i> <?= htmlspecialchars($instructor['qual_title']) ?></div>
               
                <div class="col-md-6"><strong>English Level:</strong>  <?= htmlspecialchars($instructor['english_level']) ?></div>
            </div>

            <div class="mb-3"><strong>Introduction (Bio):</strong>  <i class="fa-solid fa-chalkboard-user fa-1x"></i><br> <?= nl2br(htmlspecialchars($instructor['interests'])) ?></div>
            <div class="mb-3"><strong>Keywords</strong> <i class="fa-solid fa-key fa-1x"></i>  <small>[Instructor search criteria]</small>:<br> <?= nl2br(htmlspecialchars($instructor['keywords'])) ?></div>

                    <!-- courses provided by instructor-->
                    <div class="mb-3">
    <strong>Course(s) provided by the Instructor:</strong>
    <?php 
    if (!empty($courses)) { ?>
        <ol>
            <?php foreach ($courses as $course) { ?>
                <li><?= htmlspecialchars($course) ?></li>
            <?php } ?>
        </ol>
    <?php 
    } else {
        echo "<p><em>No specific courses were selected yet.</em></p>";
    } 
    ?>
</div>


            <div class="row mb-3">
                <div class="col-md-4"><strong>Nationality:</strong> <?= htmlspecialchars($nation_name) ?></div>
                <div class="col-md-4"><strong>Country:</strong> <?= htmlspecialchars($count_name) ?></div>
                <div class="col-md-4"><strong>City:</strong> <?= htmlspecialchars($city_name) ?></div>
                 
            </div>
           <div class="mb-3"><strong>Social</strong> <i class="fa-solid fa-share-nodes fa-1x"></i> :
    <?php if (!empty($instructor['social'])): ?>
        <a href="<?= htmlspecialchars($instructor['social'], ENT_QUOTES) ?>" target="_blank" rel="noopener noreferrer">
            <?= htmlspecialchars($instructor['social']) ?>
        </a>
    <?php else: ?>
        <span class="text-muted">No link found.</span>
    <?php endif; ?>
</div>

          <div class="mb-3">
  <strong>Bank Details</strong> <i class="fa fa-bank fa-1x"></i> :
    <hr>
  <?= !empty(trim($instructor['bank_details'])) ? nl2br(htmlspecialchars($instructor['bank_details'])) : 'No bank details provided.' ?>
    <hr>
</div>

            <div class="mb-3"><strong>Curriclum Vitea <i class="fa fa-file-alt fa-1x"></i> :</strong>
                <?php if ($instructor['cv_file']): ?>
                    <a href="cv_uploads/<?= htmlspecialchars($instructor['cv_file']) ?>" target="_blank" class="btn btn-sm btn-outline-secondary ms-2">View CV</a>
                <?php else: ?>
                    <span class="text-muted ms-2">Not Uploaded</span>
                <?php endif; ?>
            </div>

            <div class="mb-3"> <strong>Contract <i class="fas fa-file-signature fa-1x"></i> :</strong>
                <?php if ($instructor['contract_file']): ?>
                    <a href="contract_uploads/<?= htmlspecialchars($instructor['contract_file']) ?>" target="_blank" class="btn btn-sm btn-outline-secondary ms-2">View Contract</a>
                <?php else: ?>
                    <span class="text-muted ms-2">Not Uploaded</span>
                <?php endif; ?>
            </div>
        </div>

        <div class="card-footer d-flex justify-content-between">
            <a href="edit_profile.php" class="btn btn-warning">Edit Profile</a>
            <a href="logout_instructor.php" class="btn btn-danger">Logout</a>
        </div>
    </div>
</div>

<?php include('footer.php'); ?>
</body>
</html>
