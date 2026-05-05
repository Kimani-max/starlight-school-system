<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header("Location: ../login.php");
    exit();
}

$teacher_id = $_SESSION['user_id'];
$id = $_GET['id'] ?? null;

// Fetch the result
$stmt = $conn->prepare("SELECT * FROM results WHERE id = ?  AND teacher_id = ?");
$stmt->bind_param("ii", $id, $teacher_id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

if (!$data) {
    echo "Result not found or access denied.";
    exit();
}

// Update if form submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $unit = $_POST['unit'];
    $marks = $_POST['marks'];
    $semester = $_POST['semester'];
    $year = $_POST['year'];

    $stmt = $conn->prepare("UPDATE results SET unit=?, marks=?, semester=?, year=? WHERE id=? AND teacher_id=?");
    $stmt->bind_param("sisiii", $unit, $marks, $semester, $year, $id, $teacher_id);

    if ($stmt->execute()) {
        header("Location: results.php");
        exit();
    } else {
        echo "Update failed.";
    }
}
?>

<h2>Edit Result</h2>
<form method="post">
    Unit: <input type="text" name="unit" value="<?= htmlspecialchars($data['unit']) ?>" required><br>
    Marks: <input type="number" name="marks" value="<?= $data['marks'] ?>" required><br>
    Semester: <input type="text" name="semester" value="<?= htmlspecialchars($data['semester']) ?>" required><br>
    Year: <input type="number" name="year" value="<?= $data['year'] ?>" required><br>
    <button type="submit">Update Result</button>
</form>
<a href="results.php">Back</a>
