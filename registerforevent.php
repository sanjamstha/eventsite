<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

include './db.php';
include './header.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if (isset($_GET['event_id'])) {
    $event_id = $_GET['event_id'];
    $user_id = $_SESSION['user_id'];

    try {
        // Fetch event details
        $stmt = $pdo->prepare("SELECT event_price, max_capacity FROM events WHERE id = ?");
        $stmt->execute([$event_id]);
        $event = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$event) {
            echo "<p class='w-[70%] mx-auto'>The event does not exist.</p>";
            exit();
        }

        $event_price = $event['event_price'];
        $max_capacity = $event['max_capacity'];

        // Check event capacity
        $stmt = $pdo->prepare("SELECT COUNT(*) AS current_registrations FROM registrations WHERE event_id = ?");
        $stmt->execute([$event_id]);
        $registration_count = $stmt->fetch(PDO::FETCH_ASSOC)['current_registrations'];

        if ($registration_count >= $max_capacity) {
            echo "<p class='w-[70%] mx-auto'>Sorry, this event is full. You cannot register.</p>";
            exit();
        }

        // Check if user is already registered
        $stmt = $pdo->prepare("SELECT id, payment_status FROM registrations WHERE user_id = ? AND event_id = ?");
        $stmt->execute([$user_id, $event_id]);
        $registration = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($registration) {
            if ($registration['payment_status'] === 'Completed') {
                echo "<p class='w-[70%] mx-auto'>You are already registered and have completed the payment.</p>";
                exit();
            } elseif ($registration['payment_status'] === 'Pending') {
                echo "<p class='w-[70%] mx-auto'>You have registered but not paid yet. <a href='payment.php?event_id=$event_id' class='text-blue-500 hover:text-blue-700'>Pay Now</a></p>";
                exit();
            }
        }

        // Insert new registration
        $registration_date = date('Y-m-d H:i:s');
        $payment_status = ($event_price > 0) ? 'Pending' : 'Completed';
        $status = ($event_price > 0) ? 'Pending Payment' : 'Registered';

        $stmt = $pdo->prepare("INSERT INTO registrations (user_id, event_id, registration_date, payment_status, payment_amount, status) 
                               VALUES (?, ?, ?, ?, ?, ?)");

        $stmt->execute([$user_id, $event_id, $registration_date, $payment_status, $event_price, $status]);

        if ($event_price > 0) {
            // Redirect to payment page
            header("Location: payment.php?event_id=$event_id");
            exit();
        } else {
            echo "<p class='w-[70%] mx-auto'>You have successfully registered for the event!</p>";
            header("Refresh: 2; URL=profile.php");
            exit();
        }

    } catch (PDOException $e) {
        echo "<p class='w-[70%] mx-auto'>Error: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p class='w-[70%] mx-auto'>No event selected.</p>";
}

include './footer.php';
?>
