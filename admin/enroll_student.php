<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die('Access denied: Only admins can enroll students.');
}
require_once '../includes/db.php';

$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = $_POST['student_id'];
    $course_ids = $_POST['course_ids'] ?? [];
    $year = $_POST['year'];
    $semester = $_POST['semester'];

    foreach ($course_ids as $course_id) {
        // Prevent duplicate enrollment
        $check = $conn->prepare("SELECT id FROM enrollments WHERE student_id = ? AND course_id = ? AND year = ? AND semester = ?");
        $check->bind_param("iiii", $student_id, $course_id, $year, $semester);
        $check->execute();
        $check->store_result();

        if ($check->num_rows === 0) {
            $stmt = $conn->prepare("INSERT INTO enrollments (student_id, course_id, year, semester) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("iiii", $student_id, $course_id, $year, $semester);
            $stmt->execute();
            $stmt->close();
        }

        $check->close();
    }

    $success = "Student enrolled successfully.";
}

// Fetch students
$students = $conn->query("SELECT id, full_name FROM students ORDER BY full_name");

// Fetch programs
$programs = $conn->query("SELECT id, name FROM programs");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Enroll Student</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="enroll-form-container">
        <h2>Enroll Student into Courses</h2>

        <?php if ($success): ?>
            <div class="success-message"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <form method="post" action="">
            <label>Student:</label>
            <select name="student_id" id="student_id" required>
                <option value="">-- Select Student --</option>
                <?php while ($row = $students->fetch_assoc()): ?>
                    <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['full_name']) ?></option>
                <?php endwhile; ?>
            </select><br>

            <label>Program:</label>
            <select name="program_id" id="program_id" required>
                <option value="">-- Select Program --</option>
                <?php while ($row = $programs->fetch_assoc()): ?>
                    <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['name']) ?></option>
                <?php endwhile; ?>
            </select><br>

            <label>Year:</label>
            <select name="year" id="year" required>
                <option value="1">Year 1</option>
                <option value="2">Year 2</option>
                <option value="3">Year 3</option>
                <option value="4">Year 4</option>
            </select><br>

            <label>Semester:</label>
            <select name="semester" id="semester" required>
                <option value="1">Semester 1</option>
                <option value="2">Semester 2</option>
            </select><br>

            <label>Courses:</label>
            <select name="course_ids[]" id="course_list" required></select><br>

            <button type="submit">Enroll Student</button>
        </form>

        <a href="../dashboards/admin.php" class="back-button">Back to Dashboard</a>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    function loadCourses() {
        var program = $('#program_id').val();
        var year = $('#year').val();
        var semester = $('#semester').val();

        if (program && year && semester) {
            $.post('../dashboards/load_courses.php', {
                program_id: program,
                year: year,
                semester: semester
            }, function(data) {
                $('#course_list').html(data);
            });
        }
    }

    $('#program_id, #year, #semester').change(loadCourses);
    </script>
</body>
</html>
