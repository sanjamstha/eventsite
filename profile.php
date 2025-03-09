<?php
session_start();
include 'db.php'; // Database connection

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user details
$query_user = "SELECT username, email, password FROM users WHERE id = ?";
$stmt_user = $pdo->prepare($query_user);
$stmt_user->execute([$user_id]);
$user = $stmt_user->fetch();

if (!$user) {
    echo "<p>User not found.</p>";
    exit();
}

$username = $user['username'];
$email = $user['email'];
$current_password_db = $user['password']; 

include 'header.php';
?>

<section>
    <div class="w-[70%] mx-auto my-10 p-6 bg-white shadow-lg rounded-lg border">
        <h2 class="text-2xl font-semibold mb-6 text-center">Profile Management</h2>

        <!-- Display Username and Email -->
        <div class="mb-6">
            <h3 class=" font-medium">Username: <?php echo $username; ?></h3>
            <p class=" font-medium">Email: <?php echo $email; ?></p>
        </div>
        <?php include'change_password.php'; ?>
        <?php include'my_tickets.php'; ?>
        <?php include'my_events.php'; ?>
    </div>
</section>

<?php include 'footer.php'; ?>
