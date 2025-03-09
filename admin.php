<?php 
session_start();
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    $_SESSION['error_message'] = 'You do not have permission to access this page.';
    header('Location: index.php'); // Redirect to homepage or login page
    exit;
}
include('header.php'); 
include('db.php'); 
?>

<main class="flex w-[70%] mx-auto">
  <!-- Sidebar -->
  <div class="w-64 bg-white text-black p-5 shadow-md">
    <h1 class="text-2xl font-semibold mb-5">Admin Dashboard</h1>
    
    <!-- Users Section (Dropdown) -->
    <div class="mb-4 border-b border-gray-600 pb-4">
      <h2 class="text-lg font-medium cursor-pointer" id="usersDropdown">Users</h2>
      <!-- Dropdown for Users -->
      <div id="usersMenu" class="hidden">
        <ul class="py-2 text-sm text-gray-700">
          <li><a href="add_user.php" class="block px-4 py-2 hover:bg-gray-100">Add</a></li>
          <li><a href="edit_user.php" class="block px-4 py-2 hover:bg-gray-100">Edit</a></li>
          <li><a href="delete_user.php" class="block px-4 py-2 hover:bg-gray-100">Delete</a></li>
        </ul>
      </div>
    </div>

    <!-- Categories Section (Dropdown) -->
    <div class="mb-4 border-b border-gray-600 pb-4">
      <h2 class="text-lg font-medium cursor-pointer" id="categoriesDropdown">Categories</h2>
      <!-- Dropdown for Categories -->
      <div id="categoriesMenu" class="hidden">
        <ul class="py-2 text-sm text-gray-700">
          <li><a href="add_category.php" class="block px-4 py-2 hover:bg-gray-100">Add</a></li>
          <li><a href="edit_category.php" class="block px-4 py-2 hover:bg-gray-100">Edit</a></li>
          <li><a href="delete_category.php" class="block px-4 py-2 hover:bg-gray-100">Delete</a></li>
        </ul>
      </div>
    </div>

    <!-- Events Section (Dropdown) -->
    <div class="mb-4 border-b border-gray-600 pb-4">
      <h2 class="text-lg font-medium cursor-pointer" id="eventsDropdown">Events</h2>
      <!-- Dropdown for Events -->
      <div id="eventsMenu" class="hidden">
        <ul class="py-2 text-sm text-gray-700">
          <li><a href="add_event.php" class="block px-4 py-2 hover:bg-gray-100">Add</a></li>
          <li><a href="edit_event.php" class="block px-4 py-2 hover:bg-gray-100">Edit</a></li>
          <li><a href="delete_event.php" class="block px-4 py-2 hover:bg-gray-100">Delete</a></li>
        </ul>
      </div>
    </div>

    <!-- Registrations Section (Dropdown) -->
    <div class="mb-4 border-b border-gray-600 pb-4">
      <h2 class="text-lg font-medium cursor-pointer" id="registrationsDropdown">Registrations</h2>
      <!-- Dropdown for Registrations -->
      <div id="registrationsMenu" class="hidden">
        <ul class="py-2 text-sm text-gray-700">
          <li><a href="add_registration.php" class="block px-4 py-2 hover:bg-gray-100">Add</a></li>
          <li><a href="edit_registration.php" class="block px-4 py-2 hover:bg-gray-100">Edit</a></li>
          <li><a href="delete_registration.php" class="block px-4 py-2 hover:bg-gray-100">Delete</a></li>
        </ul>
      </div>
    </div>
  </div>

  <!-- Main Content Area -->
  <div class="flex-1 p-5">
    <h1 class="text-2xl font-semibold">Admin Dashboard</h1>

    <!-- Users Data -->
    <div id="usersData" class="hidden mt-5">
      <h3 class="text-lg font-medium">All Users</h3>
      <?php
        try {
          $stmt = $pdo->prepare("SELECT * FROM users");
          $stmt->execute();
          $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

          if ($users) {
            echo '<table class="min-w-full table-auto border-collapse border border-gray-300">';
            echo '<thead>';
            echo '<tr class="bg-gray-200">';
            echo '<th class="px-4 py-2 border">ID</th>';
            echo '<th class="px-4 py-2 border">Username</th>';
            echo '<th class="px-4 py-2 border">Email</th>';
            echo '<th class="px-4 py-2 border">Actions</th>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';

            foreach ($users as $user) {
              echo '<tr>';
              echo '<td class="px-4 py-2 border">' . htmlspecialchars($user['id']) . '</td>';
              echo '<td class="px-4 py-2 border">' . htmlspecialchars($user['username']) . '</td>';
              echo '<td class="px-4 py-2 border">' . htmlspecialchars($user['email']) . '</td>';
              echo '<td class="px-4 py-2 border">';
              echo '<a href="edit_user.php?id=' . $user['id'] . '" class="text-blue-600 hover:underline">Edit</a> | ';
              echo '<a href="delete_user.php?id=' . $user['id'] . '" class="text-red-600 hover:underline">Delete</a>';
              echo '</td>';
              echo '</tr>';
            }

            echo '</tbody>';
            echo '</table>';
          } else {
            echo '<p>No users found.</p>';
          }
        } catch (PDOException $e) {
          echo 'Error: ' . $e->getMessage();
        }
      ?>
    </div>

    <!-- Categories Data -->
    <div id="categoriesData" class="hidden mt-5">
      <h3 class="text-lg font-medium">All Categories</h3>
      <?php
        try {
          $stmt = $pdo->prepare("SELECT * FROM categories");
          $stmt->execute();
          $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

          if ($categories) {
            echo '<table class="min-w-full table-auto border-collapse border border-gray-300">';
            echo '<thead>';
            echo '<tr class="bg-gray-200">';
            echo '<th class="px-4 py-2 border">ID</th>';
            echo '<th class="px-4 py-2 border">Category Name</th>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';

            foreach ($categories as $category) {
              echo '<tr>';
              echo '<td class="px-4 py-2 border">' . htmlspecialchars($category['id']) . '</td>';
              echo '<td class="px-4 py-2 border">' . htmlspecialchars($category['category_name']) . '</td>';
              echo '</tr>';
            }

            echo '</tbody>';
            echo '</table>';
          } else {
            echo '<p>No categories found.</p>';
          }
        } catch (PDOException $e) {
          echo 'Error: ' . $e->getMessage();
        }
      ?>
    </div>

    <!-- Events Data -->
    <div id="eventsData" class="hidden mt-5">
      <h3 class="text-lg font-medium">All Events</h3>
      <?php
        try {
          $stmt = $pdo->prepare("SELECT * FROM events");
          $stmt->execute();
          $events = $stmt->fetchAll(PDO::FETCH_ASSOC);

          if ($events) {
            echo '<table class="min-w-full table-auto border-collapse border border-gray-300">';
            echo '<thead>';
            echo '<tr class="bg-gray-200">';
            echo '<th class="px-4 py-2 border">ID</th>';
            echo '<th class="px-4 py-2 border">Event Title</th>';
            echo '<th class="px-4 py-2 border">Event Description</th>';
            echo '<th class="px-4 py-2 border">Start Date</th>';
            echo '<th class="px-4 py-2 border">End Date</th>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';

            foreach ($events as $event) {
              echo '<tr>';
              echo '<td class="px-4 py-2 border">' . htmlspecialchars($event['id']) . '</td>';
              echo '<td class="px-4 py-2 border">' . htmlspecialchars($event['event_title']) . '</td>';
              echo '<td class="px-4 py-2 border">' . htmlspecialchars($event['event_description']) . '</td>';
              echo '<td class="px-4 py-2 border">' . htmlspecialchars($event['start_date']) . '</td>';
              echo '<td class="px-4 py-2 border">' . htmlspecialchars($event['end_date']) . '</td>';
              echo '</tr>';
            }

            echo '</tbody>';
            echo '</table>';
          } else {
            echo '<p>No events found.</p>';
          }
        } catch (PDOException $e) {
          echo 'Error: ' . $e->getMessage();
        }
      ?>
    </div>

    <!-- Registrations Data -->
    <div id="registrationsData" class="hidden mt-5">
      <h3 class="text-lg font-medium">All Registrations</h3>
      <?php
        try {
          $stmt = $pdo->prepare("SELECT * FROM registrations");
          $stmt->execute();
          $registrations = $stmt->fetchAll(PDO::FETCH_ASSOC);

          if ($registrations) {
            echo '<table class="min-w-full table-auto border-collapse border border-gray-300">';
            echo '<thead>';
            echo '<tr class="bg-gray-200">';
            echo '<th class="px-4 py-2 border">ID</th>';
            echo '<th class="px-4 py-2 border">User ID</th>';
            echo '<th class="px-4 py-2 border">Event ID</th>';
            echo '<th class="px-4 py-2 border">Registration Date</th>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';

            foreach ($registrations as $registration) {
              echo '<tr>';
              echo '<td class="px-4 py-2 border">' . htmlspecialchars($registration['id']) . '</td>';
              echo '<td class="px-4 py-2 border">' . htmlspecialchars($registration['user_id']) . '</td>';
              echo '<td class="px-4 py-2 border">' . htmlspecialchars($registration['event_id']) . '</td>';
              echo '<td class="px-4 py-2 border">' . htmlspecialchars($registration['registration_date']) . '</td>';
              echo '</tr>';
            }

            echo '</tbody>';
            echo '</table>';
          } else {
            echo '<p>No registrations found.</p>';
          }
        } catch (PDOException $e) {
          echo 'Error: ' . $e->getMessage();
        }
      ?>
    </div>
  </div>
