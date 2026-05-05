<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die('Access denied.');
}
require_once '../includes/db.php';

$enrollment_id = $_GET['id'] ?? null;
if (!$enrollment_id) {
    die('Enrollment ID is required.');
}

// Confirm deletion
$stmt = $conn->prepare("DELETE FROM enrollments WHERE id = ?");
$stmt->bind_param("i", $enrollment_id);
if ($stmt->execute()) {
    header("Location: enroll_student.php?deleted=1");
    exit();
} else {
    echo "Failed to delete enrollment.";
}
$stmt->close();
?>
