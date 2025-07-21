<?php
session_start();

// Clear all session data
session_unset();
session_destroy();

// Delete remember me cookie if it exists
if (isset($_COOKIE['jeopardy_user'])) {
    setcookie('jeopardy_user', '', time() - 3600, "/");
}

// Redirect to login page
header("Location: login.php");
exit();
?>
