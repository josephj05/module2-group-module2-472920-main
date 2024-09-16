<?php
// File paths
$file = '../data/user.txt';
$login_dates_file = '../data/last_login.txt';

// Handle logout function
function handle_logout() {
    session_destroy();  // Destroy the session
    header('Location: login.html');  // Redirect back to login page
    exit();  // Stop further script execution
}

// Handle user login
function handle_login($file, $login_dates_file, $log_file) {
    $username = trim($_POST['username']);
    $users = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    if (in_array($username, $users)) {
        $_SESSION['logged_in_user'] = $username;
        log_last_login($login_dates_file, $username);
        $last_login = get_last_login($login_dates_file, $username);

        echo "Welcome, " . htmlspecialchars($username) . "! You are now logged in.<br>";
        if ($last_login) {
            echo "Your last login was on: " . htmlspecialchars($last_login) . "<br>";
        }

        display_upload_form();
        show_user_files($log_file, $username);
        display_logout_button();
    } else {
        echo "Invalid username. Please try again.";
    }
}
?>
