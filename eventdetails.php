<?php
include 'header.php';
include 'db.php'; // PDO database connection

// Check if the 'id' parameter is set in the URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $event_id = $_GET['id'];

    try {
        // Fetch event details
        $stmt = $pdo->prepare("SELECT e.*, u.username AS organizer 
                               FROM events e 
                               JOIN users u ON e.created_by = u.id 
                               WHERE e.id = ?");
        $stmt->execute([$event_id]);
        $event = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($event) {
            // Extract event details
            $event_title = $event['event_title'];
            $event_description = $event['event_description'];
            $event_image = !empty($event['event_image']) ? "./uploads/" . $event['event_image'] : "/assets/images/default-event.jpg";
            $event_location = $event['event_location'];
            $start_date = date("D, jS M, h:i A", strtotime($event['start_date']));
            $end_date = !empty($event['end_date']) ? date("D, jS M, h:i A", strtotime($event['end_date'])) : "N/A";
            $event_price = $event['event_price'] ? '$' . number_format($event['event_price'], 2) : 'Free';
            $max_capacity = $event['max_capacity'];
            $organizer = $event['organizer'];
            ?>

            <!-- Event Detail Section -->
            <section class="my-10">
                <div class="flex w-[70%] mx-auto">
                    <!-- Image Part -->
                    <div class="pr-24 w-1/2">
                        <img src="<?php echo $event_image; ?>" alt="<?php echo htmlspecialchars($event_title); ?>"
                            class="rounded-xl drop-shadow-md" />
                    </div>

                    <!-- Details Part -->
                    <div class="w-1/2 space-y-5">
                        <!-- Title -->
                        <h1 class="text-2xl font-bold"><?php echo htmlspecialchars($event_title); ?></h1>

                        <!-- Description -->
                        <div>
                            <h2 class="text-base font-semibold">Details</h2>
                            <p class="text-sm my-2"><?php echo nl2br(htmlspecialchars($event_description)); ?></p>
                        </div>

                        <!-- Organizer -->
                        <div class="text-sm">
                            <p class="font-semibold"><i class="fa-solid fa-user"></i> Organized by:
                                <?php echo htmlspecialchars($organizer); ?></p>
                        </div>

                        <!-- Price -->
                        <div class="text-sm">
                            <p class="font-semibold"><i class="fa-solid fa-dollar-sign"></i> Price: <?php echo $event_price; ?></p>
                        </div>

                        <!-- Location -->
                        <div class="text-sm">
                            <p class="font-semibold"><i class="fa-solid fa-location-dot"></i> Location:
                                <?php echo htmlspecialchars($event_location); ?></p>
                        </div>

                        <!-- Start Date -->
                        <div class="text-sm">
                            <p class="font-semibold"><i class="fa-solid fa-calendar-days"></i> Start Date:
                                <?php echo $start_date; ?></p>
                        </div>

                        <!-- End Date -->
                        <div class="text-sm">
                            <p class="font-semibold"><i class="fa-solid fa-calendar-days"></i> End Date: <?php echo $end_date; ?>
                            </p>
                        </div>

                        <!-- Seats Available -->
                        <div class="text-sm">
                            <p class="font-semibold"><i class="fa-solid fa-chair"></i> Seat Available: <?php echo $max_capacity; ?>
                            </p>
                        </div>

                        <!-- Register Button -->
                        <a href="registerforevent.php?event_id=<?php echo $event_id; ?>">
                            <button class="bg-[#23B5E8] font-semibold hover:bg-[#147DC8] text-white px-4 py-3 my-5 rounded-xl">
                                Register for this event
                            </button>
                        </a>
                    </div>
                </div>
            </section>

            <?php
        } else {
            echo "<p>Event not found.</p>";
        }
    } catch (PDOException $e) {
        echo "<p>Error fetching event details: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
} else {
    echo "<p>No event selected.</p>";
}

include 'footer.php';
?>