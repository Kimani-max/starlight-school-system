<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    die("Unauthorized access.");
}

$student_id = $_SESSION['user_id'];
$course_id = $_POST['course_id'];
$year = $_POST['year'];
$semester = $_POST['semester'];

// Check for duplicate
$stmt = $conn->prepare("SELECT id FROM enrollments WHERE student_id = ? AND course_id = ? AND year = ? AND semester = ?");
$stmt->bind_param("iiii", $student_id, $course_id, $year, $semester);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo "You have already enrolled in this course for the selected year and semester.";
} else {
    // Insert new enrollment
    $stmt->close();
    $stmt = $conn->prepare("INSERT INTO enrollments (student_id, course_id, year, semester) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiii", $student_id, $course_id, $year, $semester);

    if ($stmt->execute()) {
        echo "Enrollment successful!";
    } else {
        echo "Error enrolling: " . $stmt->error;
    }
}

$stmt->close();
$conn->close();
?>
