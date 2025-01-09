<?php
session_start(); // Start a session to manage user state
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Management System</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="frontend/style.css">
</head>
<body>
    <header>
        <h1>Welcome to the Event Management System</h1>
    </header>

    <div class="container">
        <h2>About the System</h2>
        <p>Our Event Management System simplifies the process of organizing and managing events. Whether you're an organizer or an attendee, this platform provides all the tools you need to ensure seamless event coordination. Register today and make event management effortless!</p>

        <h2>Choose Your Role</h2>
        <div class="roles">
            <div class="role-card">
                <h3>Organizer</h3>
                <p>Create and manage your events. Keep track of attendees and their RSVPs with ease.</p>
                <a href="backend/organizer_form.php">Get Started as Organizer</a>
            </div>
            <div class="role-card">
                <h3>Attendee</h3>
                <p>Browse and RSVP to events. Stay updated with the latest event details.</p>
                <a href="backend/attendee_form.php">Get Started as Attendee</a>
            </div>
        </div>
    </div>

    <footer>
        <p>&copy; 2024 Event Management System. All rights reserved.</p>
    </footer>

    <script src="frontend/script.js"></script>
</body>
</html>
