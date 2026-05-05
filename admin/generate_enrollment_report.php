<?php
session_start();
require_once '../includes/db.php';
require_once '../vendor/TCPDF-main/tcpdf.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

// Build query based on role
if ($role === 'admin') {
    $sql = "
        SELECT s.full_name, s.admission_number, c.title, c.code, se.year, se.semester
        FROM enrollments se
        JOIN students s ON se.student_id = s.id
        JOIN courses c ON se.course_id = c.id
        ORDER BY s.full_name, se.year, se.semester
    ";
    $stmt = $conn->prepare($sql);
} elseif ($role === 'teacher') {
    $sql = "
        SELECT s.full_name, s.admission_number, c.title, c.code, se.year, se.semester
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
        SELECT s.full_name, s.admission_number, c.title, c.code, se.year, se.semester
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

// Create PDF
$pdf = new TCPDF();
$pdf->AddPage();
$pdf->SetFont('helvetica', '', 10);

// Title
$pdf->Cell(0, 10, 'Enrollment Report', 0, 1, 'C');
$pdf->Ln(5);

// Table header
$html = '
<table border="1" cellpadding="4">
<tr>
    <th><b>Full Name</b></th>
    <th><b>Admission No</b></th>
    <th><b>Course Title</b></th>
    <th><b>Course Code</b></th>
    <th><b>Year</b></th>
    <th><b>Semester</b></th>
</tr>
';

// Table data
while ($row = $result->fetch_assoc()) {
    $html .= '<tr>
        <td>' . htmlspecialchars($row['full_name']) . '</td>
        <td>' . htmlspecialchars($row['admission_number']) . '</td>
        <td>' . htmlspecialchars($row['title']) . '</td>
        <td>' . htmlspecialchars($row['code']) . '</td>
        <td>' . $row['year'] . '</td>
        <td>' . $row['semester'] . '</td>
    </tr>';
}

$html .= '</table>';

// Write to PDF
$pdf->writeHTML($html, true, false, false, false, '');

// Output
$pdf->Output('enrollment_report.pdf', 'I');
