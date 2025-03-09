<?php
// Handle password change
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($current_password === $current_password_db) {
        if ($new_password == $confirm_password) {
            $query_update_password = "UPDATE users SET password = ? WHERE id = ?";
            $stmt_update_password = $pdo->prepare($query_update_password);
            if ($stmt_update_password->execute([$new_password, $user_id])) {
                echo "<script>alert('Password updated successfully!');</script>";

            } else {
                echo "<script>alert('Error updating password. Please try again later.');</script>";
            }
        } else {
            echo "<script>alert('New password and confirm password do not match.');</script>";
        }
    } else {
        echo "<script>alert('Current password is incorrect.');</script>";
    }
}

?>

<div class="mb-6">
    <h3 class="text-xl font-medium mb-3">Change Password</h3>
    <form method="POST">
        <div class="mb-4"> 
            <label class="block text-gray-700 font-medium">Current Password</label>
            <input type="password" name="current_password" class="w-full px-4 py-2 border rounded-md" required>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 font-medium">New Password</label>
            <input type="password" name="new_password" class="w-full px-4 py-2 border rounded-md" required>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 font-medium">Confirm New Password</label>
            <input type="password" name="confirm_password" class="w-full px-4 py-2 border rounded-md" required>
        </div>
        <button type="submit" name="change_password" class="px-6 py-2 text-white bg-[#23B5E8] hover:bg-[#147DC8] rounded-md">Change Password</button>
    </form>
</div>
