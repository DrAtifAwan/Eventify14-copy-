<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'attendee') {
    header("Location: login.php");
    exit();
}
include_once 'db_connect.php';

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

// Fetch RSVP status message
$rsvp_message = isset($_SESSION['rsvp_message']) ? $_SESSION['rsvp_message'] : "";
unset($_SESSION['rsvp_message']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendee Dashboard</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <style>
        .hidden {
            display: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Welcome, Attendee <?php echo $_SESSION['user_name']; ?>!</h1>
        <p>This is your dashboard.</p>

        <?php if ($rsvp_message): ?>
        <div class="alert alert-info">
            <?php echo $rsvp_message; ?>
        </div>
        <?php endif; ?>

        <h2>Upcoming Events</h2>
        <form method="GET" action="attendee_dashboard.php" class="form-inline mb-3">
            <input type="text" name="search" class="form-control mr-2" placeholder="Search events" value="<?php echo htmlspecialchars($search_query); ?>">
            <button type="submit" class="btn btn-primary">Search</button>
        </form>
        <button id="toggle-events" class="btn btn-info">Show Upcoming Events</button>
        <div id="events-list" class="hidden">
            <table class="table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Date</th>
                        <th>Location</th>
                        <th>Description</th>
                        <th>RSVP</th>
                        <th>View</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($events as $event): ?>
                    <tr>
                        <td><?php echo $event['name']; ?></td>
                        <td><?php echo $event['date']; ?></td>
                        <td><?php echo $event['location']; ?></td>
                        <td><?php echo $event['description']; ?></td>
                        <td>
                            <form method="POST" action="rsvp_event.php">
                                <input type="hidden" name="event_id" value="<?php echo $event['id']; ?>">
                                <button type="submit" name="status" value="attend" class="btn btn-success">Attend</button>
                                <button type="submit" name="status" value="maybe" class="btn btn-warning">Maybe</button>
                                <button type="submit" name="status" value="decline" class="btn btn-danger">Decline</button>
                            </form>
                        </td>
                        <td>
                            <form method="POST" action="view_event.php">
                                <input type="hidden" name="event_id" value="<?php echo $event['id']; ?>">
                                <button type="submit" name="view_event" class="btn btn-info">View</button>
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
        const toggleButton = document.getElementById("toggle-events");
        const eventsList = document.getElementById("events-list");

        toggleButton.addEventListener("click", () => {
            if (eventsList.classList.contains("hidden")) {
                eventsList.classList.remove("hidden");
                toggleButton.textContent = "Hide Upcoming Events";
            } else {
                eventsList.classList.add("hidden");
                toggleButton.textContent = "Show Upcoming Events";
            }
        });
    </script>
</body>
</html>
