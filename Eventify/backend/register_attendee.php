<?php
// Start session and include database connection
session_start();
require_once 'db_connect.php';

$registration_success = false;

// Handle Registration
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    $sql = "INSERT INTO attendees (name, email, password) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $name, $email, $password);

    if ($stmt->execute()) {
        $registration_success = true;
    } else {
        echo "<script>alert('Error: " . $stmt->error . "');</script>";
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
    <div id="login-container" class="wrapper" style="display: none;">
        <div class="card">
            <div class="title">
                <h1 class="title title-large">Sign In</h1>
                <p class="title title-subs">Don't have an account? <span><a href="#" id="open-register" class="linktext">Create an account</a></span></p>
            </div>
            <form class="form" method="POST" action="login.php">
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

    <div id="register-container" class="wrapper">
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
                <?php if ($registration_success): ?>
                    <input type="hidden" id="registration-success" value="true">
                <?php endif; ?>
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

    backToLogin[_{{{CITATION{{{_1{](https://github.com/ATufekovic/WEB_projekt/tree/b625f563e952f13bf39900ba4f7521ffe7abcbcb/web%2FuserRegister.php)[_{{{CITATION{{{_2{](https://github.com/simumhasansabbir/sql_project/tree/e56ca9739ff80f66a81395dc67ff7a13b1016d46/index.php)