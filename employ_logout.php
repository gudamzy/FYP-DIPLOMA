<?php
session_start();

# Destroy and clear all session variables if they exist
session_unset();
session_destroy();

# Redirect to the main page
header("Location: mainpage.html");
exit();
?>
