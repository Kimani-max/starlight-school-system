<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}
require_once '../includes/db.php';

// Fetch faculties
$faculties = $conn->query("SELECT id, name FROM faculties");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Enrollment Report</title>
    <link rel="stylesheet" href="../css/style.css"> <!-- Adjust path if needed -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
<div class="container">
    <h2>Enrollment Report</h2>

    <form id="reportForm" method="post">
        <label>Faculty:</label>
        <select id="faculty">
            <option value="">-- Select Faculty --</option>
            <?php while ($f = $faculties->fetch_assoc()): ?>
                <option value="<?= $f['id'] ?>"><?= htmlspecialchars($f['name']) ?></option>
            <?php endwhile; ?>
        </select><br>

        <label>Department:</label>
        <select id="department">
            <option value="">-- Select Department --</option>
        </select><br>

        <label>Program:</label>
        <select id="program">
            <option value="">-- Select Program --</option>
        </select><br>

        <label>Course:</label>
        <select id="course">
            <option value="">-- Select Course --</option>
        </select><br>

        <label>Year:</label>
        <select id="year">
            <option value="1">Year 1</option>
            <option value="2">Year 2</option>
            <option value="3">Year 3</option>
            <option value="4">Year 4</option>
        </select><br>

        <label>Semester:</label>
        <select id="semester">
            <option value="1">Semester 1</option>
            <option value="2">Semester 2</option>
        </select><br>

        <button type="button" id="generate">Generate Report</button>
    </form>

    <div id="reportResult"></div>
</div>

<script>
    $('#faculty').change(function () {
        let facultyID = $(this).val();
        $.post('../dashboards/load_departments.php', {faculty_id: facultyID}, function (data) {
            $('#department').html(data);
            $('#program').html('<option value="">-- Select Program --</option>');
            $('#course').html('<option value="">-- Select Course --</option>');
        });
    });

    $('#department').change(function () {
        let deptID = $(this).val();
        $.post('../dashboards/load_programs.php', {department_id: deptID}, function (data) {
            $('#program').html(data);
            $('#course').html('<option value="">-- Select Course --</option>');
        });
    });

    $('#program').change(function () {
        let progID = $(this).val();
        if (progID) {
            $.post('../dashboards/load_courses.php', {program_id: progID}, function (data) {
                $('#course').html(data);
            });
        } else {
            $('#course').html('<option value="">-- Select Course --</option>');
        }
    });

    $('#generate').click(function () {
        let course_id = $('#course').val();
        let year = $('#year').val();
        let semester = $('#semester').val();

        if (course_id && year && semester) {
            $.post('fetch_enrollment_report.php', {
                course_id: course_id,
                year: year,
                semester: semester
            }, function (data) {
                $('#reportResult').html(data);
            });
        } else {
            alert("Please fill all required fields for report.");
        }
    });
</script>
</body>
</html>
