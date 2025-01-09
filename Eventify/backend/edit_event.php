<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'organizer') {
    header("Location: login.php");
    exit();
}
include_once 'db_connect.php';

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Get event details
if (isset($_POST['event_id'])) {
    $event_id = $_POST['event_id'];
    $sql = "SELECT * FROM events WHERE id = ? AND organizer_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $event_id, $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $event = $result->fetch_assoc();
    $stmt->close();
} else {
    header("Location: organizer_dashboard.php");
    exit();
}

// Handle event update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_event'])) {
    $event_name = $_POST['event_name'];
    $event_date = $_POST['event_date'];
    $event_location = $_POST['event_location'];
    $event_description = $_POST['event_description'];

    // Handle file upload
    $image_path = $event['image_path'];
    if (isset($_FILES['event_image']) && $_FILES['event_image']['error'] == 0) {
        $uploads_dir = '../uploads/';
        $image_path = $uploads_dir . basename($_FILES['event_image']['name']);
        if (!file_exists($uploads_dir)) {
            mkdir($uploads_dir, 0777, true); // Create directory if it doesn't exist
        }
        if (!move_uploaded_file($_FILES['event_image']['tmp_name'], $image_path)) {
            $image_path = $event['image_path']; // Revert to old image path if upload fails
        }
    }

    // Update event details
    $sql = "UPDATE events SET name = ?, date = ?, location = ?, description = ?, image_path = ? WHERE id = ? AND organizer_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssi", $event_name, $event_date, $event_location, $event_description, $image_path, $event_id, $_SESSION['user_id']);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Event updated successfully!";
    } else {
        $_SESSION['message'] = "Error updating event: " . $stmt->error;
        error_log("Error updating event: " . $stmt->error);
    }
    $stmt->close();

    header("Location: organizer_dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Event</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h1>Edit Event</h1>
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="event_name">Event Name</label>
                <input type="text" name="event_name" id="event_name" class="form-control" value="<?php echo htmlspecialchars($event['name']); ?>" required>
            </div>
            <div class="form-group">
                <label for="event_date">Event Date</label>
                <input type="date" name="event_date" id="event_date" class="form-control" value="<?php echo $event['date']; ?>" required>
            </div>
            <div class="form-group">
                <label for="event_location">Event Location</label>
                <input type="text" name="event_location" id="event_location" class="form-control" value="<?php echo htmlspecialchars($event['location']); ?>" required>
            </div>
            <div class="form-group">
                <label for="event_description">Event Description</label>
                <textarea name="event_description" id="event_description" class="form-control" required><?php echo htmlspecialchars($event['description']); ?></textarea>
            </div>
            <div class="form-group"> 
                <label for="event_capacity">Event Capacity</label> 
                <input type="number" name="event_capacity" id="event_capacity" class="form-control" value="<?php echo $event['capacity']; ?>" required> </div>
            <div class="form-group">
                <label for="event_image">Event Image</label>
                <input type="file" name="event_image" id="event_image" class="form-control">
            </div>
            <input type="hidden" name="event_id" value="<?php echo $event['id']; ?>">
            <button type="submit" name="update_event" class="btn btn-primary">Update Event</button>
         

        </form>
        <a href="organizer_dashboard.php" class="btn btn-secondary mt-3">Back to Dashboard</a>
    </div>
</body>
</html>
