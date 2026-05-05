<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die('Access denied.');
}

require_once '../includes/db.php';

$student_id = $_GET['id'] ?? null;
if (!$student_id) {
    die('Student ID is required.');
}

$success = '';
$error = '';

// Fetch student data
$stmt = $conn->prepare("SELECT * FROM students WHERE id = ?");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();
$stmt->close();

if (!$student) {
    die('Student not found.');
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = $_POST['full_name'];
    $admission_number = $_POST['admission_number'];
    $email = $_POST['email'];

    $update = $conn->prepare("UPDATE students SET full_name = ?, admission_number = ?, email = ? WHERE id = ?");
    $update->bind_param("sssi", $full_name, $admission_number, $email, $student_id);

    if ($update->execute()) {
        $success = "Student updated successfully.";
        // Refresh data after update
        $student['full_name'] = $full_name;
        $student['admission_number'] = $admission_number;
        $student['email'] = $email;
    } else {
        $error = "Failed to update student.";
    }
    $update->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Student</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body class="dashboard-body">
    <div class="dashboard-container">
        <header class="dashboard-header">
            <h1>Edit Student</h1>
        </header>

        <main class="dashboard-main">
            <?php if ($success): ?>
                <div class="success-message"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="error-message"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="post">
                <label>Full Name:</label><br>
                <input type="text" name="full_name" value="<?= htmlspecialchars($student['full_name']) ?>" required><br>

                <label>Admission Number:</label><br>
                <input type="text" name="admission_number" value="<?= htmlspecialchars($student['admission_number']) ?>" required><br>

                <label>Email:</label><br>
                <input type="email" name="email" value="<?= htmlspecialchars($student['email']) ?>" required><br>

                <button type="submit">Update Student</button>
                <a href="view_students.php" class="button">Cancel</a>
            </form>
        </main>
    </div>
</body>
</html>
