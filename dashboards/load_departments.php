<?php
include '../includes/db.php'; // adjust path as needed

if (isset($_POST['faculty_id'])) {
    $faculty_id = intval($_POST['faculty_id']);
    $query = $conn->query("SELECT id, name FROM departments WHERE faculty_id = $faculty_id");

    echo "<option value=''>-- Select Department --</option>";
    while ($row = $query->fetch_assoc()) {
        echo "<option value='{$row['id']}'>{$row['name']}</option>";
    }
}
