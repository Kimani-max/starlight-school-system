<?php
require_once '../includes/db.php';

if (isset($_POST['department_id'])) {
    $department_id = $_POST['department_id'];
    
    // Debug log
    error_log("Received department_id: $department_id");

    $stmt = $conn->prepare("SELECT id, name FROM programs WHERE department_id = ?");
    $stmt->bind_param("i", $department_id);
    $stmt->execute();
    $result = $stmt->get_result();

    echo '<option value="">-- Select Program --</option>';
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<option value='{$row['id']}'>{$row['name']}</option>";
        }
    } else {
        echo "<option value=''>No programs found</option>";
    }

    $stmt->close();
} else {
    echo "<option value=''>No department selected</option>";
}
?>
