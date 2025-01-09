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

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['event_id'])) {
    $event_id = $_POST['event_id'];

    // Debugging message
    error_log("Delete event request received for event ID: $event_id by user ID: {$_SESSION['user_id']}");

    $conn->begin_transaction();

    try {
        // Fetch the event name before deleting
        $sql = "SELECT name FROM events WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $event_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $event = $result->fetch_assoc();
        $event_name = $event['name'];

        // Delete related RSVPs
        $sql = "DELETE FROM rsvps WHERE event_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $event_id);
        $stmt->execute();

        // Delete related notifications
        $sql = "DELETE FROM notifications WHERE event_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $event_id);
        $stmt->execute();

        // Delete the event
        $sql = "DELETE FROM events WHERE id = ? AND organizer_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $event_id, $_SESSION['user_id']);
        $stmt->execute();

        $conn->commit();
        $_SESSION['message'] = "Event '$event_name' deleted successfully!";
        error_log("Event ID: $event_id deleted successfully.");
    } catch (mysqli_sql_exception $exception) {
        $conn->rollback();
        $_SESSION['message'] = "Error deleting event: " . $exception->getMessage();
        error_log("Error deleting event: " . $exception->getMessage());
    }

    $stmt->close();
} else {
    error_log("Invalid request method or event_id not set.");
    $_SESSION['message'] = "Invalid request. Event ID not set.";
}

header("Location: organizer_dashboard.php");
exit();
?>
