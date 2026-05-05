<?php
session_start();
require_once '../includes/db.php';

$student_id = $_SESSION['user_id'];

$query = "
    SELECT e.id, c.title , e.year, e.semester
    FROM enrollments e
    JOIN courses c ON e.course_id = c.id
    WHERE e.student_id = ?
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo "<table border='1' cellpadding='5'><tr><th>Course</th><th>Year</th><th>Semester</th><th>Action</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$row['title']}</td>
                <td>{$row['year']}</td>
                <td>{$row['semester']}</td>
                <td>
                    <button class='unenroll' data-id='{$row['id']}'>Unenroll</button>
                    <button class='edit' data-id='{$row['id']}'>Edit</button>
                </td>
              </tr>";
    }
    echo "</table>";
} else {
    echo "⚠ No enrollments found.";
}
$stmt->close();
?>
