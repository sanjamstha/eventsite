<?php
include './db.php';
session_start();

// Redirect logged-in users to the homepage
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$message = "";
$toastClass = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password']; // No hashing, stored as plain text

    // Check if email already exists
    $checkEmailStmt = $pdo->prepare("SELECT email FROM users WHERE email = ?");
    $checkEmailStmt->execute([$email]);

    if ($checkEmailStmt->rowCount() > 0) {
        $message = "Email ID already exists";
        $toastClass = "bg-red-400";
    } else {
        // Insert new user
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        if ($stmt->execute([$username, $email, $password])) {
            $message = "Account created successfully!";
            $toastClass = "bg-green-400";
            header('Location: index.php');
            exit;
        } else {
            $message = "Error: Could not create account.";
            $toastClass = "bg-red-400";
        }
    }
}
?>

<?php include 'header.php'; ?>

<div class="h-screen flex items-center justify-center">
    <div class="w-full max-w-sm p-4 bg-white border border-gray-200 rounded-lg shadow sm:p-6 md:p-8">
        <form class="space-y-6" action="register.php" method="POST">
            <h5 class="text-xl font-medium text-gray-900">Register for EventPortal</h5>

            <div>
                <label for="username" class="block mb-2 text-sm font-medium text-gray-900">Username</label>
                <input type="text" name="username" id="username" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="abc" required />
            </div>

            <div>
                <label for="email" class="block mb-2 text-sm font-medium text-gray-900">Email</label>
                <input type="email" name="email" id="email" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="abc@gmail.com" required />
            </div>

            <div>
                <label for="password" class="block mb-2 text-sm font-medium text-gray-900">Password</label>
                <input type="password" name="password" id="password" placeholder="••••••••" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required />
            </div>

            <?php if ($message): ?>
                <div class="p-4 mb-4 text-sm text-white rounded-lg <?php echo $toastClass; ?>" role="alert">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <button type="submit" class="w-full text-white bg-[#23B5E8] hover:bg-[#147DC8] focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">Create an account</button>

            <div class="text-sm font-medium text-gray-500">
                Already have an account? <a href="login.html" class="text-[#23B5E8] hover:underline">Sign In</a>
            </div>
        </form>
    </div>
</div>

<?php include 'footer.php'; ?>
