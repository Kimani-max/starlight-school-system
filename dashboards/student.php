<?php
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit();
}
include '../includes/db.php'; // adjust path if needed

$student = null; 

$student_id = $_SESSION['user_id'];

$query = "SELECT * FROM students WHERE id = '3'";
$result = mysqli_query($conn, $query);

if(mysqli_num_rows($result) > 0){
    $student = mysqli_fetch_assoc($result);
} else {
    echo "Student not found";
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="../css/style.css"> <!-- adjust path if needed -->
</head>
<body>
    <header class="school-top-header">
  <img src="../logo.png" alt="Starlight Institute Logo" class="school-logo">
  <span class="school-name">Starlight Institute</span>
</header>

<div class="dashboard-layout">
  <aside class="sidebar">
    <h2 class="sidebar-title">Student Panel</h2>
    <nav class="sidebar-nav">
      <div class="sidebar-profile">
      <img src="../profile_icon.png" alt="Profile Icon" class="profile-icon" onclick="openProfile()">
       </div>

      <a href="student.php" class="active">Dashboard</a>
      <a href="../student/my_courses.php">My Courses</a>
      <a href="../student/view_grades.php">My Results</a>
      <a href="../auth/logout.php" class="logout-link">Logout</a>
    </nav>
  </aside>

  <main class="dashboard-content">
    <header class="dashboard-header">
      <h1>Welcome, <?= htmlspecialchars($_SESSION['name']) ?>!</h1>
    </header>
    
    <section class="dashboard-main">
      <p>This is your student dashboard where you can view your enrolled courses, results, and more.</p>
    </section>

<div id="profileModal" class="modal-overlay" onclick="closeProfile()">
  <div class="modal-box" onclick="event.stopPropagation()">

    <span class="close-btn" onclick="closeProfile()">&times;</span>

    <img src="../profile_icon.png" class="modal-profile-img">

    <h2><?= htmlspecialchars($_SESSION['name']) ?></h2>

    <p><strong>Reg No:</strong> <?= htmlspecialchars($student['admission_number']) ?></p>
    <p><strong>Email:</strong> <?= htmlspecialchars($student['email']) ?></p>
    <p><strong>National ID:</strong> <?= htmlspecialchars($student['national_id']) ?></p>
    <p><strong>Phone:</strong> <?= htmlspecialchars($student['phone']) ?></p>

  </div>
</div>

  </main>
</div>

<script>
function openProfile(){
  document.getElementById("profileModal").style.display = "flex";
}

function closeProfile(){
  document.getElementById("profileModal").style.display = "none";
}
</script>

</body>
</html>
