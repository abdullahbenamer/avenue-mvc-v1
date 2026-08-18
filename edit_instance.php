<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['user_name'])) {
    header("Location: login.php");
    exit();
}

include 'resources/header.php';
include 'resources/db_config.php';

// Fetch the instance ID from the URL
if (isset($_GET['id'])) {
    $instance_id = $_GET['id'];

    $instance_query = "SELECT 
        qi.instance_ref, 
        qi.quot_id, 
        qi.cust_id, 
        qi.ven_id, 
        qp.start_date, 
        qp.course_id,
        GROUP_CONCAT(DISTINCT qp.full_name SEPARATOR ', ') AS participant_names,
        GROUP_CONCAT(DISTINCT i.full_name SEPARATOR ' || ') AS instructor_names,
        GROUP_CONCAT(DISTINCT i.inst_id) AS instructor_ids
    FROM quotation_instances qi
    LEFT JOIN quotation_participants qp ON qi.instance_id = qp.instance_id
    LEFT JOIN quotation_instructors qi_inst ON qi.instance_id = qi_inst.instance_id
    LEFT JOIN instructors i ON qi_inst.instructor_id = i.inst_id
    WHERE qi.instance_id = ?
    GROUP BY qi.instance_id";

    $stmt = $conn->prepare($instance_query);
    $stmt->bind_param("i", $instance_id);
    $stmt->execute();
    $instance_result = $stmt->get_result();

    if ($instance_result->num_rows == 0) {
        echo "<script>alert('Instance not found.'); window.location.href = 'view_instance.php';</script>";
        exit();
    }

    $instance = $instance_result->fetch_assoc();
    $participant_names = $instance['participant_names'] ?? '';
    $course_id = $instance['course_id'] ?? null;
} else {
    echo "<script>alert('No instance ID provided.'); window.location.href = 'view_instance.php';</script>";
    exit();
}

$query = "SELECT q.quot_id, q.quot_ref, c.course_title 
          FROM quotations q 
          JOIN courses c ON q.course_id = c.course_id;";
$result = $conn->query($query);

$customer_query = "SELECT * FROM `customers` ORDER BY `cust_name`";
$customer_result = $conn->query($customer_query);

$instructor_query = "SELECT inst_id, full_name, inst_portrait FROM instructors ORDER BY full_name";
$instructor_result = $conn->query($instructor_query);

$venue_query = "SELECT ven_id, ven_name FROM venues ORDER BY ven_name";
$venue_result = $conn->query($venue_query);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $quot_id = $_POST['quot_id'];
    $customer = $_POST['cust_id'];
    $instance_ref = $_POST['instance_ref'];
    $participant_names_raw = $_POST['participant_names'];
    $names = explode(',', $participant_names_raw);
    $instructor_ids = $_POST['instructor_ids'] ?? [];
    $ven_id = $_POST['ven_id'];
    $start_date = $_POST['start_date'];
    $course_id = $_POST['course_id'];

    $update_stmt = $conn->prepare("UPDATE quotation_instances 
        SET quot_id = ?, cust_id = ?, instance_ref = ?, ven_id = ? 
        WHERE instance_id = ?");
    $update_stmt->bind_param("iissi", $quot_id, $customer, $instance_ref, $ven_id, $instance_id);

    $conn->query("DELETE FROM quotation_instructors WHERE instance_id = $instance_id");
    foreach ($instructor_ids as $inst_id) {
        $stmt_inst = $conn->prepare("INSERT INTO quotation_instructors (instance_id, instructor_id) VALUES (?, ?)");
        $stmt_inst->bind_param("ii", $instance_id, $inst_id);
        $stmt_inst->execute();
    }

    if ($update_stmt->execute()) {
        $conn->query("DELETE FROM quotation_participants WHERE instance_id = $instance_id");

        if (isset($_POST['participants']) && is_array($_POST['participants'])) {
            $conn->query("DELETE FROM quotation_participants WHERE instance_id = $instance_id");

            foreach ($_POST['participants'] as $p) {
                $name = trim($p['full_name']);
                $name_a = trim($p['full_name_a'] ?? '');
                $payroll = trim($p['payroll_no'] ?? '');

                if (!empty($name)) {
                    $stmt_participant = $conn->prepare("INSERT INTO quotation_participants 
                        (instance_id, full_name, full_name_a, payroll_no, start_date, course_id, cust_id) 
                        VALUES (?, ?, ?, ?, ?, ?, ?)");
                    $stmt_participant->bind_param("issssii", $instance_id, $name, $name_a, $payroll, $start_date, $course_id, $customer);
                    $stmt_participant->execute();
                }
            }
        }

        echo "<script>alert('Quotation Instance updated successfully'); window.location.href='view_instance.php';</script>";
    } else {
        echo "<script>alert('Error updating Quotation Instance'); window.location.href='edit_instance.php?id=$instance_id';</script>";
    }
}
?>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        .instructor-grid input[type="checkbox"] {
            margin-left: 5px;
            margin-right: 5px;
        }

        .input-group.instructor-checks {
            margin-top: 0.5rem;
        }

        .instructor-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
            gap: 12px;
            padding: 5px 0;
        }

        .instructor-grid label {
            font-size: 0.7rem;
            display: flex;
            align-items: center;
            gap: 5px;
            padding: 5px;
            background-color: #f5f5f5;
            border-radius: 3px;
            border: 1px solid #ccc;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .instructor-grid img {
            width: 48px;
            height: 48px;
            object-fit: cover;
            border-radius: 50%;
            margin-right: 10px;
        }

        .participant-entry input {
            padding: 5px;
            font-size: 0.9rem;
        }

        .remove-participant {
            background: #f33;
            color: white;
            border: none;
            padding: 0 10px;
            cursor: pointer;
            font-size: 1rem;
            border-radius: 5px;
        }

        #add-participant-btn {
            background: #2d8fdd;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
        }

        .participant-entry input[name*="[full_name_a]"] {
            font-size: 1rem;
            /* Increase as needed */
            font-family: 'Noto Naskh Arabic', serif;
            /* Optional: Arabic-friendly font */
            direction: rtl;
            /* Optional: for right-to-left Arabic */
        }
    </style>

    <title>Edit GROUP</title>
