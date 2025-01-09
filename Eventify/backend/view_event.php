<?php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'attendee' && $_SESSION['role'] !== 'organizer')) {
    header("Location: login.php");
    exit();
}
include_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['view_event'])) {
    $event_id = $_POST['event_id'];

    // Fetch event details
    $sql = "SELECT * FROM events WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $event = $result->fetch_assoc();
    $stmt->close();
} else {
    if ($_SESSION['role'] == 'organizer') {
        header("Location: organizer_dashboard.php");
    } else {
        header("Location: attendee_dashboard.php");
    }
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Event</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h1>View Event</h1>
        <div class="card">
            <img src="<?php echo $event['image_path']; ?>" alt="Event Image" class="card-img-top">
            <div class="card-body">
                <h5 class="card-title"><?php echo $event['name']; ?></h5>
                <p class="card-text"><strong>Date:</strong> <?php echo $event['date']; ?></p>
                <p class="card-text"><strong>Location:</strong> <?php echo $event['location']; ?></p>
                <p class="card-text"><strong>Description:</strong> <?php echo $event['description']; ?></p>
                <form method="POST" action="<?php echo $_SESSION['role'] === 'organizer' ? 'organizer_dashboard.php' : 'attendee_dashboard.php'; ?>">
                    <button type="submit" class="btn btn-primary">Back to Dashboard</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
