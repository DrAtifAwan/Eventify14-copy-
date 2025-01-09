<?php
// Start session and include database connection
session_start();
require_once 'db_connect.php';

// Handle Registration
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    $sql = "INSERT INTO attendees (name, email, password) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $name, $email, $password);

    if ($stmt->execute()) {
        echo "<script>alert('Registration successful!'); window.location.href = 'login.php';</script>";
    } else {
        echo "<script>alert('Error: " . $stmt->error . "');</script>";
    }
    $stmt->close();
}

// Handle Login
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $email = $_POST['login_email'];
    $password = $_POST['login_password'];

    $sql = "SELECT * FROM attendees WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['role'] = 'attendee';
            echo "<script>alert('Login successful!'); window.location.href = 'attendee_dashboard.php';</script>";
        } else {
            echo "<script>alert('Invalid password');</script>";
        }
    } else {
        echo "<script>alert('No user found with this email');</script>";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendee Registration/Login</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../frontend/style.css">
</head>
<body>
<div class="container">
    <h1>Attendee Registration/Login</h1>
    <div id="login-container" class="wrapper">
        <div class="card">
            <div class="title">
                <h1 class="title title-large">Sign In</h1>
                <p class="title title-subs">Don't have an account? <span><a href="#" id="open-register" class="linktext">Create an account</a></span></p>
            </div>
            <form class="form" method="POST">
                <div class="form-group">
                    <input type="email" name="login_email" id="login_email" class="input-field" placeholder="Email address" required>
                </div>
                <div class="form-group">
                    <input type="password" name="login_password" id="login_password" class="input-field" placeholder="Password" required>
                </div>
                <div class="form-group">
                    <input type="submit" name="login" class="input-submit" value="Login">
                </div>
            </form>
        </div>
    </div>

    <div id="register-container" class="wrapper" style="display: none;">
        <div class="card">
            <div class="title">
                <h1 class="title title-large">Register</h1>
                <p class="title title-subs">Already have an account? <span><a href="#" id="back-to-login" class="linktext">Sign in</a></span></p>
            </div>
            <form class="form" method="POST">
                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input type="text" name="name" id="name" class="input-field" placeholder="Enter Your Name" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" class="input-field" placeholder="Email address" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" class="input-field" placeholder="Password" required>
                </div>
                <div class="form-group">
                    <input type="submit" name="register" class="input-submit" value="Register">
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const loginContainer = document.getElementById("login-container");
    const registerContainer = document.getElementById("register-container");
    const openRegister = document.getElementById("open-register");
    const backToLogin = document.getElementById("back-to-login");

    openRegister.addEventListener("click", (e) => {
        e.preventDefault();
        loginContainer.style.display = "none";
        registerContainer.style.display = "block";
    });

    backToLogin.addEventListener("click", (e) => {
        e.preventDefault();
        registerContainer.style.display = "none";
        loginContainer.style.display = "block";
    });
</script>
</body>
</html>
