<?php 
session_start();
require '../resources/db_config.php';

// ===== Update/Edit Instructor =======
if (isset($_POST['update'])) {
    $instructor_id = mysqli_real_escape_string($conn, $_POST['inst_id']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $education = mysqli_real_escape_string($conn, $_POST['education']);
    $major = mysqli_real_escape_string($conn, $_POST['major']);
    $interests = mysqli_real_escape_string($conn, $_POST['interests']);
    $url = mysqli_real_escape_string($conn, $_POST['url']);
    $keywords = mysqli_real_escape_string($conn, $_POST['keywords']);
    $mobile = mysqli_real_escape_string($conn, $_POST['mobile']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $nationality = mysqli_real_escape_string($conn, $_POST['nationality']);
    $country = mysqli_real_escape_string($conn, $_POST['country']);
    $city = mysqli_real_escape_string($conn, $_POST['city']);

    $query ="UPDATE instructors SET name='$name', education='$education', major='$major', interests='$interests', url='$url', keywords='$keywords', mobile='$mobile', email='$email', nationality='$nationality', country='$country', city='$city' WHERE inst_id='$instructor_id'";

    $query_run = mysqli_query($conn, $query);

    if ($query_run) {
        $_SESSION['message'] = "Instructor Updated Successfully :)";
        header("Location: index.php");
        exit(0);
    } else {
        $_SESSION['message'] = "Error Updating Instructor..!, Try again.";
        header("Location: index.php");
        exit(0);
    }
}

// ================= Add Instructor =================
if (isset($_POST['save'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $education = mysqli_real_escape_string($conn, $_POST['education']);
    $major = mysqli_real_escape_string($conn, $_POST['major']);
    $interests = mysqli_real_escape_string($conn, $_POST['interests']);
    $url = mysqli_real_escape_string($conn, $_POST['url']);
    $keywords = mysqli_real_escape_string($conn, $_POST['keywords']);
    $mobile = mysqli_real_escape_string($conn, $_POST['mobile']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $nationality = mysqli_real_escape_string($conn, $_POST['nationality']);
    $country = mysqli_real_escape_string($conn, $_POST['country']);
    $city = mysqli_real_escape_string($conn, $_POST['city']);

    // File upload handling
    $pdf_file = $_FILES['pdf_file']['name'];
    $instructor_image = $_FILES['instructor_image']['name'];
    $pdf_temp = $_FILES['pdf_file']['tmp_name'];
    $instructor_image_temp = $_FILES['instructor_image']['tmp_name'];

    move_uploaded_file($pdf_temp, "cv_uploads/$pdf_file");
    move_uploaded_file($instructor_image_temp, "photo_uploads/$instructor_image");

    $query ="INSERT INTO  instructors (name, education, major, interests, url, keywords, mobile, email, nationality, country, city, pdf_file, instructor_image) VALUES ('$name', '$education', '$major', '$interests', '$url', '$keywords', '$mobile', '$email', '$nationality', '$country', '$city', '$pdf_file', '$instructor_image')";

    $query_run = mysqli_query($conn, $query);

    if ($query_run) {
        $_SESSION['message'] = "Instructor Created Successfully :)";
        header("Location: create.php");
        exit(0);
    } else {
        $_SESSION['message'] = "Error Creating Instructor..!, Try again.";
        header("Location: create.php");
        exit(0);
    }
}

// =================== Delete Instructor =====================//
if (isset($_POST['delete'])) {
    $instructor_id = mysqli_real_escape_string($conn, $_POST['delete']);

    $query ="DELETE from instructors WHERE inst_id='$instructor_id'";

    $query_run = mysqli_query($conn, $query);

    if ($query_run) {
        $_SESSION['message'] = "Instructor Deleted.";
        header("Location: index.php");
        exit(0);
    } else {
        $_SESSION['message'] = "Error Deleting Instructor..!, Try again.";
        header("Location: index.php");
        exit(0);
    }
}
?>
