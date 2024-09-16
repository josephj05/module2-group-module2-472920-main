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
