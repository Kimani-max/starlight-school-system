<?php
// admin_review.php
session_start();
require_once '../includes/db.php';

// Fetch all applications
$stmt = $conn->prepare("SELECT * FROM applications ORDER BY created_at DESC");
$stmt->execute();
$result = $stmt->get_result();

// Update status if form submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $id = (int)$_POST['application_id'];
    $new_status = $_POST['status'];
    $update = $conn->prepare("UPDATE applications SET status = ? WHERE id = ?");
    $update->bind_param("si", $new_status, $id);
    $update->execute();
    header("Location: admin_review.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Review Applications</title>
  <link rel="stylesheet" href="../css/style.css">
</head>
<body class="dashboard-body">
  <header class="school-top-header">
    <img src="../logo.png" class="school-logo" alt="Logo">
    <span class="school-name">Starlight Institute - Admissions Admin</span>
    <a href="../index.php" class="top-link">Home</a>
  </header>

  <div class="dashboard-layout">
    <main class="dashboard-content">
      <h1>Application Review</h1>
      <a href="export_applications.php" class="btn-primary">Export CSV</a>

      <table class="styled-table">
        <thead>
          <tr>
            <th>Full Name</th>
            <th>Email</th>
            <th>Program</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
              <td><?= htmlspecialchars($row['full_name']) ?></td>
              <td><?= htmlspecialchars($row['email']) ?></td>
              <td><?= htmlspecialchars($row['program']) ?></td>
              <td><?= htmlspecialchars($row['status']) ?></td>
              <td>
                <form method="post" class="inline-form">
                  <input type="hidden" name="application_id" value="<?= $row['id'] ?>">
                  <select name="status">
                    <option value="pending" <?= $row['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="admitted" <?= $row['status'] == 'admitted' ? 'selected' : '' ?>>Admitted</option>
                    <option value="rejected" <?= $row['status'] == 'rejected' ? 'selected' : '' ?>>Rejected</option>
                  </select>
                  <button type="submit" name="update_status">Update</button>
                </form>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </main>
  </div>
</body>
</html>
