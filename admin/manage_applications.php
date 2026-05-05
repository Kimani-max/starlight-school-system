<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['application_id'], $_POST['status'])) {
    $id = (int)$_POST['application_id'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("UPDATE applications SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $id);
    $stmt->execute();
    $stmt->close();

    $_SESSION['message'] = "Application status updated.";
    header("Location: manage_applications.php");
    exit();
}
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/PHPMailer.php';
require '../PHPMailer/SMTP.php';
require '../PHPMailer/Exception.php';

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'starlight.noreply9@gmail.com';
    $mail->Password   = 'mystarlight@004';
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587;

    $mail->setFrom('starlight.noreply9@gmail.com', 'Starlight Institute');
    $mail->addAddress($email, $full_name);

    $mail->isHTML(true);
    $mail->Subject = 'Admission Status Update';
    $mail->Body    = "
        <p>Dear {$full_name},</p>
        <p>Your application status has been updated to: <strong>{$status}</strong>.</p>
        <p>Thank you for applying to <strong>Starlight Institute</strong>.</p>
    ";

    $mail->send();
} catch (Exception $e) {
    error_log("Email not sent: {$mail->ErrorInfo}");
}


// Fetch applications
$result = $conn->query("SELECT * FROM applications ORDER BY submitted_at DESC");
$applications = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Applications - Admin Dashboard</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body class="dashboard-body">

<header class="school-top-header">
    <img src="../logo.png" alt="Starlight Logo" class="school-logo">
    <span class="school-name">Starlight Institute</span>
</header>

<div class="dashboard-layout">
    <aside class="sidebar">
        <h2 class="sidebar-title">Admin Panel</h2>
        <nav class="sidebar-nav">
            <a href="../dashboards/admin.php">Dashboard</a>
            <a href="students.php">Manage Student</a>
            <a href="course.php">Manage Courses</a>
            <a href="manage_applications.php" class="active">Applications</a>
            <a href="export_application.php">Export Applications in Excel</a>
            <a href="../auth/logout.php" class="logout-link">Logout</a>
        </nav>
    </aside>

    <main class="dashboard-content">
        <header class="dashboard-header">
            <h1>Manage Applications</h1>
        </header>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="success-message"><?= htmlspecialchars($_SESSION['message']) ?></div>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>

        <section class="table-section">
            <table class="styled-table">
                <thead>
                    <tr>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Program</th>
                        <th>Status</th>
                        <th>Submitted At</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($applications)): ?>
                        <?php foreach ($applications as $app): ?>
                            <tr>
                                <td><?= htmlspecialchars($app['full_name']) ?></td>
                                <td><?= htmlspecialchars($app['email']) ?></td>
                                <td><?= htmlspecialchars($app['phone']) ?></td>
                                <td><?= htmlspecialchars($app['program']) ?></td>
                                <td><?= htmlspecialchars($app['status']) ?></td>
                                <td><?= htmlspecialchars($app['submitted_at']) ?></td>
                                <td>
                                    <form method="post" style="display: inline-flex; gap: 50px;">
                                        <input type="hidden" name="application_id" value="<?= $app['id'] ?>">
                                        <select name="status" required>
                                            <option value="Pending" <?= $app['status'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
                                            <option value="Admitted" <?= $app['status'] == 'Admitted' ? 'selected' : '' ?>>Admitted</option>
                                            <option value="Rejected" <?= $app['status'] == 'Rejected' ? 'selected' : '' ?>>Rejected</option>
                                        </select>
                                        <button type="submit">Update</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="7">No applications found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>
    </main>
</div>

</body>
</html>
