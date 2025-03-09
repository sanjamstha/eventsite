<?php
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    $_SESSION['error_message'] = 'You do not have permission to access this page.';
    header('Location: index.php'); // Redirect to homepage or login page
    exit;
}
echo'welcome to admin page';

?>

