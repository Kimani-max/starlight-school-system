<?php
session_start();
require_once '../includes/db.php'; // Adjust path as needed

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'lecturer') {
    header("Location: ../login.php");
    exit();
}

$lecturer_id = $_SESSION['user_id'];

$query = "
    SELECT c.code, c.title, d.name 
    FROM courses c
    JOIN departments d ON c.department_id = d.id
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
  <title>Assigned Courses</title>
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
      <a href="assigned_courses.php" class="active">My Courses</a>
      <a href="students_list.php">Student List</a>
      <a href="results.php">Manage Results</a>
      <a href="../auth/logout.php" class="logout-link">Logout</a>
    </nav>
  </aside>

  <main class="dashboard-content">
    <header class="dashboard-header">
      <h1>Assigned Courses</h1>
    </header>

    <section class="dashboard-main">
      <table class="courses-table">
        <thead>
          <tr>
            <th>Course Code</th>
            <th>Course Title</th>
            <th>Department</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($row = $result->fetch_assoc()): ?>
          <tr>
            <td><?= htmlspecialchars($row['code']) ?></td>
            <td><?= htmlspecialchars($row['title']) ?></td>
            <td><?= htmlspecialchars($row['name']) ?></td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </section>
  </main>
</div>

</body>
</html>
