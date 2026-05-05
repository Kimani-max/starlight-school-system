<?php 
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    exit();
}
require_once '../includes/db.php';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Basic validation
    if (
        isset($_POST['full_name'], $_POST['email'], $_POST['national_id'], $_POST['admission_number'], $_POST['phone'],
              $_POST['course_id'], $_POST['faculty_id'], $_POST['department_id'], $_POST['program_id'],
              $_POST['year'], $_POST['semester'], $_POST['mode_of_study'], $_POST['password'])
    ) {
        $full_name = trim($_POST['full_name']);
        $email = trim($_POST['email']);
        $national_id = trim($_POST['national_id']);
        $admission_number = trim($_POST['admission_number']);
        $phone = trim($_POST['phone']);

        // Cast numeric values to integers
        $course_id = (int) $_POST['course_id'];
        $faculty_id = (int) $_POST['faculty_id'];
        $department_id = (int) $_POST['department_id'];
        $program_id = (int) $_POST['program_id'];
        $year = (int) $_POST['year'];
        $semester = (int) $_POST['semester'];

        $mode_of_study = trim($_POST['mode_of_study']);
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

        // Prepare and execute insert query
        $stmt = $conn->prepare("INSERT INTO students (
            full_name, email, national_id, phone, admission_number,
            course_id, faculty_id, department_id, program_id,
            year, semester, mode_of_study, password
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        if ($stmt) {
            $stmt->bind_param(
                "sssssiiiissss",
                $full_name, $email, $national_id, $phone, $admission_number,
                $course_id, $faculty_id, $department_id, $program_id,
                $year, $semester, $mode_of_study, $password
            );

            if ($stmt->execute()) {
                $message = "✅ Student added successfully.";
            } else {
                $message = "❌ Error inserting student: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $message = "❌ Failed to prepare statement: " . $conn->error;
        }
    } else {
        $message = "❌ Please fill in all required fields.";
    }
}

// Load students and faculties
$students = $conn->query("SELECT * FROM students");
$faculties = $conn->query("SELECT id, name FROM faculties");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Students</title>
  <link rel="stylesheet" href="../css/style.css">
</head>
<body class="dashboard-body">
<div class="dashboard-layout">
  
  <!-- Sidebar -->
  <aside class="sidebar">
    <h2 class="sidebar-title">Admin Panel</h2>
    <nav class="sidebar-nav">
      <a href="../dashboards/admin.php">Dashboard</a>
      <a href="students.php" class="active">Manage Students</a>
      <a href="lecturer.php">Manage Lecturers</a>
      <a href="../auth/logout.php" class="logout-link">Logout</a>
    </nav>
  </aside>

  <!-- Main content -->
  <main class="dashboard-content">
    <header class="dashboard-header">
      <h1>Manage Students</h1>
    </header>

    <?php if (isset($message)): ?>
      <div class="info-message"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <!-- Student Form -->
    <form method="post" action="students.php" class="styled-form">
      <div class="form-group"><input type="text" name="full_name" placeholder="Full Name" required></div>
      <div class="form-group"><input type="text" name="admission_number" placeholder="Admission Number" required></div>
      <div class="form-group"><input type="text" name="national_id" placeholder="National ID/Passport" required></div>
      <div class="form-group"><input type="email" name="email" placeholder="Email" required></div>
      <div class="form-group"><input type="text" name="phone" placeholder="Phone: +254...."></div>

      <div class="form-group">
        <label>Faculty:</label>
        <select name="faculty_id" id="faculty" required>
          <option value="">-- Select Faculty --</option>
          <?php while($row = $faculties->fetch_assoc()): ?>
            <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['name']) ?></option>
          <?php endwhile; ?>
        </select>
      </div>

      <div class="form-group">
        <label>Department:</label>
        <select name="department_id" id="department" required>
          <option value="">-- Select Department --</option>
        </select>
      </div>

      <div class="form-group">
        <label>Program:</label>
        <select name="program_id" id="program_id" required>
          <option value="">-- Select Program --</option>
        </select>
      </div>

      <div class="form-group">
        <label>Year:</label>
        <select id="year" name="year" required>
          <option value="1">Year 1</option>
          <option value="2">Year 2</option>
          <option value="3">Year 3</option>
          <option value="4">Year 4</option>
        </select>
      </div>

      <div class="form-group">
        <label>Semester:</label>
        <select id="semester" name="semester" required>
          <option value="1">Semester 1</option>
          <option value="2">Semester 2</option>
        </select>
      </div>

      <div class="form-group">
        <label>Course:</label>
        <select id="course_id" name="course_id" required>
          <option value="">-- Select Course --</option>
        </select>
      </div>

      <div class="form-group">
        <label>Mode of Study:</label>
        <select name="mode_of_study" required>
          <option value="Full-time">Full-time</option>
          <option value="Part-time">Part-time</option>
          <option value="Distance">Distance Learning</option>
        </select>
      </div>

      <div class="form-group"><input type="password" name="password" placeholder="Password" required></div>
      <button type="submit" class="btn-primary">Register Student</button>
    </form>

    <!-- Student Table -->
    <h2>Registered Students</h2>
    <table class="styled-table">
      <thead>
        <tr>
          <th>Full Name</th>
          <th>Email</th>
          <th>Admission No</th>
          <th>National ID</th>
          <th>Phone</th>
          <th>Course</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = $students->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($row['full_name']) ?></td>
          <td><?= htmlspecialchars($row['email']) ?></td>
          <td><?= htmlspecialchars($row['admission_number']) ?></td>
          <td><?= htmlspecialchars($row['national_id']) ?></td>
          <td><?= htmlspecialchars($row['phone']) ?></td>
          <td><?= htmlspecialchars($row['course_id']) ?></td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>

  </main>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$('#faculty').change(function() {
  var facultyID = $(this).val();
  $.post('load_departments.php', { faculty_id: facultyID }, function(data) {
    $('#department').html(data);
    $('#program_id').html('<option value="">-- Select Program --</option>');
  });
});

$('#department').change(function() {
  var departmentID = $(this).val();
  $.post('load_programs.php', { department_id: departmentID }, function(data) {
    $('#program_id').html(data);
  });
});

function loadCourses() {
  let program = $('#program_id').val();
  let year = $('#year').val();
  let semester = $('#semester').val();

  if (program && year && semester) {
    $.post('load_courses.php', {
      program_id: program,
      year: year,
      semester: semester
    }, function(data) {
      $('#course_id').html(data);
    });
  }
}
$('#program_id, #year, #semester').change(loadCourses);
</script>
</body>
</html>  