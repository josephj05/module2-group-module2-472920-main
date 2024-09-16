<?php
// Path to the user.txt file outside public_html
$file = '../data/user.txt';
$log_file = '../data/upload_log.txt';  // Log file to store uploads

// Start the session to keep track of the logged-in user
session_start();

// Handle logout
if (isset($_POST['logout'])) {
    session_destroy();  // Destroy the session
    header('Location: login.html');  // Redirect back to login page
    exit();  // Stop further script execution
}

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Handle login form submission
    if (isset($_POST['username'])) {
        // Get the submitted username
        $username = trim($_POST['username']);

        // Read the usernames from the file
        $users = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        // Check if the username exists in the file
        if (in_array($username, $users)) {
            // Save the username in session for file upload handling
            $_SESSION['logged_in_user'] = $username;
            echo "Welcome, " . htmlspecialchars($username) . "! You are now logged in.<br>";

            // Display the file upload form
            echo '
                <form action="login.php" method="post" enctype="multipart/form-data">
                    <label for="fileToUpload">Choose a file to upload:</label><br>
                    <input type="file" name="fileToUpload" id="fileToUpload"><br><br>
                    <input type="submit" value="Upload File" name="upload">
                </form>
            ';

            // Show the list of uploaded files for this user
            show_user_files($log_file, $username);

            // Display the logout button
            echo '<form action="login.php" method="post">
                      <input type="submit" name="logout" value="Logout">
                  </form>';
        } else {
            echo "Invalid username. Please try again.";
        }
    }

    // Handle file upload form submission
    if (isset($_POST['upload']) && isset($_FILES['fileToUpload'])) {
        // Check if the user is logged in
        if (isset($_SESSION['logged_in_user'])) {
            // Directory for file uploads (relative to login.php)
            $upload_dir = 'uploads/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);  // Create the directory if it doesn't exist
            }

            // Define the file path to save the uploaded file
            $target_file = $upload_dir . basename($_FILES["fileToUpload"]["name"]);

            // If the file already exists, append a timestamp to avoid conflict
            if (file_exists($target_file)) {
                $file_info = pathinfo($target_file);
                $new_filename = $file_info['filename'] . '_' . time() . '.' . $file_info['extension'];
                $target_file = $upload_dir . $new_filename;
            }

            $uploadOk = 1;
            $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            // Check file size (limit to 5MB for example)
            if ($_FILES["fileToUpload"]["size"] > 5000000) {
                echo "Sorry, your file is too large (max 5MB).<br>";
                $uploadOk = 0;
            }

            // Check if $uploadOk is set to 0 by an error
            if ($uploadOk == 0) {
                echo "Sorry, your file was not uploaded.<br>";
            } else {
                // Move the uploaded file to the target directory
                if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
                    echo "The file " . htmlspecialchars(basename($target_file)) . " has been uploaded.<br>";
                    
                    // Log the upload (username and file path)
                    file_put_contents($log_file, $_SESSION['logged_in_user'] . '|' . $target_file . "\n", FILE_APPEND);

                    // Show the updated list of user's files
                    show_user_files($log_file, $_SESSION['logged_in_user']);
                } else {
                    echo "Sorry, there was an error uploading your file.<br>";
                }
            }

            // Display the logout button after uploading
            echo '<form action="login.php" method="post">
                      <input type="submit" name="logout" value="Logout">
                  </form>';
        } else {
            echo "You must be logged in to upload files.<br>";
        }
    }

    // Handle file deletion
    if (isset($_POST['delete']) && isset($_POST['file_to_delete'])) {
        if (isset($_SESSION['logged_in_user'])) {
            $file_to_delete = $_POST['file_to_delete'];
            // Delete the file from the server
            if (file_exists($file_to_delete)) {
                unlink($file_to_delete);  // Delete the file

                // Remove the file entry from the log
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

            // Display the logout button after deletion
            echo '<form action="login.php" method="post">
                      <input type="submit" name="logout" value="Logout">
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

        // Collect files uploaded by the logged-in user
        foreach ($uploads as $upload) {
            list($logged_user, $file_path) = explode('|', $upload);
            if ($logged_user === $username) {
                $user_files[] = $file_path;
            }
        }

        // Display user's files
        if (!empty($user_files)) {
            echo "<h3>Your Uploaded Files:</h3><ul>";
            foreach ($user_files as $file) {
                echo "<li>";
                echo "<a href='$file'>" . basename($file) . "</a> ";
                echo "<form action='login.php' method='post' style='display:inline;'>
                          <input type='hidden' name='file_to_delete' value='$file'>
                          <input type='submit' name='delete' value='Delete'>
                      </form>";
                echo "</li>";
            }
            echo "</ul>";
        } else {
            echo "You have not uploaded any files yet.<br>";
        }
    }
}
?>
