<?php
session_start();
include './db.php';
include './header.php';

// Ensure event_id is provided
if (!isset($_GET['event_id'])) {
    echo "<script>alert('No event selected.'); window.location.href='profile.php';</script>";
    exit();
}

$event_id = $_GET['event_id'];

try {
    // Fetch event details
    $stmt = $pdo->prepare("SELECT event_title FROM events WHERE id = ?");
    $stmt->execute([$event_id]);
    $event = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$event) {
        echo "<script>alert('Event not found.'); window.location.href='profile.php';</script>";
        exit();
    }

    // Fetch participants
    $stmt = $pdo->prepare("
        SELECT u.id, u.username, u.email, p.status
        FROM participants p
        JOIN users u ON p.user_id = u.id
        WHERE p.event_id = ?
    ");
    $stmt->execute([$event_id]);
    $participants = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "<script>alert('Error: " . $e->getMessage() . "'); window.location.href='profile.php';</script>";
    exit();
}
?>
    <div class="w-[70%] mx-auto my-10 p-6 bg-white shadow-lg rounded-lg border">

<!-- <section class="my-10 py-6 bg-white shadow-lg rounded-lg border"> -->
    <h2 class="text-2xl w-full font-semibold mb-6 text-center">Participants for "<?= htmlspecialchars($event['event_title']) ?>"</h2>

    <?php if ($participants): ?>
        <table class="min-w-full bg-white border border-gray-300 shadow-lg text-center">
            <thead class="bg-gray-100 border border-gray-400">
                <tr>
                    <th class="py-3 px-6 border border-gray-400">User ID</th>
                    <th class="py-3 px-6 border border-gray-400">Username</th>
                    <th class="py-3 px-6 border border-gray-400">Email</th>
                    <th class="py-3 px-6 border border-gray-400">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($participants as $participant): ?>
                    <tr class="border border-gray-400">
                        <td class="py-4 px-6 border border-gray-400"><?= htmlspecialchars($participant['id']) ?></td>
                        <td class="py-4 px-6 font-medium border border-gray-400"><?= htmlspecialchars($participant['username']) ?></td>
                        <td class="py-4 px-6 border border-gray-400"><?= htmlspecialchars($participant['email']) ?></td>
                        <td class="py-4 px-6 border border-gray-400"><?= htmlspecialchars($participant['status']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="text-center text-gray-500">No participants registered for this event.</p>
    <?php endif; ?>

    <div class="text-center mt-6">
        <a href="profile.php" class="bg-blue-500 text-white px-5 py-3 rounded-md hover:bg-blue-700">Back to My Events</a>
    </div>
<!-- </section> -->
</div>
<?php include './footer.php'; ?>
