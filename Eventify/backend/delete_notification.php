<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'organizer') {
    header("Location: login.php");
    exit();
}
include_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['notification_id'])) {
    $notification_id = $_POST['notification_id'];

    // Delete the notification
    $sql = "DELETE FROM notifications WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $notification_id);
    
    if ($stmt->execute()) {
        echo "<script>alert('Notification deleted successfully!');</script>";
    } else {
        echo "<script>alert('Error deleting notification: " . $stmt->error . "');</script>";
    }
    $stmt->close();
}

header("Location: organizer_dashboard.php");
exit();
?>
