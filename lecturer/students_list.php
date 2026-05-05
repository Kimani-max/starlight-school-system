<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'lecturer') {
    header("Location: ../login.php");
    exit();
}

$lecturer_id = $_SESSION['user_id'];

// Get students enrolled in lecturer's assigned courses
$query = "
    SELECT DISTINCT s.id, s.full_name, c.code, c.title
    FROM enrollments e
    JOIN students s ON e.student_id = s.id
    JOIN courses c ON e.course_id = c.id
    WHERE c.lecturer_id = 13
";
$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Student List</title>
  <link rel="stylesheet" href="../css/style.css">
</head>
<body class="dashboard-body">

<header class="school-top-header">
  <img src="../logo.png" alt="Starlight Institute Logo" class="school-logo">
  <span class="school-name">Starlight Institute</span>
</header>

<div class="dashboard-layout">
  <aside class="sidebar">
    <h2 class="sidebar-title">Lecturer Panel</h2>
    <nav class="sidebar-nav">
      <a href="../dashboards/lecturers.php">Dashboard</a>
      <a href="assigned_courses.php">My Courses</a>
      <a href="students_list.php" class="active">Student List</a>
      <a href="results.php">Manage Results</a>
      <a href="../auth/logout.php" class="logout-link">Logout</a>
    </nav>
  </aside>

  <main class="dashboard-content">
    <header class="dashboard-header">
      <h1>Students in your Courses</h1>
    </header>

    <section class="dashboard-main">
      <table class="students-table">
        <thead>
          <tr>
            <th>Student Name</th>
            <th>Course Code</th>
            <th>Course</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($row = $result->fetch_assoc()): ?>
          <tr>
            <td><?= htmlspecialchars($row['full_name']) ?></td>
            <td><?= htmlspecialchars($row['code']) ?></td>
            <td><?= htmlspecialchars($row['title']) ?></td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </section>
  </main>
</div>

</body>
</html>
