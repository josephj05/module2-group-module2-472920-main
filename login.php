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
        handle_login($file, $login_dates_file, $log_file);
    }

    if (isset($_POST['upload']) && isset($_FILES['fileToUpload'])) {
        handle_file_upload($log_file);
    }

    if (isset($_POST['delete']) && isset($_POST['file_to_delete'])) {
        handle_file_delete($log_file);
    }
} else {
    // Show shared files for logged-in users
    if (isset($_SESSION['logged_in_user'])) {
        show_shared_files($log_file);  // Display the shared files to all users
    }
}
?>
