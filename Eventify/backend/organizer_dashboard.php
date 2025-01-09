<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'organizer') {
    header("Location: login.php");
    exit();
}
include_once 'db_connect.php';

// Fetch success message
$success_message = isset($_SESSION['message']) ? $_SESSION['message'] : ""; 
unset($_SESSION['message']);

// Handle search query
$search_query = "";
if (isset($_GET['search'])) {
    $search_query = $_GET['search'];
    $sql = "SELECT * FROM events WHERE name LIKE ? OR location LIKE ? OR date LIKE ?";
    $stmt = $conn->prepare($sql);
    $search_param = "%" . $search_query . "%";
    $stmt->bind_param("sss", $search_param, $search_param, $search_param);
    $stmt->execute();
    $result = $stmt->get_result();
    $events = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
} else {
    // Fetch all events
    $sql = "SELECT * FROM events";
    $result = $conn->query($sql);
    $events = $result->fetch_all(MYSQLI_ASSOC);
}

// Handle event creation
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create_event'])) {
    $event_name = $_POST['event_name'];
    $event_date = $_POST['event_date'];
    $event_location = $_POST['event_location'];
    $event_description = $_POST['event_description'];
    $event_capacity = $_POST['event_capacity']; // Added this line

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

    // Insert event details including capacity and image path
    $sql = "INSERT INTO events (name, date, location, description, organizer_id, image_path, capacity) VALUES (?, ?, ?, ?, ?, ?, ?)"; // Modified SQL
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
    <title>Organizer Dashboard</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <style>
        .hidden {
            display: none;
        }
        .highlight-green {
            color: green;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Welcome, Organizer <?php echo $_SESSION['user_name']; ?>!</h1>
        <p>This is your dashboard.</p>

        <?php if ($success_message): ?>
        <div class="alert alert-success">
            <?php echo $success_message; ?>
        </div>
        <?php endif; ?>

        <button id="toggle-notifications" class="btn btn-info">Show Notifications</button>
        <div id="notifications-list" class="hidden">
            <h2>Notifications</h2>
            <ul class="list-group mb-3">
                <?php foreach ($notifications as $notification): ?>
                <li class="list-group-item">
                    <?php echo $notification['message']; ?>
                    <span class="badge badge-primary highlight-green"><?php echo $notification['created_at']; ?></span>
                    <form method="POST" action="delete_notification.php" class="d-inline">
                        <input type="hidden" name="notification_id" value="<?php echo $notification['id']; ?>">
                        <button type="submit" class="btn btn-danger btn-sm ml-2">Delete</button>
                    </form>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <h2>Create Event</h2>
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
                <label for="event_capacity">Event Capacity</label> <!-- Added this field -->
                <input type="number" name="event_capacity" id="event_capacity" class="form-control" placeholder="Event Capacity" required>
            </div>
            <div class="form-group">
                <label for="event_image">Event Image</label>
                <input type="file" name="event_image" id="event_image" class="form-control">
            </div>
            <button type="submit" name="create_event" class="btn btn-primary">Create Event</button>
        </form>

        <h2>Your Events</h2>
        <form method="GET" action="organizer_dashboard.php" class="form-inline mb-3">
            <input type="text" name="search" class="form-control mr-2" placeholder="Search events" value="<?php echo htmlspecialchars($search_query); ?>">
            <button type="submit" class="btn btn-primary">Search</button>
        </form>
        <button id="toggle-events" class="btn btn-info">Show Events</button>
        <div id="events-list" class="hidden">
            <table class="table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Date</th>
                        <th>Location</th>
                        <th>Description</th>
                        <th>Image</th>
                        <th>Capacity</th> <!-- Added Capacity column -->
                        <th>Remaining Spots</th> <!-- Added Remaining Spots column -->
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($events as $event): ?>
                    <tr>
                        <td><?php echo $event['name']; ?></td>
                        <td><?php echo $event['date']; ?></td>
                        <td><?php echo $event['location']; ?></td>
                        <td><?php echo $event['description']; ?></td>
                        <td><img src="<?php echo $event['image_path']; ?>" alt="Event Image" style="width: 100px; height: 100px;"></td>
                        <td><?php echo $event['capacity']; ?></td> <!-- Display Capacity -->
                        <td>
                            <?php
                            // Fetch the number of RSVPs for the event
                            $sql = "SELECT COUNT(*) AS rsvp_count FROM rsvps WHERE event_id = ?";
                            $stmt = $conn->prepare($sql);
                            $stmt->bind_param("i", $event['id']);
                            $stmt->execute();
                            $result = $stmt->get_result();
                            $rsvp_count = $result->fetch_assoc()['rsvp_count'];
                            $stmt->close();

                            // Handle cases where capacity might not be set
                            $event_capacity = isset($event['capacity']) ? $event['capacity'] : 0;
                            $remaining_spots = $event_capacity - $rsvp_count;
                            ?>
                            <p><?php echo $remaining_spots; ?></p>
                        </td>
                        <td>
                            <form method="POST" action="view_event.php">
                                <input type="hidden" name="event_id" value="<?php echo $event['id']; ?>">
                                <button type="submit" name="view_event" class="btn btn-info">View</button>
                            </form>
                            <form method="POST" action="edit_event.php">
                                <input type="hidden" name="event_id" value="<?php echo $event['id']; ?>">
                                <button type="submit" name="edit_event" class="btn btn-warning">Edit</button>
                            </form>
                            <form method="POST" action="delete_event.php" onsubmit="return confirm('Are you sure you want to delete this event?');">
                                <input type="hidden" name="event_id" value="<?php echo $event['id']; ?>">
                                <button type="submit" name="delete_event" class="btn btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <a href="logout.php" class="btn btn-secondary">Logout</a>
    </div>

    <script>
        const toggleNotificationsButton = document.getElementById("toggle-notifications");
        const notificationsList = document.getElementById("notifications-list");

        toggleNotificationsButton.addEventListener("click", () => {
            if (notificationsList.classList.contains("hidden")) {
                notificationsList.classList.remove("hidden");
                toggleNotificationsButton.textContent = "Hide Notifications";
            } else {
                notificationsList.classList.add("hidden");
                toggleNotificationsButton.textContent = "Show Notifications";
            }
        });

        const toggleButton = document.getElementById("toggle-events");
        const eventsList = document.getElementById("events-list");

        toggleButton.addEventListener("click", () => {
            if (eventsList.classList.contains("hidden")) {
                eventsList.classList.remove("hidden");
                toggleButton.textContent = "Hide Events";
            } else {
                eventsList.classList.add("hidden");
                toggleButton.textContent = "Show Events";
            }
        });
        </script>
</body>
</html>
