<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['user_id']) || !isset($_POST['id'])) {
    echo "❌ Invalid request.";
    exit();
}

$enrollment_id = $_POST['id'];

// Only allow admin or teacher to delete (you can modify this to restrict more)
if ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'teacher') {
    $stmt = $conn->prepare("DELETE FROM enrollments WHERE id = ?");
    $stmt->bind_param("i", $enrollment_id);

    if ($stmt->execute()) {
        echo "✅ Enrollment successfully removed.";
    } else {
        echo "❌ Error: " . $conn->error;
    }
    $stmt->close();
} else {
    echo "❌ Unauthorized.";
}
?>
