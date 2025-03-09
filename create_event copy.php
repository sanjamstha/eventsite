<?php
include('db.php'); // Include PDO database connection
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$alertMessage = "";  // Variable to store alert messages

// Initialize variables
$event_title = $event_description = $event_location = $start_date = $end_date = $event_price = $max_capacity = $category_id = '';
$event_image_file_name = null;
$event_organizer = $_SESSION['user_id']; // Logged-in user ID

// Fetch categories for dropdown
try {
    $stmt = $pdo->query("SELECT id, category_name FROM categories ORDER BY category_name ASC");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching categories: " . $e->getMessage());
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_event'])) {
    $event_title = trim($_POST['event_title']);
    $event_description = trim($_POST['event_description']);
    $event_location = trim($_POST['event_location']);
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $event_price = !empty($_POST['event_price']) ? $_POST['event_price'] : null;
    $max_capacity = !empty($_POST['max_capacity']) ? $_POST['max_capacity'] : null;
    $category_id = $_POST['category_id'];

    // Handle file upload
    if (!empty($_FILES['event_image']['tmp_name'])) {
        $event_image_tmp = $_FILES['event_image']['tmp_name'];
        $event_image_name = basename($_FILES['event_image']['name']);
        $event_image_file_name = "event_" . time() . "_" . $event_image_name;
        $upload_dir = "uploads/";

        if (!move_uploaded_file($event_image_tmp, $upload_dir . $event_image_file_name)) {
            $alertMessage = "Error uploading file.";
        }
    }

    // Insert new event
    try {
        $stmt = $pdo->prepare("INSERT INTO events 
            (event_title, event_description, start_date, end_date, event_location, event_price, created_by, event_image, category_id, max_capacity, created_at, updated_at) 
            VALUES (:event_title, :event_description, :start_date, :end_date, :event_location, :event_price, :created_by, :event_image, :category_id, :max_capacity, NOW(), NOW())");
        
        $stmt->execute([
            ':event_title' => $event_title,
            ':event_description' => $event_description,
            ':start_date' => $start_date,
            ':end_date' => $end_date,
            ':event_location' => $event_location,
            ':event_price' => $event_price,
            ':created_by' => $event_organizer,
            ':event_image' => $event_image_file_name,
            ':category_id' => $category_id,
            ':max_capacity' => $max_capacity
        ]);
        echo "<script>alert('Event created successfully.'); window.location.href = 'index.php';</script>";
        // header('Location: index.php');
        exit();
    } catch (PDOException $e) {
        $alertMessage = "Error creating event: " . $e->getMessage();
    }
}

?>

<?php include 'header.php'; ?>

<section>
    <div class="max-w-2xl mx-auto my-10 p-6 bg-white shadow-lg rounded-lg border">
        <h2 class="text-2xl font-semibold mb-6 text-center">Create Event</h2>

        <?php if ($alertMessage): ?>
            <script>alert("<?php echo $alertMessage; ?>");</script>
        <?php endif; ?>

        <form action="create_event.php" method="POST" enctype="multipart/form-data">
            <div class="mb-4">
                <label class="block text-gray-700 font-medium">Event Title</label>
                <input type="text" name="event_title" class="w-full px-4 py-2 border rounded-md" placeholder="Enter event title" value="<?php echo $event_title; ?>" required>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-medium">Event Description</label>
                <textarea name="event_description" rows="4" class="w-full px-4 py-2 border rounded-md" placeholder="Describe the event" required><?php echo $event_description; ?></textarea>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-medium">Event Image</label>
                <input type="file" name="event_image" class="w-full border rounded-md">
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-medium">Max Capacity</label>
                <input type="number" name="max_capacity" class="w-full px-4 py-2 border rounded-md" placeholder="Enter max participants" min="1" value="<?php echo $max_capacity; ?>" required>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-medium">Category</label>
                <select name="category_id" class="w-full px-4 py-2 border rounded-md" required>
                <option value="">Select a category</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo $category['id']; ?>" 
                            <?php echo ($category_id == $category['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($category['category_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-medium">Location</label>
                <input type="text" name="event_location" class="w-full px-4 py-2 border rounded-md" 
                       placeholder="Enter event location" value="<?php echo htmlspecialchars($event_location); ?>" required>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-medium">Start Date</label>
                <input type="datetime-local" name="start_date" class="w-full px-4 py-2 border rounded-md" 
                       value="<?php echo $start_date; ?>" required>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-medium">End Date</label>
                <input type="datetime-local" name="end_date" class="w-full px-4 py-2 border rounded-md" 
                       value="<?php echo $end_date; ?>">
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-medium">Price</label>
                <input type="number" name="event_price" class="w-full px-4 py-2 border rounded-md" 
                       placeholder="Enter event price (leave empty for free)" min="0" 
                       value="<?php echo $event_price; ?>">
            </div>

            <div class="flex justify-center">
                <button type="submit" name="create_event" class="px-6 py-2 text-white font-semibold rounded-md bg-[#23B5E8]  
                        hover:bg-[#147DC8] focus:outline-none focus:ring-2 focus:ring-[#147DC8]">
                    Create Event
                </button>
            </div>
        </form>
    </div>
</section>

<?php include 'footer.php'; ?>