</main>

<script>
  // Function to hide all data sections
  function hideAllDataSections() {
    document.getElementById('usersData').classList.add('hidden');
    document.getElementById('categoriesData').classList.add('hidden');
    document.getElementById('eventsData').classList.add('hidden');
    document.getElementById('registrationsData').classList.add('hidden');
  }

  // Toggle the dropdown visibility and show respective data
  document.querySelector('#usersDropdown').addEventListener('click', function() {
    hideAllDataSections(); // Hide all data sections
    document.getElementById('usersMenu').classList.toggle('hidden');
    document.getElementById('usersData').classList.toggle('hidden');
  });

  document.querySelector('#categoriesDropdown').addEventListener('click', function() {
    hideAllDataSections(); // Hide all data sections
    document.getElementById('categoriesMenu').classList.toggle('hidden');
    document.getElementById('categoriesData').classList.toggle('hidden');
  });

  document.querySelector('#eventsDropdown').addEventListener('click', function() {
    hideAllDataSections(); // Hide all data sections
    document.getElementById('eventsMenu').classList.toggle('hidden');
    document.getElementById('eventsData').classList.toggle('hidden');
  });

  document.querySelector('#registrationsDropdown').addEventListener('click', function() {
    hideAllDataSections(); // Hide all data sections
    document.getElementById('registrationsMenu').classList.toggle('hidden');
    document.getElementById('registrationsData').classList.toggle('hidden');
  });
</script>


<?php include('footer.php'); ?>
