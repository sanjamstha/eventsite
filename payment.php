<?php
session_start();
include './db.php';
include './header.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if (!isset($_GET['event_id'])) {
    echo "<script>alert('No event selected.'); window.location.href='index.php';</script>";
    exit();
}

$event_id = $_GET['event_id'];
$user_id = $_SESSION['user_id'];

try {
    // Fetch event details
    $stmt = $pdo->prepare("SELECT event_title, event_price FROM events WHERE id = ?");
    $stmt->execute([$event_id]);
    $event = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$event) {
        echo "<script>alert('Event not found.'); window.location.href='index.php';</script>";
        exit();
    }

    $event_title = $event['event_title'];
    $event_price = $event['event_price'];

    // Check if user is already registered
    $stmt = $pdo->prepare("SELECT id, payment_status FROM registrations WHERE user_id = ? AND event_id = ?");
    $stmt->execute([$user_id, $event_id]);
    $registration = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($registration) {
        $registration_id = $registration['id'];
        $payment_status = $registration['payment_status'];

        // If payment is already completed, redirect to profile
        if ($payment_status === 'Completed') {
            echo "<script>alert('Payment already completed for this event.'); window.location.href='profile.php';</script>";
            exit();
        }
    } else {
        // Insert new registration with "Pending" payment status
        $stmt = $pdo->prepare("INSERT INTO registrations (user_id, event_id, registration_date, payment_status, payment_amount, status) 
                               VALUES (?, ?, NOW(), 'Pending', ?, 'Registered')");
        $stmt->execute([$user_id, $event_id, $event_price]);

        // Get the new registration ID
        $registration_id = $pdo->lastInsertId();
    }

    // Handle "Pay Now" button click
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pay_now'])) {
        // Update payment status to "Completed"
        $updatePayment = $pdo->prepare("UPDATE registrations SET payment_status = 'Completed' WHERE id = ?");
        $updatePayment->execute([$registration_id]);

        // Check if user is already a participant (avoid duplicate entries)
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM participants WHERE user_id = ? AND event_id = ?");
        $stmt->execute([$user_id, $event_id]);
        $isAlreadyParticipant = $stmt->fetchColumn();

        // Insert user into participants table if not already added
        if (!$isAlreadyParticipant) {
            $insertParticipant = $pdo->prepare("INSERT INTO participants (user_id, event_id, status) VALUES (?, ?, 'Confirmed')");
            $insertParticipant->execute([$user_id, $event_id]);
        }

        echo "<script>alert('Payment successful! You are now a participant.'); window.location.href='profile.php';</script>";
        exit();
    }

    // Handle "Cancel Registration" button click
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_registration'])) {
        // Delete registration record
        $deleteRegistration = $pdo->prepare("DELETE FROM registrations WHERE id = ?");
        $deleteRegistration->execute([$registration_id]);

        echo "<script>alert('Registration canceled successfully.'); window.location.href='profile.php';</script>";
        exit();
    }

} catch (PDOException $e) {
    echo "<script>alert('Error: " . $e->getMessage() . "'); window.location.href='index.php';</script>";
    exit();
}
?>

<section class="my-10 text-center">
    <h1 class="text-2xl font-bold">Payment for "<?php echo htmlspecialchars($event_title); ?>"</h1>
    <p class="text-lg mt-2">Amount to Pay: <strong>$<?php echo number_format($event_price, 2); ?></strong></p>

    <form method="POST">
        <button type="submit" name="pay_now" class="bg-green-500 text-white px-5 py-3 mt-5 rounded-lg">
            Pay Now
        </button>
    </form>

    <form method="POST">
        <button type="submit" name="cancel_registration" class="bg-red-500 text-white px-5 py-3 mt-3 rounded-lg">
            Cancel Registration
        </button>
    </form>
</section>

<?php include './footer.php'; ?>

<script>
let paymentCompleted = false; // Track if payment is completed

window.addEventListener("beforeunload", function (event) {
    if (!paymentCompleted) {
        event.preventDefault();
        event.returnValue = "If you leave this page, your registration will be canceled, and you won't be counted as a participant.";
        
        // Delay fetch to ensure it runs before the page unloads
        setTimeout(() => {
            fetch("cancel_registration.php?event_id=<?= $event_id ?>&user_id=<?= $user_id ?>", {
                method: "GET"
            });
        }, 100);
    }
});

// Handle page visibility change (backup method for tab closing)
document.addEventListener("visibilitychange", function () {
    if (document.visibilityState === "hidden" && !paymentCompleted) {
        fetch("cancel_registration.php?event_id=<?= $event_id ?>&user_id=<?= $user_id ?>", {
            method: "GET"
        });
    }
});

// Mark payment as completed when user clicks "Pay Now"
document.querySelector("button[name='pay_now']").addEventListener("click", function () {
    paymentCompleted = true;
});
</script>



