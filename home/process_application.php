<?php
require_once '../includes/db.php'; // adjust path as necessary

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $program = trim($_POST['program']);
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    $address = trim($_POST['address']);

    // Basic validation
    if (empty($full_name) || empty($email) || empty($phone) || empty($program)) {
        header("Location: apply.php?error=missing_fields");
        exit();
    }

    // Prepare and insert
    $stmt = $conn->prepare("INSERT INTO applications (full_name, email, phone, program, dob, gender, address) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $full_name, $email, $phone, $program, $dob, $gender, $address);

    if ($stmt->execute()) {
        header("Location: thankyou.php");
    } else {
        echo "Error: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
} else {
    header("Location: apply.php");
    exit();
}
