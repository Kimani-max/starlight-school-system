<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'lecturer') {
    header("Location: ../login.php");
    exit();
}

require_once '../includes/db.php';

$lecturer_id = (int)$_SESSION['user_id'];
$name = $_SESSION['name'];
$message = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = (int)$_POST['student_id'];
    $course_id = (int)$_POST['course_id'];
    $marks = (int)$_POST['marks'];
    $grade = trim($_POST['grade']);
    $semester = trim($_POST['semester']);
    $year = (int)$_POST['year'];

    $stmt = $conn->prepare("INSERT INTO results (student_id, course_id, marks, grade, semester, year) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iiissi", $student_id, $course_id, $marks, $grade, $semester, $year);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Results added successfully.";
        header("Location: results.php");
        exit();
    }
    $stmt->close();
}

// Get students enrolled in lecturer's courses
$students = [];
$student_sql = "
    SELECT DISTINCT s.id, s.full_name
    FROM enrollments e
    JOIN students s ON e.student_id = s.id
    JOIN courses c ON e.course_id = c.id
    WHERE c.lecturer_id = 13
";
$stmt = $conn->prepare($student_sql);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $students[] = $row;
}
$stmt->close();

// Get courses taught by lecturer
$courses = [];
$stmt = $conn->prepare("SELECT id, title FROM courses WHERE lecturer_id = 13");
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $courses[] = $row;
}
$stmt->close();

// Get results entered by lecturer
$results = [];
$sql = "
    SELECT r.marks, r.grade, r.semester, r.year, r.created_at, s.full_name, c.title
    FROM results r
    JOIN students s ON r.student_id = s.id
    JOIN courses c ON r.course_id = c.id
    WHERE c.lecturer_id = 13
    ORDER BY r.created_at ASC
";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $results[] = $row;
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Lecturer - Manage Results</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body class="dashboard-body">
<header class="school-top-header">
  <img src="../logo.png" alt="Starlight Institute Logo" class="school-logo">
  <span class="school-name">Starlight Institute</span>
</header>

<div class="dashboard-layout">
  <aside class="sidebar">
    <h2 class="sidebar-title">Lecturer Panel</h2>
    <nav class="sidebar-nav">
        <a href="../dashboards/lecturers.php">Dashboard</a>
      <a href="../lecturer/assigned_courses.php">My Courses</a>
      <a href="../lecturer/students_list.php">Student List</a>
      <a href="../dashboards/results.php" class="active">Manage Results</a>
      <a href="../auth/logout.php" class="logout-link">Logout</a>
    </nav>
  </aside>

    <main class="dashboard-content">

        <?php if ($message): ?>
            <div class="info-message"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <section class="form-section">
            <h2>Add Result</h2>
            <form method="post" class="styled-form">
                <div class="form-group">
                    <label>Student:</label>
                    <select name="student_id" required>
                        <option value="">-- Select Student --</option>
                        <?php foreach ($students as $s): ?>
                            <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['full_name']) ?></option>
                        <?php endforeach; ?>
                        <?php if (empty($students)): ?>
                            <option disabled>No students found</option>
                        <?php endif; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Course:</label>
                    <select name="course_id" required>
                        <option value="">-- Select Course --</option>
                        <?php foreach ($courses as $c): ?>
                            <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['title']) ?></option>
                        <?php endforeach; ?>
                        <?php if (empty($courses)): ?>
                            <option disabled>No courses found</option>
                        <?php endif; ?>
                    </select>
                </div>

                <div class="form-group">
                    <input type="number" name="marks" placeholder="Marks (0-100)" required>
                </div>
                <div class="form-group">
                    <input type="text" name="grade" placeholder="Grade (A-E)" required>
                </div>
                <div class="form-group">
                    <input type="text" name="semester" placeholder="Semester (e.g., Semester 1)" required>
                </div>
                <div class="form-group">
                    <input type="number" name="year" placeholder="Year (e.g., 2025)" required>
                </div>

                <button type="submit" class="btn-primary">Submit Result</button>
            </form>
        </section>

        <section class="table-section">
            <h2>Submitted Results</h2>
            <table class="styled-table">
                <thead>
                    <tr>
                        <th>Student Name</th>
                        <th>Course</th>
                        <th>Marks</th>
                        <th>Grade</th>
                        <th>Semester</th>
                        <th>Year</th>
                        <th>Submitted At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($results)): ?>
                        <?php foreach ($results as $r): ?>
                            <tr>
                                <td><?= htmlspecialchars($r['full_name']) ?></td>
                                <td><?= htmlspecialchars($r['title']) ?></td>
                                <td><?= $r['marks'] ?></td>
                                <td><?= htmlspecialchars($r['grade']) ?></td>
                                <td><?= htmlspecialchars($r['semester']) ?></td>
                                <td><?= $r['year'] ?></td>
                                <td><?= $r['created_at'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="6">No results submitted yet.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>
    </main>
</div>
</body>
</html>
