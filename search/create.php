<?php
ob_start();
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
require '../resources/db_config.php';
include('header.php');

// Fetch qualifications
$qualifications = mysqli_query($conn, "SELECT qual_id, qual_title FROM qualifications");

// Fetch courses
$courses = mysqli_query($conn, "SELECT course_id, course_title FROM courses");

// Fetch countries
$countries = mysqli_query($conn, "SELECT count_id, count_name FROM countries");

// Fetch Nationalities
$nations = mysqli_query($conn, "SELECT nation_id, nation_name FROM nations");

// Fetch cities
$cities = mysqli_query($conn, "SELECT city_id, city_name FROM cities");

// ================= Add Instructor =================
if (isset($_POST['save'])) {
    $full_name = isset($_POST['full_name']) ? mysqli_real_escape_string($conn, $_POST['full_name']) : '';
    $qual_id = isset($_POST['qual_id']) ? mysqli_real_escape_string($conn, $_POST['qual_id']) : '';
    $major = isset($_POST['major']) ? mysqli_real_escape_string($conn, $_POST['major']) : '';
    $interests = isset($_POST['interests']) ? mysqli_real_escape_string($conn, $_POST['interests']) : '';
    $keywords = isset($_POST['keywords']) ? mysqli_real_escape_string($conn, $_POST['keywords']) : '';
    $social = isset($_POST['social']) ? mysqli_real_escape_string($conn, $_POST['social']) : '';
    $mobile = isset($_POST['mobile']) ? mysqli_real_escape_string($conn, $_POST['mobile']) : '';
    $email = isset($_POST['email']) ? mysqli_real_escape_string($conn, $_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $nation_id = isset($_POST['nationality']) ? mysqli_real_escape_string($conn, $_POST['nationality']) : '';
    $count_id = isset($_POST['country']) ? mysqli_real_escape_string($conn, $_POST['country']) : '';
    $city_id = isset($_POST['city_id']) ? mysqli_real_escape_string($conn, $_POST['city_id']) : '';
    $bank_details = isset($_POST['bank_details']) ? mysqli_real_escape_string($conn, $_POST['bank_details']) : '';
    $arabic_level = isset($_POST['arabic_level']) ? mysqli_real_escape_string($conn, $_POST['arabic_level']) : 'None';
    $english_level = isset($_POST['english_level']) ? mysqli_real_escape_string($conn, $_POST['english_level']) : 'None';

    
    // Initialize file names
    $pdf_file_name = '';
    $instructor_image_name = '';
    $contract_file_name = '';

// Handle file uploads (if needed)
if (isset($_FILES['pdf_file']['name']) && $_FILES['pdf_file']['name'] != '') {
    $pdf_file_extension = pathinfo($_FILES['pdf_file']['name'], PATHINFO_EXTENSION);
    $pdf_file_name = uniqid('cv_', true) . '.' . $pdf_file_extension;
    $pdf_file_tmp = $_FILES['pdf_file']['tmp_name'];
    $pdf_file_destination = "cv_uploads/" . $pdf_file_name;
    move_uploaded_file($pdf_file_tmp, $pdf_file_destination);
}
   if (isset($_FILES['instructor_image']['name']) && $_FILES['instructor_image']['name'] != '') {
    $instructor_image_extension = pathinfo($_FILES['instructor_image']['name'], PATHINFO_EXTENSION);
    $instructor_image_name = uniqid('photo_', true) . '.' . $instructor_image_extension;
    $instructor_image_tmp = $_FILES['instructor_image']['tmp_name'];
    $instructor_image_destination = "photo_uploads/" . $instructor_image_name;
    move_uploaded_file($instructor_image_tmp, $instructor_image_destination);
}

if (isset($_FILES['contract_file']['name']) && $_FILES['contract_file']['name'] != '') {
    $contract_file_extension = pathinfo($_FILES['contract_file']['name'], PATHINFO_EXTENSION);
    $contract_file_name = uniqid('contract_', true) . '.' . $contract_file_extension;
    $contract_file_tmp = $_FILES['contract_file']['tmp_name'];
    $contract_file_destination = "contract_uploads/" . $contract_file_name;
    move_uploaded_file($contract_file_tmp, $contract_file_destination);
}

    // Handle multiple course IDs
    if (isset($_POST['course_id'])) {
        $course_ids = implode(',', $_POST['course_id']); // Convert array to comma-separated string
    } else {
        $course_ids = ''; // No courses selected
    }
    
    $query = "INSERT INTO instructors 
(full_name, qual_id, major, interests, keywords, social, mobile, email, password, nation_id, count_id, city_id, cv_file, inst_portrait, contract_file, bank_details, arabic_level, english_level)
VALUES 
('$full_name', '$qual_id', '$major', '$interests', '$keywords', '$social', '$mobile', '$email', '$hashed_password', '$nation_id', '$count_id', '$city_id', '$pdf_file_name', '$instructor_image_name', '$contract_file_name', '$bank_details', '$arabic_level', '$english_level')";


    if (mysqli_query($conn, $query)) {
        $inst_id = mysqli_insert_id($conn); // Get the last inserted instructor ID

        if (isset($_POST['course_id']) && !empty($_POST['course_id'])) {
            foreach ($_POST['course_id'] as $course_id) {
                $course_id = mysqli_real_escape_string($conn, $course_id);
                $course_query = "INSERT INTO instructors_courses (inst_id, course_id) VALUES ('$inst_id', '$course_id')";
                mysqli_query($conn, $course_query);
            }
        }

        $_SESSION['message'] = "Instructor Created Successfully :)";
        header("Location: create.php");
        exit(0);
    } else {
        $error_message = mysqli_error($conn);
        echo "Error Creating Instructor: $error_message";
        exit(0);
    }
}
ob_end_flush();
?>

<!-- Rest of the HTML code remains unchanged -->
<div class="container mt-5">
    <?php include('message.php'); ?>
    <div class="row">
        <div class="col-md-7">
            <div class="card">
                <div class="card-header">
                    <h4><i class="fa fa-plus fa-2x"></i> Add Instructor</h4>
                </div>
                <div class="card-body">
                   
                    <!--FORM to create a new instructor-->
                    
                    <form method="post" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="full_name">Instructor Name</label>
                            <input type="text" name="full_name" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="instructor_image">Upload PHOTO (Only Image files)</label>
                            <input type="file" name="instructor_image" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label for="qual_id">Qualification</label>
                            <select name="qual_id" class="form-control">
                                <?php while ($row = mysqli_fetch_assoc($qualifications)) : ?>
                                    <option value="<?= $row['qual_id']; ?>"><?= $row['qual_title']; ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="major">Major</label>
                            <input type="text" name="major" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label for="interests">Interests (Introduction)</label>
                            <textarea name="interests" class="form-control"></textarea>
                        </div>

                                                <div class="mb-3">
                            <label for="keywords">Keywords (Tags) - <i>(Search Criteria)</i></label>
                            <textarea name="keywords" class="form-control" required></textarea>
                        </div>
                        
                        <div class="mb-3">
    <label for="course_id">Courses Tought</label>
    <select style="height: 25em; font-size: 0.8rem;" name="course_id[]" class="form-control" multiple>
        <?php while ($row = mysqli_fetch_assoc($courses)) : ?>
            <option value="<?= $row['course_id']; ?>"><?= strtoupper($row['course_title']); ?></option>
        <?php endwhile; ?>
    </select>
</div>

                        <div class="mb-3">
                            <label for="mobile">Mobile</label>
                            <input type="text" name="mobile" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="email">Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="nationality">Nationality</label>
                            <select name="nationality" class="form-control">
                                <?php while ($row = mysqli_fetch_assoc($nations)) : ?>
                                    <option value="<?= $row['nation_id']; ?>"><?= $row['nation_name']; ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="country">Country of Residence</label>
                            <select name="country" class="form-control">
                                <?php while ($row = mysqli_fetch_assoc($countries)) : ?>
                                    <option value="<?= $row['count_id']; ?>"><?= $row['count_name']; ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="city_id">City</label>
                            <select name="city_id" class="form-control">
                                <?php while ($row = mysqli_fetch_assoc($cities)) : ?>
                                    <option value="<?= $row['city_id']; ?>"><?= $row['city_name']; ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="social">Social Link (url)</label>
                            <input type="url" name="social" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label for="pdf_file">Upload CV (Only PDF File)</label>
                            <input type="file" name="pdf_file" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label for="contract_file">Upload CONTRACT (Only PDF File)</label>
                            <input type="file" name="contract_file" class="form-control">
                        </div>

<div class="mb-3">
    <label for="bank_details">Bank Account Details</label>
    <textarea name="bank_details" class="form-control" rows="3" placeholder="e.g., Bank Name, Account Number, IBAN, Swift Code"></textarea>
</div>

<div class="mb-3">
    <label for="arabic_level">Arabic Language Level</label>
    <select name="arabic_level" class="form-control" required>
        <option value="">-- Select Arabic Level --</option>
        <option value="None">None</option>
        <option value="Beginner">Beginner</option>
        <option value="Intermediate">Intermediate</option>
        <option value="Advanced">Advanced</option>
    </select>
</div>

<div class="mb-3">
    <label for="english_level">English Language Level</label>
    <select name="english_level" class="form-control" required>
        <option value="">-- Select English Level --</option>
        <option value="None">None</option>
        <option value="Beginner">Beginner</option>
        <option value="Intermediate">Intermediate</option>
        <option value="Advanced">Advanced</option>
    </select>
</div>


<div class="mb-3">
    <label for="password">Set Instructor Password</label>
    <input type="password" name="password" class="form-control" required>
</div>


                        <div class="mb-3">
                            <button type="submit" name="save" class="searchBtn">Create New</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include('footer.php'); ?>