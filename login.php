<?php
include './db.php';
session_start(); // Start session

// Define admin credentials
define('ADMIN_EMAIL', 'admin@gmail.com');
define('ADMIN_PASSWORD', 'admin123');

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: index.php'); 
    exit; 
}

$message = '';
$toastClass = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $message = 'Both fields are required!';
        $toastClass = 'bg-red-500'; 
    } else {
        // Check if the user is an admin
        if ($email === ADMIN_EMAIL && $password === ADMIN_PASSWORD) {
            $_SESSION['user_id'] = 0; // Special value for admin
            $_SESSION['username'] = 'Admin';
            $_SESSION['email'] = ADMIN_EMAIL;
            $_SESSION['is_admin'] = true;

            header('Location: admin.php'); // Redirect to admin panel
            exit;
        } else {
            // Check user in database
            $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // If user exists and password matches
            if ($user && $password === $user['password']) {
                $_SESSION['user_id'] = $user['id']; 
                $_SESSION['username'] = $user['username']; 
                $_SESSION['email'] = $user['email']; 
                $_SESSION['is_admin'] = false; 

                header('Location: index.php'); // Redirect to homepage
                exit;
            } else {
                $message = 'Invalid email or password!';
                $toastClass = 'bg-red-500'; 
            }
        }
    }
}
?>

<?php include 'header.php'; ?>

<div class="h-screen flex items-center justify-center">
    <div class="w-full max-w-sm p-4 bg-white border border-gray-200 rounded-lg shadow sm:p-6 md:p-8">
        <form class="space-y-6" action="login.php" method="POST">
            <h5 class="text-xl font-medium text-gray-900">Login to EventPortal</h5>

            <div>
                <label for="email" class="block mb-2 text-sm font-medium text-gray-900">Email</label>
                <input type="email" name="email" id="email" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="abc@gmail.com" required />
            </div>

            <div>
                <label for="password" class="block mb-2 text-sm font-medium text-gray-900">Password</label>
                <input type="password" name="password" id="password" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="••••••••" required />
            </div>

            <?php if ($message): ?>
                <div class="p-4 mb-4 text-sm text-white rounded-lg <?php echo $toastClass; ?>" role="alert">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <button type="submit" class="w-full text-white bg-[#23B5E8] hover:bg-[#147DC8] focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">Sign In</button>

            <div class="text-sm font-medium text-gray-500">
                Don't have an account? <a href="register.php" class="text-[#23B5E8] hover:underline">Create an account</a>
            </div>
        </form>
    </div>
</div>

<?php include 'footer.php'; ?>
