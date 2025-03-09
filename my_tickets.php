<?php
// Fetch events the user has registered for
$query_tickets = "
    SELECT e.id, e.event_title, e.event_image, r.id AS registration_id, r.registration_date, r.payment_status
    FROM registrations r
    JOIN events e ON r.event_id = e.id
    WHERE r.user_id = ?
";
$stmt_tickets = $pdo->prepare($query_tickets);
$stmt_tickets->execute([$user_id]);
$result_tickets = $stmt_tickets->fetchAll(PDO::FETCH_ASSOC);

// Handle cancellation request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_registration'])) {
    $registration_id = $_POST['registration_id'];

    // First, get event_id and user_id associated with the registration
    $stmt = $pdo->prepare("SELECT event_id FROM registrations WHERE id = ?");
    $stmt->execute([$registration_id]);
    $eventData = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($eventData) {
        $event_id = $eventData['event_id'];

        // Delete participant entry
        $stmt = $pdo->prepare("DELETE FROM participants WHERE user_id = ? AND event_id = ?");
        $stmt->execute([$user_id, $event_id]);

        // Delete registration from database
        $stmt = $pdo->prepare("DELETE FROM registrations WHERE id = ?");
        $stmt->execute([$registration_id]);

        echo "<script>alert('Registration canceled successfully.'); window.location.href='profile.php';</script>";
        exit();
    }
}
?>

<div class="my-10 py-6 bg-white shadow-lg rounded-lg border border-gray-500">
    <h2 class="text-2xl w-full font-semibold mb-6 text-center">My Tickets</h2>

    <?php if ($result_tickets): ?>
        <table class="min-w-full bg-white border border-gray-300 shadow-lg text-center">
            <thead class="bg-gray-100 border border-gray-400">
                <tr>
                    <th class="py-3 px-6 border border-gray-400">Event Image</th>
                    <th class="py-3 px-6 border border-gray-400">Event Title</th>
                    <th class="py-3 px-6 border border-gray-400">Registered At</th>
                    <th class="py-3 px-6 border border-gray-400">View Details</th>
                    <th class="py-3 px-6 border border-gray-400">Payment Status</th>
                    <th class="py-3 px-6 border border-gray-400">Cancel</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($result_tickets as $ticket): ?>
                    <?php
                    // Format registration date
                    $registered_at = date("D, jS M Y", strtotime($ticket['registration_date']));
                    ?>
                    <tr class="border border-gray-400">
                        <!-- Event Image -->
                        <td class="py-4 px-6 border border-gray-400">
                            <img src="uploads/<?= $ticket['event_image'] ?>" alt="<?= $ticket['event_title'] ?>" class="w-44 h-44 object-cover rounded-md mx-auto">
                        </td>

                        <!-- Event Title -->
                        <td class="py-4 px-6 font-medium border border-gray-400">
                            <?= htmlspecialchars($ticket['event_title']) ?>
                        </td>

                        <!-- Registered At -->
                        <td class="py-4 px-6 text-gray-600 border border-gray-400">
                            <?= $registered_at ?>
                        </td>

                        <!-- View Details -->
                        <td class="py-4 px-6 border border-gray-400">
                            <a href="eventdetails.php?id=<?= $ticket['id'] ?>" class="text-blue-500 hover:text-blue-700">View</a>
                        </td>

                        <!-- Payment Status -->
                        <td class="py-4 px-6 border border-gray-400">
                            <?php
                            $payment_status = ucfirst($ticket['payment_status']); // Capitalize first letter

                            // Determine text color
                            $text_color = ($payment_status === 'Completed') ? 'text-green-500' : 
                                          (($payment_status === 'Pending') ? 'text-orange-500' : 'text-red-500');
                            ?>
                            
                            <span class="font-semibold <?= $text_color ?>">
                                <?= $payment_status ?>
                            </span>

                            <?php if ($ticket['payment_status'] === 'Pending' || $ticket['payment_status'] === 'Failed'): ?>
                                <br>
                                <a href="payment.php?event_id=<?= $ticket['id'] ?>" 
                                   class="mt-2 inline-block bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                                    Pay Now
                                </a>
                            <?php endif; ?>
                        </td>

                        <!-- Cancel Registration -->
                        <td class="py-4 px-6 border border-gray-400">
                            <form method="POST">
                                <input type="hidden" name="registration_id" value="<?= $ticket['registration_id'] ?>">
                                <button type="submit" name="cancel_registration" class="text-red-500 hover:text-red-700">
                                    Cancel
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="text-center text-gray-500">You have not registered for any events yet.</p>
    <?php endif; ?>
</div>
