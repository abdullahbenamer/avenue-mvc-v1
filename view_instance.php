<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
if (!isset($_SESSION['user_name'])) {
    header("Location: login.php");
    exit();
}

// Check if the user has the required role
if ($_SESSION['user_role'] != 'ADMIN' && $_SESSION['user_role'] != 'ACCOUNTANT' && $_SESSION['user_role'] != 'USER') {
    header("Location: unauthorized.php");
    exit();
}

// This is view_instance.php
include 'resources/db_config.php';
include 'resources/header.php'; ?>

<!--Show attendance Upload Success-->
<?php if (isset($_GET['msg']) && $_GET['msg'] === 'upload_success'): ?>
    <script>
        alert("Attendance sheet uploaded successfully!");
        // remove the ?msg=upload_success from URL without reloading
        if (history.replaceState) {
            const url = new URL(window.location);
            url.searchParams.delete('msg');
            history.replaceState(null, '', url);
        }
    </script>
<?php endif; ?>


<?php
$query = "
SELECT 
    qi.instance_id, 
    qi.instance_ref, 
    q.quot_id, 
    q.quot_ref, 
    c.cust_code, 
    COUNT(qp.part_id) AS participant_count, 
    i.instructor_names, 
    i.instructor_ids, 
    i.instructor_portraits,
    qp.start_date,
    qi.attendance_sheet  
FROM quotation_instances qi
JOIN quotations q ON qi.quot_id = q.quot_id
JOIN customers c ON qi.cust_id = c.cust_id
LEFT JOIN quotation_participants qp ON qi.instance_id = qp.instance_id
LEFT JOIN (
    SELECT 
        qi.instance_id, 
        GROUP_CONCAT(i.full_name SEPARATOR ',') AS instructor_names, 
        GROUP_CONCAT(i.inst_id SEPARATOR ',') AS instructor_ids,
        GROUP_CONCAT(i.inst_portrait SEPARATOR ',') AS instructor_portraits
    FROM quotation_instructors qi
    JOIN instructors i ON qi.instructor_id = i.inst_id
    GROUP BY qi.instance_id
) i ON i.instance_id = qi.instance_id
GROUP BY qi.instance_id
ORDER BY qi.instance_id DESC
";

$result = $conn->query($query);

$instance_id_with_constraint = null;

