<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="register-body">
    <div class="register-container">
        <h2 class="register-title">Register</h2>
        <form action="register.php" method="POST" class="register-form">
            <input type="text" name="username" placeholder="Username" required> <br>
            <input type="email" name="email" placeholder="Email" required> <br>
            <input type="password" name="password" placeholder="Password" required> <br>

            <label for="role">Role:</label>
            <select name="role" required>
                <option value="student">Student</option>
                <option value="lecturer">Lecturer</option>
                <option value="admin">Admin</option>
            </select> <br>

            <button type="submit">Register</button>
            <p>Already have an account? <a href="login.php">Login</a></p>
        </form>
    </div>
</body>
</html>


    <?php 
    //handle form submission
    if($_SERVER["REQUEST_METHOD"] == "POST") {
        require_once "includes/db.php";

        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);
        $role = $_POST['role'];

        //insert user
        $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ? ,?)");
        $stmt->bind_param("ssss", $username, $email, $password, $role);

        if($stmt->execute()){
            echo"<p>Registration successful You can now login<a href='../auth/login.php'></a>.</p>";
        } else {
            echo"<p>Error<p>";
        }
        $stmt->close();
        $conn->close();
    }
    ?>
    
</body>
</html>