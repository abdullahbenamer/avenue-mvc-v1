<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
if (!isset($_SESSION['user_name'])) {
  header("Location: ../login.php"); // Redirect if not logged in
  exit();
}

require '../resources/db_config.php';
include('header.php') ?>

<!-- =========================== -->
<div class="container mt-5">
    <?php
    if (isset($_GET['id'])) {
        $instructor_id = mysqli_real_escape_string($conn, $_GET['id']);
        $query = "SELECT 
                    instructors.*, 
                    nations.nation_name AS nationality,
                    countries.count_name AS living_country,
                    cities.city_name AS city,
                    qualifications.qual_title AS qualification
                  FROM instructors
                  LEFT JOIN nations ON instructors.nation_id = nations.nation_id
                  LEFT JOIN countries ON instructors.count_id = countries.count_id
                  LEFT JOIN cities ON instructors.city_id = cities.city_id
                  LEFT JOIN qualifications ON instructors.qual_id = qualifications.qual_id
                  WHERE inst_id = '$instructor_id'";
        
        $query_run = mysqli_query($conn, $query);

        // Fetch courses taught by the instructor
        $course_query = "SELECT courses.course_title 
                         FROM courses 
                         JOIN instructors_courses ON courses.course_id = instructors_courses.course_id
                         WHERE instructors_courses.inst_id = '$instructor_id'";

        $course_run = mysqli_query($conn, $course_query);
        $courses = [];
        if (mysqli_num_rows($course_run) > 0) {
            while ($course = mysqli_fetch_array($course_run)) {
                $courses[] = $course['course_title'];
            }
        }

        if (mysqli_num_rows($query_run) > 0) {
            $instructor = mysqli_fetch_array($query_run);
    ?>
            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-3">
                        <div class="card-header">
                            <h4>Personal details</h4>
                        </div>
                        <div class="card-body">
                            <input type="hidden" name="id" value="<?= $instructor_id ?>">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <img class="inst_circle" src="photo_uploads/<?= !empty($instructor['inst_portrait']) ? $instructor['inst_portrait'] : 'instructor_male.jpg'; ?>" alt="Avatar">
                                    </div>
                                    <div class="mb-3">
                                        <label for="nationality">Nationality</label>
                                        <p><?= $instructor['nationality'] ?></p>
                                    </div>
                                    <div class="mb-3">
                                        <label for="living_country">Living country</label>
                                        <p><?= $instructor['living_country'] ?></p>
                                    </div>
                                    <div class="mb-3">
                                        <label for="city">City</label>
                                        <p><?= $instructor['city'] ?></p>
                                    </div>
                                     <!-- Social Link -->
                                    <div class="mb-3">
                                        <label for="social">Social Link</label>
                                        <p>
                                <?php 
                                if (!empty($instructor['social'])) {
                                    echo "<a href='" . $instructor['social'] . "' target='_blank'>Open social page</a>";
                                } else {
                                    echo "<em>No Social Page provided.</em>";
                                }
                                ?>
                             </p>
                           
                            </div>
                                    
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="name">Name</label>
                                        <p style="font-size: 1rem; font-weight: bold;"><?= strtoupper($instructor['full_name']) ?></p>
                                    </div>
                                    <div class="mb-3">
                                        <label for="qualification">Qualification</label>
                                        <p><?= $instructor['qualification'] ?></p>
                                    </div>
                                    <div class="mb-3">
                                        <label for="major">Major</label>
                                        <p><?= $instructor['major'] ?></p>
                                    </div>
                                    <div class="mb-3">
                                        <label for="mobile">Mobile</label>
                                        <p><?= $instructor['mobile'] ?></p>
                                    </div>
                                    <div class="mb-3">
                                        <label for="email">Email</label>
                                        <p><?= $instructor['email'] ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card mb-3">
                        <div class="card-header">
                            <h4>Professional details</h4>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
    <label for="interests">Introduction</label>
    <pre style="white-space: pre-wrap; font-family: inherit; font-size: 1rem;"><?= htmlspecialchars($instructor['interests']) ?></pre>
</div>
<div class="mb-3">
    <label for="keywords">Specialities (Search Keywords)</label>
    <pre style="white-space: pre-wrap; font-family: inherit; font-size: 1rem;"><?= htmlspecialchars($instructor['keywords']) ?></pre>
</div>
<div class="mb-3">
    <label>Language Proficiency</label>
    <ul style="padding-left: 1.25rem;">
        <li><strong>Arabic:</strong> <?= htmlspecialchars($instructor['arabic_level'] ?? 'Not specified') ?></li>
        <li><strong>English:</strong> <?= htmlspecialchars($instructor['english_level'] ?? 'Not specified') ?></li>
    </ul>
</div>
    <div class="mb-3">
    <label for="courses">Course(s) provided by the Instructor:</label>
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
                            <div class="mb-3">
                                <label for="cv_file">Curriculum vitae</label>
                                <p>
                                    <?php if (!empty($instructor['cv_file'])) : ?>
                                        <a href="cv_uploads/<?= $instructor['cv_file']; ?>" target="_blank">View CV</a>
                                    <?php else : ?>
                                        <span>No CV available</span>
                                    <?php endif; ?>
                                </p>
                            </div>
                            <div class="mb-3">
                                <label for="contract_file">Contract</label>
                                <p>
                                    <?php if (!empty($instructor['contract_file'])) : ?>
                                        <a href="contract_uploads/<?= $instructor['contract_file']; ?>" target="_blank">View Contract</a>
                                    <?php else : ?>
                                        <span>No Contract available</span>
                                    <?php endif; ?>
                                </p>
                            </div>
                            <hr>
                            <div class="mb-3">
    <label for="bank_details">Bank Account Details</label>
    <p>
        <?php 
        if (!empty($instructor['bank_details'])) {
            echo nl2br(htmlspecialchars($instructor['bank_details']));
        } else {
            echo "<em>No bank details available.</em>";
        }
        ?>
    </p>
</div>

<hr>
                            <!--<div class="mb-3">-->
                            <!--    <a href="edit.php?id=<?//= $instructor_id ?>" class="btn btn-primary">Edit Profile</a>-->
                            <!--</div>-->
                            
                        </div>
                    </div>
                </div>
            </div>
    <?php
        } else {
            echo "<h4>No such id found..!</h4>";
        }
    }
    ?>
</div>

<?php include('footer.php') ?>
