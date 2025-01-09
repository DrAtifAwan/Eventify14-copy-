<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'attendee') {
    header("Location: login.php");
    exit();
}
include_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['status'])) {
    $event_id = $_POST['event_id'];
    $attendee_id = $_SESSION['user_id'];
    $status = $_POST['status'];

    // Check if RSVP already exists
    $sql = "SELECT * FROM rsvps WHERE event_id = ? AND attendee_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $event_id, $attendee_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Update existing RSVP
        $sql = "UPDATE rsvps SET status = ? WHERE event_id = ? AND attendee_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sii", $status, $event_id, $attendee_id);
    } else {
        // Insert new RSVP
        $sql = "INSERT INTO rsvps (event_id, attendee_id, status) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iis", $event_id, $attendee_id, $status);
    }

    if ($stmt->execute()) {
        // Fetch event name
        $sql = "SELECT name FROM events WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $event_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $event = $result->fetch_assoc();

        // Create notification for organizer
        $message = "Attendee " . $_SESSION['user_name'] . " has RSVP'd: " . $status . " to the event \"" . $event['name'] . "\"";
        $sql = "INSERT INTO notifications (event_id, attendee_id, message) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iis", $event_id, $attendee_id, $message);
        $stmt->execute();

        $_SESSION['rsvp_message'] = "RSVP status updated to '" . $status . "' for the event \"" . $event['name'] . "\"";
    } else {
        $_SESSION['rsvp_message'] = "Error updating RSVP status: " . $stmt->error;
    }
    $stmt->close();
}

header("Location: attendee_dashboard.php");
exit();
?>
