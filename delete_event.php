<?php
session_start();
include './db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$event_id = $_GET['id'] ?? null;

if (!$event_id) {
    echo "<script>alert('Invalid event ID.'); window.location.href = 'profile.php';</script>";
    exit();
}

try {
    // Ensure the user owns the event
    $stmt = $pdo->prepare("SELECT id FROM events WHERE id = ? AND created_by = ?");
    $stmt->execute([$event_id, $user_id]);
    $event = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$event) {
        echo "<script>alert('You do not have permission to delete this event.'); window.location.href = 'profile.php';</script>";
        exit();
    }

    // Delete participants first
    $stmt = $pdo->prepare("DELETE FROM participants WHERE event_id = ?");
    $stmt->execute([$event_id]);

    // Delete registrations
    $stmt = $pdo->prepare("DELETE FROM registrations WHERE event_id = ?");
    $stmt->execute([$event_id]);

    // Delete the event
    $stmt = $pdo->prepare("DELETE FROM events WHERE id = ?");
    $stmt->execute([$event_id]);

    echo "<script>alert('Event deleted successfully.'); window.location.href = 'profile.php';</script>";
    exit();
} catch (PDOException $e) {
    echo "<script>alert('Error deleting event.'); window.location.href = 'profile.php';</script>";
}
?>
