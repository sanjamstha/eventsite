<?php 
// Check if a session is already started before calling session_start()
if (session_status() == PHP_SESSION_NONE) {
    session_start(); // Start session only if not already started
}
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Event Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="./script.js"></script>
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
    />
  </head>
  <body class="flex flex-col min-h-screen">
    <div class="flex-grow">
    <header class="w-full border-b bg-white">
      <div class="w-[70%] mx-auto flex items-center justify-between py-5">
        <a href="./index.php" class="text-2xl font-semibold">EventPortal</a>
        <?php
          if (isset($_SESSION['user_id'])) {
              echo "Welcome, " . $_SESSION['username'] . "!"; 
        ?>

        <div class="flex w-1/3 items-center justify-between">
          <a href="./index.php">Home</a>
          <a href="./create_event.php">Create Event</a>
          <a href="./profile.php">My Profile</a>
        </div>
        <div>
          <a
            href="./logout.php"
            class="bg-[#23B5E8] font-semibold hover:bg-[#147DC8] text-white px-6 py-2 rounded-md"
            >Logout</a
          >
        </div>

        <?php } else {  
            echo "Welcome, Guest!";
        ?>
        <div>
          <a
            href="./login.php"
            class="font-semibold bg-[#23B5E8]  hover:bg-[#147DC8] text-white px-6 py-2 rounded-md"
            >Login</a
          >
        </div>

        <?php } ?>
      </div>
    </header>

