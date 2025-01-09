<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'organizer') {
    header("Location: login.php");
    exit();
}
include_once 'db_connect.php';

// Handle event creation
$success_message = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create_event'])) {
    $event_name = $_POST['event_name'];
    $event_date = $_POST['event_date'];
    $event_location = $_POST['event_location'];
    $event_description = $_POST['event_description'];
    $event_capacity = $_POST['event_capacity'];

    // Handle file upload
    $image_path = "";
    if (isset($_FILES['event_image']) && $_FILES['event_image']['error'] == 0) {
        $uploads_dir = '../uploads/';
        $image_path = $uploads_dir . basename($_FILES['event_image']['name']);
        if (!file_exists($uploads_dir)) {
            mkdir($uploads_dir, 0777, true); // Create directory if it doesn't exist
        }
        if (move_uploaded_file($_FILES['event_image']['tmp_name'], $image_path)) {
            $success_message = "Image uploaded successfully!";
        } else {
            $success_message = "Failed to upload image.";
        }
    }

    // Insert event details including image path and capacity
    $sql = "INSERT INTO events (name, date, location, description, organizer_id, image_path, capacity) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssi", $event_name, $event_date, $event_location, $event_description, $_SESSION['user_id'], $image_path, $event_capacity);

    if ($stmt->execute()) {
        $success_message = "Event created successfully!";
    } else {
        $success_message = "Error: " . $stmt->error;
    }
    $stmt->close();
}

// Fetch unread notifications
$sql = "SELECT * FROM notifications ORDER BY created_at DESC";
$result = $conn->query($sql);
$notifications = $result->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Event</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h1>Create Event</h1>
        
        <?php if ($success_message): ?>
        <div class="alert alert-success">
            <?php echo $success_message; ?>
        </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="event_name">Event Name</label>
                <input type="text" name="event_name" id="event_name" class="form-control" placeholder="Event Name" required>
            </div>
            <div class="form-group">
                <label for="event_date">Event Date</label>
                <input type="date" name="event_date" id="event_date" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="event_location">Event Location</label>
                <input type="text" name="event_location" id="event_location" class="form-control" placeholder="Event Location" required>
            </div>
            <div class="form-group">
                <label for="event_description">Event Description</label>
                <textarea name="event_description" id="event_description" class="form-control" placeholder="Event Description" required></textarea>
            </div>
            <div class="form-group">
                <label for="event_image">Event Image</label>
                <input type="file" name="event_image" id="event_image" class="form-control">
            </div>
            <div class="form-group">
                <label for="event_capacity">Event Capacity</label>
                <input type="number" name="event_capacity" id="event_capacity" class="form-control" placeholder="Event Capacity" required>
            </div>
            <button type="submit" name="create_event" class="btn btn-primary">Create Event</button>
        </form>
        <a href="organizer_dashboard.php" class="btn btn-secondary mt-3">Back to Dashboard</a>
    </div>
</body>
</html>
