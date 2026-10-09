<?php
session_start();

# Check if login
if (isset($_SESSION['user_id'])) {
    # Destroy and clear all session 
    session_unset();  

    session_destroy(); 

    # Redirect to the admin login page
    header("Location: admin_login.php");
    exit(); 
}

# If the user is not logged in, redirect to the admin login page
header("Location: mainpage.html");
exit();
?>
