<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

require_once '../includes/db.php';

// Fetch all students
$result = $conn->query("SELECT * FROM students ORDER BY id ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Students</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body class="dashboard-body">
<div class="dashboard-container">
    <header class="dashboard-header">
        <h1>All Registered Students</h1>
    </header>

    <main class="dashboard-main">
        <table class="styled-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Admission Number</th>
                    <th>Email</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['full_name']) ?></td>
                    <td><?= htmlspecialchars($row['admission_number']) ?></td>
                    <td><?= htmlspecialchars($row['email']) ?></td>
                    <td>
                        <a href="edit_students.php?id=<?= $row['id'] ?>" class="edit-btn">Edit</a>
                        <a href="delete_students.php?id=<?= $row['id'] ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this student?')">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <div class="actions">
            <a href="../dashboards/admin.php" class="btn">← Back to Dashboard</a>
            <a href="../auth/logout.php" class="btn logout">Logout</a>
        </div>
    </main>
</div>
</body>
</html>
