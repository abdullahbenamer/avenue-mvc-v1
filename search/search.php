<?php
session_start();
if (!isset($_SESSION['user_name'])) {
  header("Location: ../login.php"); // Redirect if not logged in
  exit();
}
include('../resources/db_config.php');
include('header.php');
?>
<section>
    <div class="main">
        <p><i class="fas fa-search fa-5x"></i></p>
        <p class="search_header">Search for Instructor(s)</p>
        <div>
            <form action="" method="GET">
                   <p style="display: flex; gap: 10px; flex-wrap: wrap;">
    <input class="searchBx" type="text" name="k" placeholder="Enter a KEYWORD, a TITLE, or INSTRUCTOR NAME " autocomplete="off" value="<?= isset($_GET['k']) ? htmlspecialchars($_GET['k']) : '' ?>">
    <input class="searchBtn" type="submit" value="Search" />
    <a href="<?= basename($_SERVER['PHP_SELF']); ?>" class="searchBtn" style="background-color: #eee; color: #666; text-align: center; text-decoration: none; padding: 8px 12px; border-radius: 4px;">Reset</a>
</p>
            </form>
        </div>

        <?php
        if (isset($_GET['k']) && trim($_GET['k']) !== '') {
            $k = trim($_GET['k']);
            mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
            $conn = new mysqli($servername, $username, $password, $database);
            $keywords = explode(' ', $k);

            // Build query
            $query_string = "SELECT DISTINCT instructors.*, qualifications.qual_title 
                             FROM instructors
                             LEFT JOIN qualifications ON instructors.qual_id = qualifications.qual_id
                             LEFT JOIN instructors_courses ON instructors.inst_id = instructors_courses.inst_id
                             LEFT JOIN courses ON instructors_courses.course_id = courses.course_id
                             WHERE ";

            $display_words = "";
            foreach ($keywords as $word) {
                $safe_word = $conn->real_escape_string($word);
                $query_string .= "(
                    instructors.keywords LIKE '%$safe_word%' OR 
                    instructors.interests LIKE '%$safe_word%' OR 
                    instructors.full_name LIKE '%$safe_word%' OR 
                    courses.course_title LIKE '%$safe_word%'
                ) OR ";
                $display_words .= $safe_word . " ";
            }
            $query_string = substr($query_string, 0, -3); // remove last 'OR'

            $query = mysqli_query($conn, $query_string);
            $result_count = mysqli_num_rows($query);

            if ($result_count > 0) {
                echo '<br /><div class="right"><p class="succ_msg"><b><u>' . $result_count . '</u></b> result(s) found for your search <b><i>' . htmlspecialchars($display_words) . '</i></b></p></div><br />';
                echo '<i class="fa-solid fa-person-chalkboard fa-5x"></i><br>';
                echo '<div class="results container-fluid" style="width: 80%;"><div class="row">';

                while ($row = mysqli_fetch_assoc($query)) {
                    echo '<div class="col-md-4">';
                    echo '<div class="result-item" style="margin-bottom: 1rem; border: 1px solid #BBB; border-radius: 10px; padding: 1rem; min-height: 420px; display: flex; flex-direction: column; justify-content: space-between; background: #fafafa;">';

                    echo '<div class="instructor-content">';
                    echo '<h3><i class="fa-solid fa-person-chalkboard"></i> <a href="view_single.php?id=' . $row['inst_id'] . '">' . strtoupper(htmlspecialchars($row['full_name'])) . " (ID-" .  $row['inst_id'] . ")" .'</a></h3>';
                    echo '<img class="inst_thumb" src="photo_uploads/' . (!empty($row['inst_portrait']) ? $row['inst_portrait'] : 'instructor_male.jpg') . '" alt="Instructor Photo">';
                    echo '<p><i class="fa-solid fa-user-graduate"></i> ' . strtoupper(htmlspecialchars($row['qual_title'])) . '</p>';
                    echo '<p><i class="fa-solid fa-award"></i> ' . htmlspecialchars($row['major']) . '</p>';
                    echo '<p class="truncate"><i class="fa-solid fa-thumbs-up"></i> ' . htmlspecialchars($row['interests']) . '</p>';
                    echo '<p><i class="fa-solid fa-mobile"></i> ' . htmlspecialchars(strtolower($row['mobile'])) . '</p>';
                    echo '<p><i class="fa-solid fa-envelope"></i> ' . htmlspecialchars($row['email']) . '</p>';

                    echo '<p><i class="fa-solid fa-share-nodes"></i> ';
                    if (!empty($row['social']) && filter_var($row['social'], FILTER_VALIDATE_URL)) {
                        echo '<a href="' . htmlspecialchars($row['social']) . '" target="_blank">Social Link</a>';
                    } else {
                        echo 'No Social Link available';
                    }
                    echo '</p>';

                    echo '<div style="margin-top: auto; text-align: right;">';
                    echo '<a href="view_single.php?id=' . $row['inst_id'] . '" class="btn btn-sm btn-primary">Read More &raquo;</a>';
                    echo '</div>'; // button container

                    echo '</div>'; // instructor-content
                    echo '</div>'; // result-item
                    echo '</div>'; // col
                }
                echo '</div></div>'; // row and container
            } else {
                echo '<br /><p class="error_msg">Sorry, <strong>no results</strong> returned. Try another keyword(s).</p>';
            }
        } else {
            echo '<br /><br /><div><p class="error_msg"><i>Please, enter a keyword(s) for search...!</i></p></div>';
        }
        ?>
    </div>
</section>
<?php include('footer.php'); ?>