// Handle deletion logic (as per your existing code)
if (isset($_GET['del_id'])) {
    $instance_id = $_GET['del_id'];
    $check_related_sql = "SELECT * FROM quotation_instances WHERE instance_id = $instance_id";
    $result_check_related = $conn->query($check_related_sql);

    if ($result_check_related->num_rows > 0) {
        $instance_id_with_constraint = $instance_id;
    } else {
        $sql_delete = "DELETE FROM quotation_instances WHERE instance_id = $instance_id";
        if ($conn->query($sql_delete)) {
            echo "<script>alert('Quotation instance deleted successfully.'); window.location.href = 'view_instance.php';</script>";
        } else {
            echo "<script>alert('Error deleting record: " . $conn->error . "'); window.location.href = 'view_instance.php';</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        /* Grid layout */
        .grid-list-container {
            width: 97%;
            margin: 1.5rem auto;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            font-family: sans-serif;
        }

        .grid-header,
        .grid-row {
            display: grid;
            grid-template-columns: 4% 30% 5% 10% 6% 3% 18% 5% 7% 7%;
            gap: 0.4rem;
            align-items: center;
            padding: 0.4rem;
            border-bottom: 1px solid #BBB;
            font-size: 0.8rem;
            /* grid-template-columns: repeat(auto-fit, minmax(1fr));*/
            /*Make columns responsive */
        }

        .grid-header {
            font-weight: bold;
            background-color: #D1D1D1;
        }

        .grid-row {
            background: #fff;
            transition: background 0.2s ease;
        }

        .grid-row:hover {
            background: #D9D9D9;
        }

        .grid-row a {
            color: #0055aa;
            text-decoration: none;
        }

        .grid-row a:hover {
            text-decoration: underline;
        }

        .grid-list-container {
            overflow-x: auto;
        }
        
        /*button style*/
        .custom-file-input {
    display: none;
}

.custom-file-label {
    display: inline-block;
    background-color: #196BDE;
    color: white;
    padding: 5px 10px;
    font-size: 0.7rem;
    cursor: pointer;
    border-radius: 4px;
    text-align: center;
    transition: background 0.3s ease;

}

.custom-file-label:hover {
    background-color: #2980b9;
}


    </style>
    <title>List of Groups</title>
</head>

<body>

    <?php if (!empty($instance_id_with_constraint)) : ?>
        <script>
            alert('Cannot delete this instance because it is linked to other records.');
        </script>
    <?php endif; ?>

      <div class="about_title" style="margin: 1rem 25% 1rem 25%;">
        <p>
        <h3>List of GROUPS <i class="fa fa-users fa-2xl" aria-hidden="true"></i></h3>
        </p>

        <div class="input-group">
            <a href="create_quot_instance.php"><button type="submit" class="btn2">+ ENROLLMENT [TRAINEES GROUP] <i class="fa fa-users fa-1xl"></i></button></a>
        </div>

        <div class="input-group">
            <a href="quotation_participants.php"><button type="submit" class="btn">Attendance Sheet <i class="fa fa-file fa-1xl"></i></button></a>
        </div>
    </div>

    <!-- ---------- GRID STYLE --------------------- -->

    <div class="grid-list-container">
        <div class="grid-header">
            <div>G. ID</div>
            <div>Group Description</div>
            <div>Customer</div>
            <div>Quotation Ref#</div>
            <div>Start</div>
            <div>Cand.</div>
            <div>Instructor(s)</div>
            <div>Attendance Sheet File</div>
            <div>Upload Sheet</div>
             <div>Action</div>
        </div>

        <?php while ($row = $result->fetch_assoc()) { ?>
            <div class="grid-row">
            <div><a href="group_details.php?id=<?php echo $row['instance_id']; ?>">
        <?php echo "G-" . $row['instance_id']; ?></a></div>
        <div><a href="group_details.php?id=<?php echo $row['instance_id']; ?>">
        <?php echo strtoupper($row['instance_ref']); ?></a></div>
                <div><?php echo strtoupper($row['cust_code']); ?></div>
                <div><?php echo $row['quot_ref']; ?></div>
                <div><?php echo $row['start_date']; ?></div>
                <div style="color:blue; font-weight:600;">[<?php echo $row['participant_count']; ?>]</div>
                <div>
                    <!-- SINGLE AND MULTIPLE INSTRUCTORS SELECTION-->
                    <?php
                    $names_raw = $row['instructor_names'] ?? '';
                    $ids_raw = $row['instructor_ids'] ?? '';
                    $portraits_raw = $row['instructor_portraits'] ?? '';

                    $names = array_map('trim', explode(',', $names_raw));
                    $ids = array_map('trim', explode(',', $ids_raw));
                    $portraits = array_map('trim', explode(',', $portraits_raw));

                    if (count($names) > 0 && count($names) == count($ids) && count($names) == count($portraits) && !empty($names[0])) {
                        foreach ($names as $index => $name) {
                            $id = htmlspecialchars($ids[$index]);
                            $img = htmlspecialchars($portraits[$index]) ?: 'instructor_male.jpg'; // fallback if missing
                            $img_path = 'search/photo_uploads/' . $img;

                            echo '<div style="display:inline-flex;align-items:center;gap:5px;margin-bottom:4px;">';
                            // echo '<img src="' . $img_path . '" alt="portrait" style="width:45px;height:45px;border-radius:50%;object-fit:cover;">';
                             echo '<img src="' . $img_path . '" alt="portrait" style="width: 50px;height: 50px;object-fit: cover;border-radius: 5px;border: 1px solid #ccc;transition: transform 0.2s ease-in-out;box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);">';
                            echo '<a href="search/view_single.php?id=' . $id . '" target="_blank">' . strtoupper(htmlspecialchars($name)) . '</a>';
                            echo '</div>';

                            if ($index < count($names) - 1) {
                                echo '<span style="margin: 0 4px;"></span>';
                            }
                        }
                    } else {
                        echo 'No Instructor Selected!';
                    }
                    ?>

                </div>
                <!--Upload attendance sheet file-->
               <div>
                    <?php if (!empty($row['attendance_sheet'])): ?>
            <a href="uploads/attendance/<?php echo htmlspecialchars($row['attendance_sheet']); ?>" style="display: inline-block; padding: 6px 12px; background-color: green; color: white; text-decoration: none; border-radius: 4px;" target="_blank" style="font-size:0.9rem;color:green;font-weight:600;">View</a>
        <?php endif; ?>
         </div>
          <div>
            <form action="upload_attendance.php" method="post" enctype="multipart/form-data" style="display:flex;flex-direction:column;gap:3px;border: none;padding:5px 10px;">
            <input type="hidden" name="instance_id" value="<?php echo $row['instance_id']; ?>">
              <input type="file" name="attendance_file" id="file_<?php echo $row['instance_id']; ?>" class="custom-file-input" required>
        <label for="file_<?php echo $row['instance_id']; ?>" class="custom-file-label">Select File</label>
            <!-- Show file name in red color-->
        <span class="file-name" id="name_<?php echo $row['instance_id']; ?>" style="font-size: 0.75rem; color: red;"></span>
        <button type="submit" style="font-size:0.7rem; padding:5px">Upload</button>
         </form>
        </div>
                <div>
                    <a href="edit_instance.php?id=<?php echo $row['instance_id']; ?>" style="display: inline-block; padding: 6px 12px; background-color: #0044ff; color: white; text-decoration: none; border-radius: 4px;">Edit</a>

                    <!--<a href="view_instance.php?del_id=<?php //echo $row['instance_id']; ?>" onclick="return confirm('Are you sure you want to delete this instance?');">Delete</a>-->
                </div>
            </div>
        <?php } ?>
    </div>
	<!-- Show selected file name for upload ---->	
    <script>
    document.querySelectorAll('.custom-file-input').forEach(input => {
        input.addEventListener('change', function () {
            const fileName = this.files[0]?.name || '';
            const spanId = 'name_' + this.id.split('_')[1];  // extract instance_id
            const targetSpan = document.getElementById(spanId);
            if (targetSpan) {
                targetSpan.textContent = fileName;
            }
        });
    });
</script>

    <?php include 'resources/footer.php'; ?>