</head>

<body>

    <div class="about" style="margin: 1rem 25% 1rem 25%;">
        <p>
            <?php
            echo "You are logged in as <b>" . $_SESSION['user_role'] . "</b> | User: <b>" . $_SESSION['user_name'] . "</b>";
            ?>
        </p>
    </div>

    <div class="about_title" style="margin: 1rem 25% 1rem 25%;">
        <h3>Edit GROUP [# <?php echo $instance_id; ?>] <i class="fa-solid fa-users fa-2xl"></i></h3>
    </div>

    <form method="POST">
        <div class="input-group">
            <label for="quot_id">Quotation:</label>
            <select name="quot_id" id="quot_id" required>
                <option value="">Select Quotation</option>
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <option value="<?php echo $row['quot_id']; ?>" <?php echo ($row['quot_id'] == $instance['quot_id']) ? 'selected' : ''; ?>>
                        <?php echo $row['quot_ref'] . " - " . $row['course_title']; ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <div class="input-group">
            <label>Customer Name:</label>
            <select name="cust_id" required>
                <option value=""></option>
                <?php while ($row = mysqli_fetch_array($customer_result)) { ?>
                    <option value="<?php echo $row['cust_id']; ?>" <?php echo ($row['cust_id'] == $instance['cust_id']) ? 'selected' : ''; ?>>
                        <?php echo strtoupper($row['cust_name']); ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <div class="input-group">
            <label for="course_id">Course Title:</label>
            <select name="course_id" id="course_id" required>
                <option value="">Select Course</option>
                <?php
                $course_query = "SELECT course_id, course_title FROM courses";
                $course_result = $conn->query($course_query);
                while ($row = $course_result->fetch_assoc()) { ?>
                    <option value="<?php echo $row['course_id']; ?>" <?php echo ($row['course_id'] == $course_id) ? 'selected' : ''; ?>>
                        <?php echo $row['course_title']; ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <div class="input-group instructor-checks">
            <label>Instructor(s):</label>
            <div class="instructor-grid">
                <?php
                $selected_ids = explode(',', $instance['instructor_ids'] ?? '');
                mysqli_data_seek($instructor_result, 0);
                while ($row = mysqli_fetch_assoc($instructor_result)) {
                    $checked = in_array($row['inst_id'], $selected_ids) ? 'checked' : '';
                    $portrait = trim($row['inst_portrait']);
                    $portrait = !empty($portrait) ? $portrait : 'instructor_male.jpg';
                    echo '<label>
        <input type="checkbox" name="instructor_ids[]" value="' . $row['inst_id'] . '" ' . $checked . '> 
        <img src="search/photo_uploads/' . $portrait . '" alt="portrait"> ' . htmlspecialchars(strtoupper($row['full_name'])) . '
    </label>';
                }

                ?>
            </div>
            <small>Select one or more instructors</small>
        </div>

        <div class="input-group">
            <label for="ven_id">Venue:</label>
            <select name="ven_id" id="ven_id" required>
                <option value="">Select Venue</option>
                <?php while ($row = mysqli_fetch_array($venue_result)) { ?>
                    <option value="<?php echo $row['ven_id']; ?>" <?php echo ($row['ven_id'] == $instance['ven_id']) ? 'selected' : ''; ?>>
                        <?php echo $row['ven_name']; ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <div class="input-group">
            <label for="instance_ref">GROUP Description:</label>
            <input type="text" name="instance_ref" id="instance_ref" value="<?php echo htmlspecialchars($instance['instance_ref']); ?>" required>
        </div>

        <div class="input-group">
            <label for="participant_names">Participant Name(s)</label>

            <?php
            //Replaced textarea with this loop to allow editing multiple participants
            $participant_query = "SELECT * FROM quotation_participants WHERE instance_id = ?";
            $stmt_p = $conn->prepare($participant_query);
            $stmt_p->bind_param("i", $instance_id);
            $stmt_p->execute();
            $participant_result = $stmt_p->get_result();
            ?>
            <div class="input-group">
                   <label>Participants:</label>
                <div id="participants-container">
                    <?php
                    $i = 0;
                    while ($p = $participant_result->fetch_assoc()) {
                    ?>
                        <div class="participant-entry" data-index="<?php echo $i; ?>" style="margin-bottom: 10px; display: flex; gap: 5px;">
                            <input type="hidden" name="participants[<?php echo $i; ?>][old_part_id]" value="<?php echo $p['part_id']; ?>">
                            <input type="text" name="participants[<?php echo $i; ?>][full_name]" placeholder="Full Name" value="<?php echo htmlspecialchars($p['full_name']); ?>" required>
                            <input type="text" name="participants[<?php echo $i; ?>][full_name_a]" placeholder="Arabic Name" value="<?php echo htmlspecialchars($p['full_name_a'] ?? ''); ?>">
                            <input type="text" name="participants[<?php echo $i; ?>][payroll_no]" placeholder="Payroll No" value="<?php echo htmlspecialchars($p['payroll_no'] ?? ''); ?>">
                            <button type="button" class="remove-participant">❌</button>
                        </div>

                    <?php $i++;
                    } ?>
                </div>
                <button type="button" id="add-participant-btn" style="margin-top:10px;">➕ Add Participant</button>
            </div>
        </div>
        <div class="input-group">
            <label for="start_date">Start Date:</label>
            <input type="date" name="start_date" id="start_date" value="<?php echo htmlspecialchars($instance['start_date']); ?>" required>
        </div>

        <br>
        <button type="submit" class="btn btn-primary">Update GROUP</button>
        <a href="view_instance.php" class="btn btn-secondary">Cancel Editing</a>
    </form>

    <script>
        // Handle insert new participant rows.
        document.addEventListener('DOMContentLoaded', () => {
            let participantIndex = <?php echo $i; ?>;

            const container = document.getElementById('participants-container');
            const addBtn = document.getElementById('add-participant-btn');

            addBtn.addEventListener('click', () => {
                const entry = document.createElement('div');
                entry.className = 'participant-entry';
                entry.setAttribute('data-index', participantIndex);
                entry.style.cssText = 'margin-bottom:10px; display:flex; gap:5px;';

                entry.innerHTML = `
            <input type="text" name="participants[${participantIndex}][full_name]" placeholder="Full Name" required>
            <input type="text" name="participants[${participantIndex}][full_name_a]" placeholder="Arabic Name">
            <input type="text" name="participants[${participantIndex}][payroll_no]" placeholder="Payroll No">
            <button type="button" class="remove-participant">❌</button>
        `;

                container.appendChild(entry);
                participantIndex++;
            });

            container.addEventListener('click', function(e) {
                if (e.target.classList.contains('remove-participant')) {
                    e.target.parentElement.remove();
                }
            });
        });
    </script>


    <?php include 'resources/footer.php'; ?>