<?php
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

function show_shared_files($log_file) {
    if (file_exists($log_file)) {
        // Read the log file to get uploaded files and their owners
        $uploads = file($log_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $shared_files = array();

        // Collect all files from the log
        foreach ($uploads as $upload) {
            list($logged_user, $file_path) = explode('|', $upload);
            $shared_files[] = array('user' => $logged_user, 'file' => $file_path);
        }

        // Display the list of shared files with uploader info and delete button for the uploader
        if (!empty($shared_files)) {
            echo "<h3>Shared Files:</h3><ul>";
            foreach ($shared_files as $shared_file) {
                echo "<li>";
                echo "<a href='" . $shared_file['file'] . "' download>" . basename($shared_file['file']) . "</a> ";
                echo "<em>Uploaded by: " . htmlspecialchars($shared_file['user']) . "</em>";

                // If the current user is the uploader, display a delete button
                if ($shared_file['user'] === $_SESSION['logged_in_user']) {
                    echo "<form action='login.php' method='post' style='display:inline;'>
                            <input type='hidden' name='file_to_delete' value='" . $shared_file['file'] . "'>
                            <input type='submit' name='delete' value='Delete'>
                          </form>";
                }

                echo "</li>";
            }
            echo "</ul>";
        } else {
            echo "No files have been shared yet.<br>";
        }
    } else {
        echo "No files have been uploaded yet.<br>";
    }
}


// Function to display the upload form
function display_upload_form() {
    echo '
        <form action="login.php" method="post" enctype="multipart/form-data">
            <label for="fileToUpload">Choose a file to upload:</label><br>
            <input type="file" name="fileToUpload" id="fileToUpload"><br><br>
            <input type="submit" value="Upload File" name="upload">
        </form>
    ';
}

// Function to display the logout button
function display_logout_button() {
    echo '<form action="login.php" method="post">
              <input type="submit" name="logout" value="Logout">
          </form>';
}
?>
