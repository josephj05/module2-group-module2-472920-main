<?php
$log_file = '../data/upload_log.txt';

function handle_file_upload($log_file) {
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

        display_logout_button();
    } else {
        echo "You must be logged in to upload files.<br>";
    }
}

// Handle file deletion (similar to upload process)
function handle_file_delete($log_file) {
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

    display_logout_button();
}
?>
