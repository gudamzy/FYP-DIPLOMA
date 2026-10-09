<?php

require_once "mysqli.php";  #dbc connection

session_start();

# hold error messages
$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    # Get form inputs
    $username = mysqli_real_escape_string($dbc, $_POST['username']);
    $password = mysqli_real_escape_string($dbc, $_POST['password']);

    # Query the database for the entered username
    $query = "SELECT admin_id, password FROM rms_admin WHERE username = '$username'";
    $result = mysqli_query($dbc, $query);

    if ($result && mysqli_num_rows($result) == 1) {
        # Fetch  details
        $row = mysqli_fetch_assoc($result);

        # Verify the password directly (no hashing)
        if ($password == $row['password']) {
            # Save user information in session
            $_SESSION['admin_id'] = $row['admin_id'];  
            $_SESSION['username'] = $username;

            # Redirect location
            header("Location: admin.php");
            exit();
        } else {
            #error message
            $error = "Invalid password.";
        }
    } else {
        $error = "Invalid username or user does not exist.";
    }
}

# Close the connection
mysqli_close($dbc);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(to right, #ff7e5f, #feb47b);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: #333;
        }

        .login-container {
            background: #fff;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            width: 90%;
            max-width: 400px;
            text-align: center;
        }

        .login-container h2 {
            margin-bottom: 20px;
            font-size: 1.8rem;
            color: #ff5722;
        }

        .login-container form {
            display: flex;
            flex-direction: column;
        }

        .login-container label {
            text-align: left;
            font-size: 1rem;
            margin-bottom: 5px;
            color: #555;
        }

        .login-container input {
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
        }

        .login-container input:focus {
            outline: none;
            border-color: #ff5722;
            box-shadow: 0 0 5px rgba(255, 87, 34, 0.5);
        }

        .login-container input[type="submit"] {
            background: #ff5722;
            color: #fff;
            border: none;
            cursor: pointer;
            transition: background 0.3s;
        }

        .login-container input[type="submit"]:hover {
            background: #e64a19;
        }

        .error {
            color: red;
            margin-bottom: 15px;
        }

        footer {
            margin-top: 20px;
            font-size: 0.8rem;
            color: #777;
        }
    </style>
</head>
<body>

<div class="login-container">
    <h2>Admin Login</h2>

    <!-- Display error messages -->
    <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>

    <form action="admin_login.php" method="post">
        <label for="username">Username:</label>
        <input type="text" name="username" id="username" placeholder="Enter your username" required>
        
        <label for="password">Password:</label>
        <input type="password" name="password" id="password" placeholder="Enter your password" required>
        
        <input type="submit" value="Login">
    </form>

    <footer>&copy; 2024 Restaurant Management System</footer>
</div>

</body>
</html>
