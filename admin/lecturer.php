<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

require_once '../includes/db.php';

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $national_id = trim($_POST['national_id']);
    $phone = trim($_POST['phone']);
    $faculty_id = $_POST['faculty_id'];
    $department_id = $_POST['department_id'];
    $lecturer_code = strtoupper(trim($_POST['lecturer_code']));
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $course_id = isset($_POST['course_id']) && $_POST['course_id'] !== "" ? $_POST['course_id'] : null;

    $check = $conn->prepare("SELECT id FROM lecturers WHERE lecturer_code = ?");
    $check->bind_param("s", $lecturer_code);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $message = "Error: Lecturer code '$lecturer_code' already exists.";
    } else {
        $stmt = $conn->prepare("INSERT INTO lecturers (full_name, email, national_id, phone, faculty_id, department_id, lecturer_code, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssiiss", $full_name, $email, $national_id, $phone, $faculty_id, $department_id, $lecturer_code, $password);

        if ($stmt->execute()) {
            $lecturer_id = $stmt->insert_id;

            // Assign selected course
            if ($course_id) {
                $update = $conn->prepare("UPDATE courses SET lecturer_id = ? WHERE id = ?");
                $update->bind_param("ii", $lecturer_id, $course_id);
                $update->execute();
                $update->close();
            }

            $message = "Lecturer registered successfully.";
        } else {
            $message = "Error on insert: " . $conn->error;
        }
        $stmt->close();
    }
    $check->close();
}

$faculties = $conn->query("SELECT id, name FROM faculties");
$courses = $conn->query("SELECT id, code, title FROM courses ");

$lecturers = $conn->query("
    SELECT l.*, f.name AS faculty_name, d.name AS department_name,
           c.code AS course_code, c.title AS course_title
    FROM lecturers l
    LEFT JOIN faculties f ON l.faculty_id = f.id
    LEFT JOIN departments d ON l.department_id = d.id
    LEFT JOIN courses c ON c.lecturer_id = l.id
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Register Lecturer</title>
  <link rel="stylesheet" href="../css/style.css">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="dashboard-body">
<div class="dashboard-layout">

  <!-- Sidebar -->
  <aside class="sidebar">
    <h2 class="sidebar-title">Admin Panel</h2>
    <nav class="sidebar-nav">
      <a href="../dashboards/admin.php">Dashboard</a>
      <a href="students.php">Manage Students</a>
      <a href="lecturer.php" class="active">Manage Lecturers</a>
      <a href="../auth/logout.php" class="logout-link">Logout</a>
    </nav>
  </aside>

  <!-- Main Content -->
  <main class="dashboard-content">
    <header class="dashboard-header">
      <h1>Register Lecturer</h1>
    </header>

    <?php if ($message): ?>
      <div class="info-message"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <!-- Lecturer Registration Form -->
    <form method="post" action="lecturer.php" class="styled-form">
      <div class="form-group"><input type="text" name="full_name" placeholder="Full Name" required></div>
      <div class="form-group"><input type="email" name="email" placeholder="Email" required></div>
      <div class="form-group"><input type="text" name="national_id" placeholder="National ID" required></div>
      <div class="form-group"><input type="text" name="phone" placeholder="Phone: +254..." required></div>

      <div class="form-group">
        <label>Faculty:</label>
        <select name="faculty_id" id="faculty" required>
          <option value="">-- Select Faculty --</option>
          <?php while ($row = $faculties->fetch_assoc()): ?>
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
        <label>Assign Course (optional):</label>
        <select name="course_id">
          <option value="">-- Select Course --</option>
          <?php while ($course = $courses->fetch_assoc()): ?>
            <option value="<?= $course['id'] ?>">
              <?= htmlspecialchars($course['code'] . ' - ' . $course['title']) ?>
            </option>
          <?php endwhile; ?>
        </select>
      </div>

      <div class="form-group"><input type="text" name="lecturer_code" placeholder="Lecturer Code (e.g. LEC001)" required></div>
      <div class="form-group"><input type="password" name="password" placeholder="Password" required></div>

      <button type="submit" class="btn-primary">Register Lecturer</button>
    </form>

    <!-- Lecturer Table -->
    <h2>Registered Lecturers</h2>
    <table class="styled-table">
      <thead>
        <tr>
          <th>Name</th>
          <th>Email</th>
          <th>Phone</th>
          <th>Faculty</th>
          <th>Department</th>
          <th>Code</th>
          <th>Assigned Course</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = $lecturers->fetch_assoc()): ?>
          <tr>
            <td><?= htmlspecialchars($row['full_name']) ?></td>
            <td><?= htmlspecialchars($row['email']) ?></td>
            <td><?= htmlspecialchars($row['phone']) ?></td>
            <td><?= htmlspecialchars($row['faculty_name']) ?></td>
            <td><?= htmlspecialchars($row['department_name']) ?></td>
            <td><?= htmlspecialchars($row['lecturer_code']) ?></td>
            <td>
              <?php if ($row['course_code']): ?>
                <?= htmlspecialchars($row['course_code']) ?> - <?= htmlspecialchars($row['course_title']) ?>
              <?php else: ?>
                <em>None</em>
              <?php endif; ?>
            </td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </main>
</div>

<!-- Load Departments Dynamically -->
<script>
  $('#faculty').change(function () {
    var facultyID = $(this).val();
    $.post('load_departments.php', { faculty_id: facultyID }, function (data) {
      $('#department').html(data);
    });
  });
</script>
</body>
</html>
