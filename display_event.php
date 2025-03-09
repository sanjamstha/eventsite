<?php
// Include the new database connection (db.php)
include('./db.php');

// Query to fetch all events from the `events` table
$query = "SELECT e.id, e.event_title, e.event_description, e.start_date, e.end_date, e.event_location, e.event_price, e.event_image, e.created_at, e.updated_at, c.category_name 
          FROM events e
          JOIN categories c ON e.category_id = c.id
          ORDER BY e.created_at DESC";

// Execute the query using PDO
$stmt = $pdo->query($query);

// Check if there are any events in the database
if ($stmt->rowCount() > 0) {
    echo '<section class="pb-10 bg-white ">';
    echo '<div class="w-[70%] mx-auto flex flex-wrap gap-4 justify-around">'; // Flexbox container with gap, centered

    // Loop through each event and display
    while ($event = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Extract event details
        $event_title = $event['event_title'];
        $event_description = $event['event_description'];
        $event_image = $event['event_image'] ? $event['event_image'] : '/assets/images/default-event.jpg'; // Default image if none exists
        $event_location = $event['event_location'];
        $start_date = date("D, jS M, h:i a", strtotime($event['start_date'])); // Format date
        $event_price = $event['event_price'] ? '$' . number_format($event['event_price'], 2) : 'Free'; // Price formatting
        $category_name = $event['category_name'];

        // Event card with border
        echo '<div class="max-w-xs rounded-lg border border-gray-300 overflow-hidden shadow-lg my-4 w-full sm:w-1/2 md:w-1/3 eventCard" data-title="' . strtolower($event_title) . '">'; // Add event title as data-title attribute
        echo '<a href="./eventdetails.php?id=' . $event['id'] . '">';
        echo '<img class="w-full h-64 object-cover hover:scale-105 duration-300" src="./uploads/' . $event_image . '" alt="' . $event_title . '" />';  // Ensure correct path for images
        echo '<div class="m-4 space-y-2">';
        echo '<div class="font-bold mb-2">' . $event_title . '</div>';
        echo '<div class="text-sm"><i class="fa-solid fa-calendar-days"></i>    ' . $start_date . '</div>';
        echo '<div class="text-sm"><i class="fa-solid fa-location-dot"></i>     ' . $event_location . '</div>';
        echo '<div class="inline-block bg-gray-300 rounded-full px-3 py-1 text-xs font-semibold ">' . $event_price . '</div>';
        echo '<div class="text-xs text-gray-500">Category: ' . $category_name . '</div>';
        echo '</div>';
        echo '</a>';
        echo '</div>';
    }

    echo '</div>';
    echo '</section>';
} else {
    echo "<p>No events found</p>";
}

// Close the connection
$pdo = null;
?>