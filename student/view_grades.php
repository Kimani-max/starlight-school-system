<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../login.php");
    exit();
}

$student_id = $_SESSION['user_id'];

// Fetch results for the student
$sql = "
    SELECT r.marks, r.semester, r.year, r.created_at, c.code, c.title
    FROM results r
    JOIN courses c ON r.course_id = c.id
    WHERE r.student_id = 3
    ORDER BY r.year DESC, r.semester ASC, c.title ASC
";

$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Results</title>
  <link rel="stylesheet" href="../css/style.css">
</head>
<body class="dashboard-body">

<header class="school-top-header">
  <img src="../logo.png" alt="Starlight Institute Logo" class="school-logo">
  <span class="school-name">Starlight Institute</span>
</header>

<div class="dashboard-layout">
  <aside class="sidebar">
    <h2 class="sidebar-title">Student Panel</h2>
    <nav class="sidebar-nav">
      <a href="../dashboards/student.php">Dashboard</a>
      <a href="my_courses.php">My Courses</a>
      <a href="view_results.php" class="active">My Results</a>
      <a href="../auth/logout.php" class="logout-link">Logout</a>
    </nav>
  </aside>

  <main class="dashboard-content">
    <header class="dashboard-header">
      <h1>My Results</h1>
    </header>

    <section class="table-section">
      <table class="styled-table">
        <thead>
          <tr>
            <th>Course Code</th>
            <th>Course Title</th>
            <th>Marks</th>
            <th>Semester</th>
            <th>Year</th>
            <th>Recorded On</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
              <tr>
                <td><?= htmlspecialchars($row['code']) ?></td>
                <td><?= htmlspecialchars($row['title']) ?></td>
                <td><?= $row['marks'] ?></td>
                <td><?= htmlspecialchars($row['semester']) ?></td>
                <td><?= $row['year'] ?></td>
                <td><?= $row['created_at'] ?></td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr><td colspan="6">No results available yet.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </section>
  </main>
</div>
</body>
</html>
