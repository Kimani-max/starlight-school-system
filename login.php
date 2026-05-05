<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login Page</title>

  <link rel="stylesheet" href="css/style.css">
</head>
<body class="login-body">
   <div class="login-container">
    <h2>LOGIN PAGE</h2>
    <form action="login.php" method="post" class="login-form">
        <label for="email">Email</label>
        <input type="text" id="email" name="email" required> <br>
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required> <br>
        <button type="submit">Login</button> <hr>
        <p>Don't have an account?<a href="register.php">Sign Up here</a></p>
    </form>
   </div>
</body>
</html>

<?php
session_start();
require_once 'includes/db.php';

if($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $name, $hashedpassword, $role);
        $stmt->fetch();

        if(password_verify($password, $hashedpassword)) {
            $_SESSION['user_id'] = $id;
            $_SESSION['name'] = $name;
            $_SESSION['role'] = $role;

            //Redirect based on role
            if($role ==='admin') {
                header("Location: dashboards/admin.php");
            }elseif ($role === 'lecturer') {
                header("Location: dashboards/lecturers.php");
            } else {
                header("Location: dashboards/student.php");
            }
            exit();

        } else{
            echo"Invalid password.";
        } 
    } else{
            echo"User not found.";
    }
    $stmt->close();
}
?>

