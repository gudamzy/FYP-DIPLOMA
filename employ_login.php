<?php
require_once __DIR__ . '/includes/security.php'; # safe session + helpers
require_once "mysqli.php";  #dbc connection

# hold error messages
$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    csrf_check();

    if (login_blocked('employee')) {
        $error = "Too many failed attempts. Please wait 5 minutes and try again.";
    } else {
        # Get form inputs (no escaping needed: we use a prepared statement)
        $username = trim($_POST['username'] ?? '');
        $password = (string)($_POST['password'] ?? '');

        # Look up the user safely
        $stmt = mysqli_prepare($dbc, "SELECT employee_id, password FROM rms_employee WHERE username = ? LIMIT 1");
        mysqli_stmt_bind_param($stmt, 's', $username);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $found_id, $found_password);
        $row = mysqli_stmt_fetch($stmt) ? ['employee_id' => $found_id, 'password' => $found_password] : null;
        mysqli_stmt_close($stmt);

        if ($row && verify_and_upgrade_password($dbc, 'rms_employee', 'employee_id', $row['employee_id'], $password, $row['password'])) {
            login_succeeded('employee');
            login_session_refresh();
            # Save user information in session
            $_SESSION['employee_id'] = $row['employee_id'];
            $_SESSION['username'] = $username;

            # Redirect location
            header("Location: employ.php");
            exit();
        }

        login_failed('employee');
        # Same message for wrong username or wrong password (does not reveal which usernames exist)
        $error = "Invalid username or password.";
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
    <title>Employee Login</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(to right, #00b4db, #0083b0);
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
            color: #007acc;
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
            border-color: #007acc;
            box-shadow: 0 0 5px rgba(0, 122, 204, 0.5);
        }

        .login-container input[type="submit"] {
            background: #007acc;
            color: #fff;
            border: none;
            cursor: pointer;
            transition: background 0.3s;
        }

        .login-container input[type="submit"]:hover {
            background: #005b99;
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
    <h2>Employee Login</h2>

    <!-- Display error messages -->
    <?php if (!empty($error)) echo "<p class='error'>" . e($error) . "</p>"; ?>

    <form action="employ_login.php" method="post">
        <?php echo csrf_field(); ?>
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
