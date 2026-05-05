<?php
session_start();
if ($_SESSION['role'] !== 'admin') {
    die('Access denied: Only admins can edit/unenroll.');
}

require_once '../includes/db.php';

if (isset($_POST['id'], $_POST['year'], $_POST['semester'])) {
    $id = $_POST['id'];
    $year = $_POST['year'];
    $semester = $_POST['semester'];
    $student_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("UPDATE enrollments SET year = ?, semester = ? WHERE id = ? AND student_id = ?");
    $stmt->bind_param("iiii", $year, $semester, $id, $student_id);
    if ($stmt->execute()) {
        echo "✅ Enrollment updated.";
    } else {
        echo "❌ Update failed.";
    }
    $stmt->close();
}
?>
