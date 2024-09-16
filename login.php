<?php
// Path to the user.txt file outside public_html
$file = '../data/user.txt';
$log_file = '../data/upload_log.txt';  // Log file to store uploads
$login_dates_file = '../data/last_login.txt'; // File to store last login dates

// Start the session to keep track of the logged-in user
session_start();

// Handle logout
if (isset($_POST['logout'])) {
    session_destroy();  // Destroy the session
    header('Location: login.html');  // Redirect back to login page
    exit();  // Stop further script execution
}

// Inline CSS for styling the page
echo '<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f4;
        margin: 0;
        padding: 20px;
        display: flex;
        justify-content: center;
    }

    .container {
        background-color: #fff;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
        width: 400px;
    }

    h1 {
        text-align: center;
        color: #333;
    }

    form {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    input[type="text"], input[type="file"] {
        padding: 10px;
        border-radius: 5px;
        border: 1px solid #ccc;
        width: 100%;
    }

    input[type="submit"] {
        padding: 10px;
        background-color: #28a745;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 16px;
    }

    input[type="submit"]:hover {
        background-color: #218838;
    }

    .logout-btn {
        background-color: #dc3545;
        margin-top: 10px;
    }

    .logout-btn:hover {
        background-color: #c82333;
    }

    .file-list ul {
        list-style-type: none;
        padding: 0;
    }

    .file-list ul li {
        padding: 10px 0;
        border-bottom: 1px solid #ccc;
    }

    .file-list ul li a {
        color: #007bff;
        text-decoration: none;
    }

    .file-list ul li a:hover {
        text-decoration: underline;
    }

    .file-list form {
        display: inline;
    }

    .file-list input[type="submit"] {
        background-color: #ffc107;
        color: #333;
        padding: 5px 10px;
        font-size: 14px;
    }
</style>';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Handle login form submission
    if (isset($_POST['username'])) {
        $username = trim($_POST['username']);
        $users = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        if (in_array($username, $users)) {
            $_SESSION['logged_in_user'] = $username;
            log_last_login($login_dates_file, $username);
            $last_login = get_last_login($login_dates_file, $username);

            echo '<div class="container">';
            echo "<h1>Welcome, " . htmlspecialchars($username) . "!</h1>";
            if ($last_login) {
                echo "<p>Your last login was on: " . htmlspecialchars($last_login) . "</p>";
            }

            // File upload form
            echo '
                <form action="login.php" method="post" enctype="multipart/form-data">
                    <label for="fileToUpload">Choose a file to upload:</label>
                    <input type="file" name="fileToUpload" id="fileToUpload">
                    <input type="submit" value="Upload File" name="upload">
                </form>
            ';

            show_user_files($log_file, $username);

            echo '<form action="login.php" method="post">
                      <input type="submit" name="logout" value="Logout" class="logout-btn">
                  </form>';
            echo '</div>';
        } else {
            echo "Invalid username. Please try again.";
        }
    }

    // Handle file upload
    if (isset($_POST['upload']) && isset($_FILES['fileToUpload'])) {
        if (isset($_SESSION['logged_in_user'])) {
            $upload_dir = 'uploads/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            $target_file = $upload_dir . basename($_FILES["fileToUpload"]["name"]);

            if (file_exists($target_file)) {
                $file_info = pathinfo($target_file);
                $new_filename = $file_info['filename'] . '_' . time() . '.' . $file_info['extension'];
                $target_file = $upload_dir . $new_filename;
            }

            $uploadOk = 1;
            $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            if ($_FILES["fileToUpload"]["size"] > 5000000) {
                echo "Sorry, your file is too large (max 5MB).<br>";
                $uploadOk = 0;
            }

            if ($uploadOk == 0) {
                echo "Sorry, your file was not uploaded.<br>";
            } else {
                if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
                    echo "The file " . htmlspecialchars(basename($target_file)) . " has been uploaded.<br>";
                    file_put_contents($log_file, $_SESSION['logged_in_user'] . '|' . $target_file . "\n", FILE_APPEND);
                    show_user_files($log_file, $_SESSION['logged_in_user']);
                } else {
                    echo "Sorry, there was an error uploading your file.<br>";
                }
            }

            echo '<form action="login.php" method="post">
                      <input type="submit" name="logout" value="Logout" class="logout-btn">
                  </form>';
        } else {
            echo "You must be logged in to upload files.<br>";
        }
    }

    // Handle file deletion
    if (isset($_POST['delete']) && isset($_POST['file_to_delete'])) {
        if (isset($_SESSION['logged_in_user'])) {
            $file_to_delete = $_POST['file_to_delete'];
            if (file_exists($file_to_delete)) {
                unlink($file_to_delete);
                $updated_log = array();
                $uploads = file($log_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                foreach ($uploads as $upload) {
                    list($logged_user, $file_path) = explode('|', $upload);
                    if ($logged_user !== $_SESSION['logged_in_user'] || $file_path !== $file_to_delete) {
                        $updated_log[] = $upload;
                    }
                }
                file_put_contents($log_file, implode("\n", $updated_log) . "\n");

                echo "File has been deleted.<br>";
                show_user_files($log_file, $_SESSION['logged_in_user']);
            } else {
                echo "File does not exist.<br>";
            }

            echo '<form action="login.php" method="post">
                      <input type="submit" name="logout" value="Logout" class="logout-btn">
                  </form>';
        }
    }
} else {
    echo "Invalid request method.";
}

// Function to show uploaded files for the logged-in user with delete buttons
function show_user_files($log_file, $username) {
    if (file_exists($log_file)) {
        $uploads = file($log_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $user_files = array();

        foreach ($uploads as $upload) {
            list($logged_user, $file_path) = explode('|', $upload);
            if ($logged_user === $username) {
                $user_files[] = $file_path;
            }
        }

        if (!empty($user_files)) {
            echo "<div class='file-list'><h3>Your Uploaded Files:</h3><ul>";
            foreach ($user_files as $file) {
                echo "<li>";
                echo "<a href='$file'>" . basename($file) . "</a> ";
                echo "<form action='login.php' method='post'>
                          <input type='hidden' name='file_to_delete' value='$file'>
                          <input type='submit' name='delete' value='Delete'>
                      </form>";
                echo "</li>";
            }
            echo "</ul></div>";
        } else {
            echo "You have not uploaded any files yet.<br>";
        }
    }
}

// Function to log the last login date for a user
function log_last_login($file, $username) {
    $current_time = date("Y-m-d H:i:s");
    $logins = file_exists($file) ? file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) : array();
    $updated_logins = array();
    $user_found = false;
    foreach ($logins as $login) {
        list($logged_user, $login_time) = explode('|', $login);
        if ($logged_user === $username) {
            $updated_logins[] = $username . '|' . $current_time;
            $user_found = true;
        } else {
            $updated_logins[] = $login;
        }
    }
    if (!$user_found) {
        $updated_logins[] = $username . '|' . $current_time;
    }
    file_put_contents($file, implode("\n", $updated_logins) . "\n");
}

// Function to get the last login date for a user
function get_last_login($file, $username) {
    if (file_exists($file)) {
        $logins = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($logins as $login) {
            list($logged_user, $login_time) = explode('|', $login);
            if ($logged_user === $username) {
                return $login_time;
            }
        }
    }
    return null;
}
?>
