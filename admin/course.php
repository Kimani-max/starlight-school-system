<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

require_once '../includes/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $code = trim($_POST['code']);
    $title = trim($_POST['title']);
    $department_id = $_POST['department_id'];
    $program_id = $_POST['program_id'];
    $year = $_POST['year'];
    $semester = $_POST['semester'];
    $lecturer_id = $_POST['lecturer_id'];

    $stmt = $conn->prepare("INSERT INTO courses (code, title, department_id, program_id, year, semester, lecturer_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssiiiii", $code, $title, $department_id, $program_id, $year, $semester, $lecturer_id);

    if ($stmt->execute()) {
        echo "<p class='success'>Course added successfully.</p>";
    } else {
        echo "<p class='error'>Error: " . $conn->error . "</p>";
    }
    $stmt->close();
}

$departments = $conn->query("SELECT id, name FROM departments");
$programs = $conn->query("SELECT id, name FROM programs");
$lecturers = $conn->query("SELECT id, full_name FROM lecturers");
$courses = $conn->query("SELECT * FROM courses");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Course Management</title>
    <link rel="stylesheet" href="../css/style.css"> <!-- External CSS -->
</head>
<body>

    <h3>Course Management</h3>

    <form method="post" action="">
        <input type="text" name="code" placeholder="Course Code (e.g. BIT101)" required><br>
        <input type="text" name="title" placeholder="Course Title" required><br>

        <label>Department:</label>
        <select name="department_id" required>
            <option value="">-- Select Department --</option>
            <?php while ($row = $departments->fetch_assoc()): ?>
                <option value="<?= $row['id'] ?>"><?= $row['name'] ?></option>
            <?php endwhile; ?>
        </select><br>

        <label>Program:</label>
        <select name="program_id" required>
            <option value="">-- Select Program --</option>
            <?php while ($row = $programs->fetch_assoc()): ?>
                <option value="<?= $row['id'] ?>"><?= $row['name'] ?></option>
            <?php endwhile; ?>
        </select><br>

        <label>Year of Study:</label>
        <select name="year" required>
            <option value="1">Year 1</option>
            <option value="2">Year 2</option>
            <option value="3">Year 3</option>
            <option value="4">Year 4</option>
        </select><br>

        <label>Semester:</label>
        <select name="semester" required>
            <option value="1">Semester 1</option>
            <option value="2">Semester 2</option>
        </select><br>

        <label>Assign Lecturer:</label>
        <select name="lecturer_id" required>
            <option value="">-- Select Lecturer --</option>
            <?php while ($row = $lecturers->fetch_assoc()): ?>
                <option value="<?= $row['id'] ?>"><?= $row['full_name'] ?></option>
            <?php endwhile; ?>
        </select><br>

        <button type="submit">Add Course</button>
    </form>

    <h3>All Courses</h3>
    <table border="1" cellpadding="5">
        <tr>
            <th>Code</th><th>Title</th><th>Year</th><th>Semester</th><th>Program</th><th>Lecturer ID</th>
        </tr>
        <?php while ($row = $courses->fetch_assoc()): ?>
        <tr>
            <td><?= $row['code'] ?></td>
            <td><?= $row['title'] ?></td>
            <td><?= $row['year'] ?></td>
            <td><?= $row['semester'] ?></td>
            <td><?= $row['program_id'] ?></td>
            <td><?= $row['lecturer_id'] ?></td>
        </tr>
        <?php endwhile; ?>
    </table>

    <a href="../dashboards/admin.php">Back to Dashboard</a>

</body>
</html>
