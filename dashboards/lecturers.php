<?php
session_start();

include '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'lecturer') {
    header("Location: ../login.php");
    exit();
}

// ---------------- FETCH LECTURER PROFILE ----------------
$lecturer = null;

$lecturer_id = $_SESSION['user_id'];  // matches your login session

$query = "SELECT * FROM lecturers WHERE id = '13'";
$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) > 0) {
    $lecturer = mysqli_fetch_assoc($result);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Lecturer Dashboard</title>
  <link rel="stylesheet" href="../css/style.css">

  <style>
    .profile-box{
        text-align:center;
        padding:15px 0;
    }

    .profile-icon{
        width:60px;
        height:60px;
        border-radius:50%;
        cursor:pointer;
    }

    .profile-panel{
        display:none;
        background:#ffffff;
        padding:15px;
        margin:20px;
        border-radius:10px;
        box-shadow:0 0 10px rgba(0,0,0,0.1);
    }
  </style>
</head>

<body class="dashboard-body">

  <!-- Top School Header -->
  <header class="school-top-header">
    <img src="../logo.png" alt="Starlight Institute Logo" class="school-logo">
    <span class="school-name">Starlight Institute</span>
  </header>

  <div class="dashboard-layout">
    
    <!-- Sidebar -->
    <aside class="sidebar">

      <h2 class="sidebar-title">Lecturer Panel</h2>

      <!-- Profile Icon -->
      <div class="profile-box">
        <img src="../profile_icon.png" 
             alt="Profile" 
             class="profile-icon" 
             id="profileToggle">
      </div>

      <nav class="sidebar-nav">
        <a href="lecturers.php">Dashboard</a>
        <a href="../lecturer/assigned_courses.php">My Courses</a>
        <a href="../lecturer/students_list.php">Student List</a>
        <a href="../lecturer/results.php">Manage Results</a>
        <a href="../auth/logout.php" class="logout-link">Logout</a>
      </nav>
    </aside>


    <!-- Main Content -->
    <main class="dashboard-content">
      <header class="dashboard-header">
        <h1>Welcome, <?= htmlspecialchars($_SESSION['name']); ?> (Lecturer)</h1>
      </header>

      <section class="dashboard-main">
        <h2>Dashboard Overview</h2>
        <p>You can view your assigned courses, access student lists, and manage results.</p>
      </section>

      <!-- Profile Panel -->
      <div id="profilePanel" class="profile-panel">
        <?php if($lecturer): ?>
            <h3><?= htmlspecialchars($lecturer['full_name']); ?></h3>
            <p><strong>Email:</strong> <?= htmlspecialchars($lecturer['email']); ?></p>
            <p><strong>Phone:</strong> <?= htmlspecialchars($lecturer['phone']); ?></p>
            <p><strong>National ID:</strong> <?= htmlspecialchars($lecturer['national_id']); ?></p>
            <p><strong>Lec Code:</strong> <?= htmlspecialchars($lecturer['lecturer_code']); ?></p>
        <?php else: ?>
            <p>Profile not found</p>
        <?php endif; ?>
      </div>

    </main>

  </div>

  <script>
    document.getElementById('profileToggle').addEventListener('click', function(){
        const panel = document.getElementById('profilePanel');
        panel.style.display = (panel.style.display === 'block') ? 'none' : 'block';
    });
  </script>

</body>
</html>
