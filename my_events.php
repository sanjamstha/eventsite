    <?php
    // Fetch events created by the logged-in user
    $query_events = "SELECT id, event_title, event_image, created_at FROM events WHERE created_by = ?";
    $stmt_events = $pdo->prepare($query_events);
    $stmt_events->execute([$user_id]);
    $result_events = $stmt_events->fetchAll(PDO::FETCH_ASSOC);
    ?>

    <div class="my-10 py-6 bg-white shadow-lg rounded-lg border border-gray-500">
        <h2 class="text-2xl w-full font-semibold mb-6 text-center">Events You Organized</h2>

        <?php if ($result_events): ?>
            <table class="min-w-full bg-white border border-gray-300 shadow-lg text-center">
                <thead class="bg-gray-100 border border-gray-400">
                    <tr>
                        <th class="py-3 px-6 border border-gray-400">Event Image</th>
                        <th class="py-3 px-6 border border-gray-400">Event Title</th>
                        <th class="py-3 px-6 border border-gray-400">Created At</th>
                        <th class="py-3 px-6 border border-gray-400">View Details</th>
                        <th class="py-3 px-6 border border-gray-400">Actions</th>
                        <th class="py-3 px-6 border border-gray-400">Participants</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($result_events as $event): ?>
                        <?php
                        // Get participants count
                        $participants_count_query = "SELECT COUNT(*) AS participant_count FROM registrations WHERE event_id = ?";
                        $stmt_participants = $pdo->prepare($participants_count_query);
                        $stmt_participants->execute([$event['id']]);
                        $participants_count = $stmt_participants->fetch(PDO::FETCH_ASSOC)['participant_count'];

                        // Format creation date
                        $created_at = date("D, jS M Y", strtotime($event['created_at']));
                        ?>
                        <tr class="border border-gray-400">
                            <!-- Event Image -->
                            <td class="py-4 px-6 border border-gray-400">
                                <img src="uploads/<?= $event['event_image'] ?>" alt="<?= $event['event_title'] ?>" class="w-44 h-44 object-cover rounded-md mx-auto">
                            </td>

                            <!-- Event Title -->
                            <td class="py-4 px-6 font-medium border border-gray-400">
                                <?= htmlspecialchars($event['event_title']) ?>
                            </td>

                            <!-- Created At -->
                            <td class="py-4 px-6 text-gray-600 border border-gray-400">
                                <?= $created_at ?>
                            </td>

                            <!-- View Details -->
                            <td class="py-4 px-6 border border-gray-400">
                                <a href="eventdetails.php?id=<?= $event['id'] ?>" class="text-blue-500 hover:text-blue-700">View</a>
                            </td>

                            <!-- Actions: Edit & Delete -->
                            <td class="py-4 px-6 border border-gray-400 text-center align-middle">
                                <div class="flex items-center justify-center gap-4">
                                    <a href="create_event.php?edit_id=<?= $event['id'] ?>" class="text-blue-500 hover:text-blue-700">
                                        <i class="fas fa-edit text-xl"></i>
                                    </a>
                                    <a href="delete_event.php?id=<?= $event['id'] ?>" class="text-red-500 hover:text-red-700" onclick="return confirm('Are you sure?');">
                                        <i class="fas fa-trash text-xl"></i>
                                    </a>
                                </div>
                            </td>



                            <!-- Participants -->
                            <td class="py-4 px-6 border border-gray-400">
                                <span class="font-semibold"><?= $participants_count ?></span> Participants
                                <a href="view_participants.php?event_id=<?= $event['id'] ?>" class="text-green-500 hover:text-green-700 ml-4">View</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="text-center text-gray-500">You have not organized any events yet.</p>
        <?php endif; ?>
    </div>
