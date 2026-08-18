<?php
ob_start();
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['inst_id'])) {
    header("Location: login_instructor.php");
    exit();
}
$instructor_id = $_SESSION['inst_id'];

require '../resources/db_config.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

$email_status = ''; // sent email confirmation message

// Fetch instructor data
$stmt = $conn->prepare("SELECT * FROM instructors WHERE inst_id = ?");
$stmt->bind_param("i", $instructor_id);
$stmt->execute();
$result = $stmt->get_result();
$instructor = $result->fetch_assoc();

// Initialize missing keys
$instructor = array_merge([
    'full_name' => '',
     'mobile' => '',
    'email' => '',
    'keywords' => '',
    'nation_id' => '',
    'cv_file' => '',
    'inst_portrait' => '',
    'social' => '',
    'bank_details' => '',
], $instructor);

// Fetch courses list
$courses_result = $conn->query("SELECT course_id, course_title FROM courses");

// Fetch instructor's assigned course IDs
$assigned_course_ids = [];
$course_stmt = $conn->prepare("SELECT course_id FROM instructors_courses WHERE inst_id = ?");
$course_stmt->bind_param("i", $instructor_id);
$course_stmt->execute();
$course_result = $course_stmt->get_result();
while ($row = $course_result->fetch_assoc()) {
    $assigned_course_ids[] = $row['course_id'];
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $count_id = $_POST['count_id'] ?? null;
    $city_id = $_POST['city_id'] ?? null;
    $full_name = trim($_POST['full_name']);
    $mobile = trim($_POST['mobile'] ?? '');
    if (!empty($mobile) && !preg_match('/^\+?[0-9\s\-]{7,20}$/', $mobile)) {
    $errors[] = "Invalid mobile number format.";
    }
    $email = trim($_POST['email']);
    $interests = $_POST['interests'];
    $keywords = trim($_POST['keywords']);
    $nation_id = $_POST['nation_id'];
    $courses = $_POST['courses'] ?? [];
    $arabic_level = isset($_POST['arabic_level']) ? $_POST['arabic_level'] : 'None';
    $english_level = isset($_POST['english_level']) ? $_POST['english_level'] : 'None';
    $social = trim($_POST['social']);
    $bank_details = trim($_POST['bank_details']);
if ($bank_details === 'No bank details provided') {
    $bank_details = ''; // Don't store the placeholder in DB
}

    $errors = [];

    // Email validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    // Email duplication check
    $email_check = $conn->prepare("SELECT inst_id FROM instructors WHERE email = ? AND inst_id != ?");
    $email_check->bind_param("si", $email, $instructor_id);
    $email_check->execute();
    $email_check->store_result();
    if ($email_check->num_rows > 0) {
        $errors[] = "This email is already used by another instructor.";
    }

    // Handle file uploads with validation
    function upload_file($file_input, $target_dir, $current_file = '', $allowed_types = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'], $max_size = 2 * 1024 * 1024) {
        if (isset($_FILES[$file_input]) && $_FILES[$file_input]['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES[$file_input];
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

            if (!in_array($ext, $allowed_types)) {
                return ['error' => "Invalid file type for $file_input."];
            }

            if ($file['size'] > $max_size) {
                return ['error' => "File $file_input exceeds max size of 2MB."];
            }

            $filename = uniqid() . '.' . $ext;
            $target_path = $target_dir . $filename;
            if (move_uploaded_file($file['tmp_name'], $target_path)) {
                return ['filename' => $filename];
            }
        }
        return ['filename' => $current_file];
    }

    if (empty($errors)) {
        $cv = upload_file('cv_file', 'cv_uploads/', $instructor['cv_file']);
        $photo = upload_file('photo_file', 'photo_uploads/', $instructor['inst_portrait'], ['jpg', 'jpeg', 'png']);
      
        foreach ([$cv, $photo] as $upload) {
            if (isset($upload['error'])) {
                $errors[] = $upload['error'];
            }
        }
    }

    if (empty($errors)) {

// Handle password update if provided
if (!empty($_POST['password'])) {
    $new_password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'] ?? '';

    if ($new_password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    } else {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $password_update_stmt = $conn->prepare("UPDATE instructors SET password = ? WHERE inst_id = ?");
        $password_update_stmt->bind_param("si", $hashed_password, $instructor_id);
        $password_update_stmt->execute();
    }
}


   $update_stmt = $conn->prepare("UPDATE instructors 
    SET full_name=?, mobile=?, email=?, nation_id=?, count_id=?, city_id=?, keywords=?, interests=?, cv_file=?, inst_portrait=?, arabic_level=?, english_level=?, social=?, bank_details=? 
    WHERE inst_id=?");

$update_stmt->bind_param("sssiiissssssssi", 
    $full_name, 
    $mobile, 
    $email, 
    $nation_id, 
    $count_id, 
    $city_id, 
    $keywords, 
    $interests, 
    $cv['filename'], 
    $photo['filename'], 
    $arabic_level, 
    $english_level, 
    $social, 
    $bank_details,
    $instructor_id
);

       $update_stmt->execute();

        $conn->query("DELETE FROM instructors_courses WHERE inst_id = $instructor_id");
        $insert_course = $conn->prepare("INSERT INTO instructors_courses (inst_id, course_id) VALUES (?, ?)");
        foreach ($courses as $course_id) {
            $insert_course->bind_param("ii", $instructor_id, $course_id);
            $insert_course->execute();
        }

    // SEND EMAIL TO INSTRUCTOR
    //==========================
     $mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host       = 'mail.erahorizons.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'cto@erahorizons.com';
    $mail->Password   = 'Ct20o25@'; // your mailbox/app password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    $mail->setFrom('cto@erahorizons.com', 'Avenue International');
    $mail->addAddress($email, $full_name); // send to the instructor’s email

    $mail->isHTML(true);
    $mail->Subject = 'Profile Update Confirmation';

    // Build the email body
    // $mail->Body = "<p>This is a System Generated Message.</p>";
    $mail->Body = "<p>This is a System Generated Message.</p>
    <p>Dear $full_name,</p>
    <p>Your profile has been successfully updated in our system.</p>";

    if (!empty($_POST['password'])) {
        $mail->Body .= "<p><strong>Your new password:</strong> " . htmlspecialchars($_POST['password']) . "</p>";
    }
    $mail->Body .= "<p>For future update, login to:<br>https://www.app.erahorizons.com/appv5_dev/search/login_instructor.php<br> or contact +905349216965, contact@avenueinternational.net.</p>";
    $mail->Body .= "<p>Best regards,<br>Avenue International Training Center</p>";

    $mail->send();
    $email_status = "<div class='alert alert-success'>✅ Confirmation email sent to $email</div>";
} catch (Exception $e) {
    $email_status = "<div class='alert alert-warning'>⚠️ Email could not be sent. Error: {$mail->ErrorInfo}</div>";
}

echo "<div class='alert alert-success'>Profile updated successfully. 
<a href='instructor_profile.php?inst_id=" . $_SESSION['inst_id'] . "' class='btn btn-outline-primary btn-sm ms-3'>View My Profile</a>
</div>";

// Refresh instructor data
        $stmt = $conn->prepare("SELECT * FROM instructors WHERE inst_id = ?");
        $stmt->bind_param("i", $instructor_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $instructor = $result->fetch_assoc();
    } else {
        foreach ($errors as $error) {
            echo "<div class='alert alert-danger'>$error</div>";
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="shortcut icon" href="icon.ico" type="image/x-icon" />
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Noto+Naskh+Arabic:wght@400;500;600;700&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;1,300&family=Tajawal:wght@200;300;400;500&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
  <link href="fa/css/all.min.css" rel="stylesheet">
  <style>
    label {
        color: #CC0A0B; 
        font-size: 1.2rem;
        font-weight: bold;
    }
</style>
</head>
<body>
     <div class="logo">
    <img width="300" src="../resources/logo.png" alt="/resources/logo.png">
  </div>
<div class="container mt-4">
    <h2>Edit Your Profile</h2>
    <div><?php if ($email_status) echo $email_status; ?></div>
    <form method="POST" enctype="multipart/form-data">
        <br><br>
        <!-- Profile photo -->
        <?php 
         $portrait = (!empty($instructor['inst_portrait']) && file_exists("photo_uploads/" . $instructor['inst_portrait']))
    ? "photo_uploads/" . $instructor['inst_portrait']
    : "photo_uploads/instructor_male.jpg";
    ?>
<div>
<img src="<?= htmlspecialchars($portrait) ?>" alt="Instructor Portrait" class="rounded-circle shadow" width="150" height="150">
</div>
<br><br>
       <div class="mb-3">
    <label>Full Name</label>
    <input type="text" name="full_name" class="form-control" required value="<?= htmlspecialchars($instructor['full_name']) ?>">
</div>
<div class="mb-3">
    <label>Mobile Phone <i class="fa-solid fa-mobile-screen fa-1x"></i></label>
    <input type="tel" name="mobile" class="form-control" placeholder="+1234567890" value="<?= htmlspecialchars($instructor['mobile']) ?>">
    <small style="color: red !important;" class="form-text text-muted">Include country code, e.g., +90 123 555 6666</small>
</div>
<div class="mb-3">
    <label>Email <i class="fa-solid fa-envelope fa-1x"></i></label>
    <input type="email" name="email" class="form-control" required value="<?= htmlspecialchars($instructor['email']) ?>">
</div>
<div class="mb-3">
        <label for="social" class="form-label">Social Link</label>
        <input type="url" name="social" class="form-control"
               value="<?= htmlspecialchars($instructor['social']) ?>"
               placeholder="https://linkedin.com/in/username"
               autocomplete="off">
    </div>
<div class="mb-3">
    <label>Nationality <i class="fa-solid fa-globe fa-1x"></i></label>
    <select name="nation_id" class="form-control" required>
        <option value="">Select Nationality</option>
        <?php
        $nation_result = $conn->query("SELECT nation_id, nation_name FROM nations ORDER BY nation_name");
        while ($row = $nation_result->fetch_assoc()) {
            $selected = ($instructor['nation_id'] == $row['nation_id']) ? 'selected' : '';
            echo "<option value='{$row['nation_id']}' $selected>{$row['nation_name']}</option>";
        }
        ?>
    </select>
</div>

<div class="mb-3">
    <label>Country of Residency <i class="fa-solid fa-location-dot fa-1x"></i></label>
    <select name="count_id" class="form-control">
        <option value="">Select Country</option>
        <?php
        $country_result = $conn->query("SELECT count_id, count_name FROM countries ORDER BY count_name");
        while ($row = $country_result->fetch_assoc()) {
            $selected = ($instructor['count_id'] == $row['count_id']) ? 'selected' : '';
            echo "<option value='{$row['count_id']}' $selected>{$row['count_name']}</option>";
        }
        ?>
    </select>
</div>

<div class="mb-3">
    <label>City</label>
    <select name="city_id" class="form-control">
        <option value="">Select City</option>
        <?php
        $city_result = $conn->query("SELECT city_id, city_name FROM cities ORDER BY city_name");
        while ($row = $city_result->fetch_assoc()) {
            $selected = ($instructor['city_id'] == $row['city_id']) ? 'selected' : '';
            echo "<option value='{$row['city_id']}' $selected>{$row['city_name']}</option>";
        }
        ?>
    </select>
</div>

<div class="mb-3">
    <label>Introduction (Bio)</label>
    <textarea name="interests" class="form-control" rows="4" maxlength="1000" required
              oninput="document.getElementById('bioCount').textContent = this.value.length + '/1000 characters';"><?= htmlspecialchars($instructor['interests']) ?></textarea>
    <small style="color: red; font-weight: bold;" id="bioCount"><?= strlen($instructor['interests']) ?>/1000 characters</small>
    <small style="color: red !important;" class="form-text text-muted">
        Brief introduction about your TRAINING experience.
    </small>
</div>

<div class="mb-3">
    <label>Keywords <i class="fa-solid fa-key fa-1x"></i><small>[Instructor search criteria]</small>:</label>
    <textarea class="form-control" name="keywords" id="keywords" rows="3" maxlength="500" required
              oninput="document.getElementById('keywordsCount').textContent = this.value.length + '/500 characters';"><?= htmlspecialchars($instructor['keywords']) ?></textarea>
    <small style="color: red; font-weight: bold;" id="keywordsCount"><?= strlen($instructor['keywords']) ?>/500 characters</small>
    <small style="color: red !important;" class="form-text text-muted">
        Enter keywords separated by commas, avoid repeating,  e.g., "Project, Maintenance, Programming, Upstream, Downstream, Instrumentation, PLC, Electric, water, civil, networking, security, fiber optics, engineering"
    </small>
</div>


<div class="mb-3">
    <label>Courses Taught</label>
    <select name="courses[]" class="form-control" multiple size="10">
        <?php while ($row = $courses_result->fetch_assoc()): ?>
            <option value="<?= $row['course_id'] ?>" <?= in_array($row['course_id'], $assigned_course_ids) ? 'selected' : '' ?>>
                <?= $row['course_title'] ?>
            </option>
        <?php endwhile; ?>
    </select>
     <small style="color: red !important;" class="form-text text-muted">
        Use CTRL key to select more courses.
    </small>
</div>

<!-- Language Levels -->
<div class="mb-3">
    <label for="arabic_level">Arabic Language Level <i class="fa-solid fa-language"></i></label>
    <select name="arabic_level" class="form-control" required>
        <option value="">-- Select Arabic Level --</option>
        <?php
        $levels = ['None', 'Beginner', 'Intermediate', 'Advanced'];
        foreach ($levels as $level) {
            $selected = ($instructor['arabic_level'] == $level) ? 'selected' : '';
            echo "<option value='$level' $selected>$level</option>";
        }
        ?>
    </select>
</div>

<div class="mb-3">
    <label for="english_level">English Language Level <i class="fa-solid fa-language"></i></label>
    <select name="english_level" class="form-control" required>
        <option value="">-- Select English Level --</option>
        <?php
        foreach ($levels as $level) {
            $selected = ($instructor['english_level'] == $level) ? 'selected' : '';
            echo "<option value='$level' $selected>$level</option>";
        }
        ?>
    </select>
</div>


<div class="mb-3">
    <label>Upload CV <i class="fa-solid fa-file-alt fa-1x"></i></label>
    <input type="file" name="cv_file" class="form-control" <?= empty($instructor['cv_file']) ? 'required' : '' ?>>
    <?php if (!empty($instructor['cv_file'])): ?>
        <small><a href="cv_uploads/<?= $instructor['cv_file'] ?>" target="_blank">Your Current CV</a></small>
    <?php endif; ?>
</div>

        <div class="mb-3">
            <label>Upload a New Photo <i class="fa-solid fa-photo-film fa-1x"></i></label>
            <input type="file" name="photo_file" class="form-control">
            <?php if (!empty($instructor['inst_portrait'])): ?>
                <small><a href="photo_uploads/<?= $instructor['inst_portrait'] ?>" target="_blank">Your Current Photo</a></small>
            <?php endif; ?>
        </div>
         
<br>
  <div class="mb-3">
    <label for="contract_file">Contract <i class="fas fa-file-signature fa-1x"></i></label>
            <p>
            <?php if (!empty($instructor['contract_file'])) : ?>
                <a href="contract_uploads/<?= $instructor['contract_file']; ?>" target="_blank">View Contract</a>
                                    <?php else : ?>
                                        <span>No Contract available</span>
                                    <?php endif; ?>
                                </p>
                            </div>
                          
<div class="mb-3">
    <label>Bank Details <i class="fas fa-bank fa-1x"></i></label>
      <textarea name="bank_details" class="form-control" rows="6"
        placeholder="HOLDER NAME:
BANK NAME:
BRANCH ADDRESS:
IBAN:
BANK ACCOUNT NUMBER:
SWIFT CODE:"><?= htmlspecialchars($instructor['bank_details'] ?: 'No bank details provided') ?></textarea>
    <small style="color: red !important;" class="form-text text-muted">Fill in all the bank information as needed. Include bank name, branch address, IBAN, Account Number, SWIFT code, or any other payment details.</small>
</div>
<br>
<div class="mb-3">
        <label for="password" class="form-label">New Password</label>
        <input type="password" name="password" class="form-control"
               placeholder="[Leave blank to keep current password]"
               autocomplete="new-password">
    </div>

    <div class="mb-3">
        <label for="confirm_password" class="form-label">Confirm Password</label>
        <input type="password" name="confirm_password" class="form-control"
               placeholder="Re-enter new password"
               autocomplete="new-password">
    </div>

<br><br>
        <button type="submit" class="btn btn-primary">Update Profile</button>
<a href="instructor_profile.php?inst_id=<?= $_SESSION['inst_id'] ?>" class="btn btn-secondary ms-2">Cancel</a>

    </form>
</div>
<br><br>
<?php include('footer.php'); ?>
