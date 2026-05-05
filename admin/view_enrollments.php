<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$role = $_SESSION['role'];
$user_id = $_SESSION['user_id'];

if ($role === 'admin') {
    $sql = "
        SELECT s.full_name, s.admission_number, c.title, c.code, se.year, se.semester, se.id
        FROM enrollments se
        JOIN students s ON se.student_id = s.id
        JOIN courses c ON se.course_id = c.id
        ORDER BY s.full_name, se.year, se.semester
    ";
    $stmt = $conn->prepare($sql);
} elseif ($role === 'teacher') {
    $sql = "
        SELECT s.full_name, s.admission_number, c.title, c.code, se.year, se.semester, se.id
        FROM enrollments se
        JOIN students s ON se.student_id = s.id
        JOIN courses c ON se.course_id = c.id
        WHERE c.teacher_id = ?
        ORDER BY s.full_name, se.year, se.semester
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
} else {
    $sql = "
        SELECT s.full_name, s.admission_number, c.title, c.code, se.year, se.semester, se.id
        FROM enrollments se
        JOIN students s ON se.student_id = s.id
        JOIN courses c ON se.course_id = c.id
        WHERE se.student_id = ?
        ORDER BY s.full_name, se.year, se.semester
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Enrollments</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body class="dashboard_body">
<div class="dashboard_container">
    <header class="dashboard_header">
        <h1>Course Enrollments</h1>
    </header>

    <main class="dashboard_main">
        <table class="styled-table">
            <thead>
                <tr>
                    <th>Full Name</th>
                    <th>Admission No</th>
                    <th>Course Title</th>
                    <th>Course Code</th>
                    <th>Year</th>
                    <th>Semester</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['full_name']) ?></td>
                    <td><?= htmlspecialchars($row['admission_number']) ?></td>
                    <td><?= htmlspecialchars($row['title']) ?></td>
                    <td><?= htmlspecialchars($row['code']) ?></td>
                    <td><?= $row['year'] ?></td>
                    <td><?= $row['semester'] ?></td>
                    <td>
                        <button class="btn-edit-btn" 
                                data-id="<?= $row['id'] ?>" 
                                data-year="<?= $row['year'] ?>" 
                                data-semester="<?= $row['semester'] ?>">
                            Edit
                        </button>
                        <button class="btn-unenroll-enroll" data-id="<?= $row['id'] ?>">Unenroll</button>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="7">⚠ No enrollments found.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>

        <div class="enrollment-actions">
            <a href="generate_enrollment_report.php" target="_blank" class="btn-enrollment-download">📄 Download PDF</a> 
            <a href="../dashboards/admin.php" class="btn-enrollment-back">← Back to Dashboard</a>
        </div>

        <div id="message" class="message"></div>
    </main>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).on('click', '.btn-unenroll-enroll', function() {
    let id = $(this).data('id');
    if (confirm("Are you sure you want to unenroll?")) {
        $.post('unenroll.php', { id: id }, function(response) {
            $('#message').html(response);
            location.reload();
        });
    }
});

$(document).on('click', '.btn-edit-btn', function() {
    let id = $(this).data('id');
    let currentYear = $(this).data('year');
    let currentSemester = $(this).data('semester');

    let newYear = prompt("Enter new year (1-4):", currentYear);
    let newSemester = prompt("Enter new semester (1-2):", currentSemester);

    if (newYear && newSemester) {
        $.post('edit_enrollment.php', {
            id: id,
            year: newYear,
            semester: newSemester
        }, function(response) {
            $('#message').html(response);
            location.reload();
        });
    }
});
</script>
</body>
</html>
