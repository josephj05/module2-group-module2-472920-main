<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login Page</title>
</head>
<body>

<?php
// Include necessary files
require_once 'auth.php';
require_once 'functions.php';
require_once 'upload.php';

session_start();  // Start the session

// Handle logout
if (isset($_POST['logout'])) {
    handle_logout();
}

// Handle POST requests
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['username'])) {
        handle_login($file, $login_dates_file, $log_file);  // Login logic
    }

    if (isset($_POST['upload']) && isset($_FILES['fileToUpload'])) {
        handle_file_upload($log_file);  // File upload logic
    }

    if (isset($_POST['delete']) && isset($_POST['file_to_delete'])) {
        handle_file_delete($log_file);  // File deletion logic
    }
} else {
    // Show shared files for logged-in users
    if (isset($_SESSION['logged_in_user'])) {
        show_shared_files($log_file);  // Display the shared files to all users
    }
}

// Ensure the logout button is always displayed when the user is logged in
if (isset($_SESSION['logged_in_user'])) {
    echo '<h2>Welcome, ' . htmlspecialchars($_SESSION['logged_in_user']) . '!</h2>';

    // Display the logout button
    echo '<form action="login.php" method="post">
              <input type="submit" name="logout" value="Logout">
          </form>';
} else {
    // If the user is not logged in, show the login form
    echo '<h2>Login</h2>';
    echo '<form action="login.php" method="post">
              <label for="username">Username:</label>
              <input type="text" id="username" name="username" required>
              <br><br>
              <input type="submit" value="Login">
          </form>';
}

?>

</body>
</html>
