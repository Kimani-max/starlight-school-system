<?php
require_once '../includes/db.php';

if (isset($_POST['program_id']) && !empty($_POST['program_id'])) {
    $program_id = $_POST['program_id'];

    $stmt = $conn->prepare("SELECT id, title FROM courses WHERE program_id = ?");
    $stmt->bind_param("i", $program_id);
    $stmt->execute();
    $result = $stmt->get_result();

    echo "<option value=''>-- Select Course --</option>";
    while ($row = $result->fetch_assoc()) {
        echo "<option value='{$row['id']}'>{$row['title']}</option>";
    }
    $stmt->close();
} else {
    echo "<option value=''>No program selected</option>";
}
?>
