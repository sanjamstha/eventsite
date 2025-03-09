<?php
session_start();
include './db.php';

// Ensure user is logged in and event_id is provided
if (!isset($_SESSION['user_id']) || !isset($_GET['event_id'])) {
    http_response_code(400); // Bad Request
    exit();
}

$user_id = $_SESSION['user_id'];
$event_id = $_GET['event_id'];

try {
    // Check if the registration exists and is still pending
    $stmt = $pdo->prepare("SELECT id FROM registrations WHERE user_id = ? AND event_id = ? AND payment_status = 'Pending'");
    $stmt->execute([$user_id, $event_id]);
    $registration = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($registration) {
        // Delete the pending registration
        $stmt = $pdo->prepare("DELETE FROM registrations WHERE id = ?");
        $stmt->execute([$registration['id']]);

        // Also delete from participants table (in case it was inserted)
        $stmt = $pdo->prepare("DELETE FROM participants WHERE user_id = ? AND event_id = ?");
        $stmt->execute([$user_id, $event_id]);

        http_response_code(200); // Success
        exit();
    } else {
        http_response_code(404); // Registration not found
        exit();
    }
} catch (PDOException $e) {
    error_log("Error canceling registration: " . $e->getMessage());
    http_response_code(500); // Internal Server Error
    exit();
}
?>
