<?php
session_start();
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="../css/style.css">
</head>
<body class="dashboard-body">
<!-- Top School Header -->
<header class="school-top-header">
  <img src="../logo.png" alt="Starlight Institute Logo" class="school-logo">
  <span class="school-name">Starlight Institute</span>
</header>
  <div class="dashboard-layout">
    
    <!-- Sidebar Navigation -->
    <aside class="sidebar">
      <h2 class="sidebar-title">Admin Panel</h2>
      <nav class="sidebar-nav">
        <a href="../admin/enroll_student.php">Enroll Student</a>
        <a href="../admin/students.php">Manage Students</a>
        <a href="../admin/lecturer.php">Manage Lecturers</a>
        <a href="../admin/view_students.php">View Students</a>
        <a href="../admin/course.php">Manage Courses</a>
        <a href="../admin/view_enrollments.php">View Enrolled Students</a>
        <a href="../admin/enrollment_report.php">Generate Report</a>
        <a href="../admin/manage_applications.php" class="active">Applications</a>
        <a href="../admin/export_application.php" class="btn-export">Export applications to Excel</a>
        <a href="../auth/logout.php" class="logout-link">Logout</a>
      </nav>
    </aside>

    <!-- Main Content -->
    <main class="dashboard-content">
      <header class="dashboard-header">
        <h1>Welcome, <?php echo $_SESSION['name']; ?> (Admin)</h1>
      </header>

      <section class="dashboard-main">
        <h2>Dashboard Overview</h2>
        <p>This is your control panel. Use the sidebar to manage the system.</p>
      </section>
    </main>
    
  </div>
</body>
</html>


