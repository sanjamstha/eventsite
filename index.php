<?php
include 'db.php'; // Include the database connection
session_start();
if (isset($_SESSION['error_message'])) {
    echo '<div class="p-4 mb-4 text-sm text-white bg-red-500 rounded-lg">' . $_SESSION['error_message'] . '</div>';
    unset($_SESSION['error_message']); // Clear the error message after displaying it
}
?>

<?php include 'header.php'; ?>

<main>
    <?php include 'herosection.php'; ?>
    <?php include 'search_event.php'; ?>
    <?php include 'display_event.php'; ?>
</main>

<?php include 'footer.php'; ?>
