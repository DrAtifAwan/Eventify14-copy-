<?php
session_start();
require_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    if ($role === 'organizer') {
        $sql = "SELECT * FROM organizers WHERE email = ?";
    } elseif ($role === 'attendee') {
        $sql = "SELECT * FROM attendees WHERE email = ?";
    } else {
        echo "<script>alert('Invalid role selected');</script>";
        exit();
    }

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['role'] = $role;
            if ($role === 'organizer') {
                header("Location: organizer_dashboard.php");
            } elseif ($role === 'attendee') {
                header("Location: attendee_dashboard.php");
            }
            exit();
        } else {
            echo "<script>alert('Incorrect password');</script>";
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
    <title>Login</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../frontend/style.css">
</head>
<body>
<div class="container">
    <h1>Login</h1>
    <form method="POST">
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" class="input-field" placeholder="Email address" required>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" name="password" id="password" class="input-field" placeholder="Password" required>
        </div>
        <div class="form-group">
            <label for="role">Role</label>
            <select name="role" id="role" class="input-field" required>
                <option value="organizer">Organizer</option>
                <option value="attendee">Attendee</option>
            </select>
        </div>
        <div class="form-group">
            <input type="submit" name="action" class="input-submit" value="login">
        </div>
    </form>
</div>
</body>
</html>
