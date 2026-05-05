<?php
require_once '../includes/db.php';

$course_id = $_POST['course_id'];
$year = $_POST['year'];
$semester = $_POST['semester'];

$sql = "
    SELECT s.full_name, s.admission_number, s.email, se.year, se.semester
    FROM enrollments se
    JOIN students s ON s.id = se.student_id
    WHERE se.course_id = ? AND se.year = ? AND se.semester = ?
    ORDER BY s.full_name
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("iii", $course_id, $year, $semester);
$stmt->execute();
$result = $stmt->get_result();

echo "<h3>Enrollment Report</h3>";
echo "<table border='1'><tr><th>Name</th><th>Admission No</th><th>Email</th><th>Year</th><th>Semester</th></tr>";

while ($row = $result->fetch_assoc()) {
    echo "<tr>
        <td>{$row['full_name']}</td>
        <td>{$row['admission_number']}</td>
        <td>{$row['email']}</td>
        <td>{$row['year']}</td>
        <td>{$row['semester']}</td>
    </tr>";
}
echo "</table>";
$stmt->close();
