<?php 
require_once __DIR__ . '/includes/security.php';
require_admin(); # only admins can add employees

$page_title = 'Employee Register';
include ('./includes/header_admin.html'); #header

# Check if the form has been submitted
if (isset($_POST['submit'])) {
    csrf_check();

    require_once ('mysqli.php'); # dbc connection
    global $dbc;

    $errors = array(); # error array

    $username_in = trim($_POST['username'] ?? '');
    $email_in    = trim($_POST['email'] ?? '');
    $password_in = (string)($_POST['password'] ?? '');

    # Check if required fields (username, email, password) are filled
    if ($username_in === '' || $email_in === '' || $password_in === '') {
        $errors[] = 'Please enter username, email, and password.';
    }

    # Check the email is valid
    if ($email_in !== '' && !filter_var($email_in, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address.';
    }

    # Password must be at least 8 characters
    if ($password_in !== '' && strlen($password_in) < 8) {
        $errors[] = 'Password must be at least 8 characters.';
    }

    # Check if the phone number starts with 60 (for Malaysia numbers)
    if (isset($_POST['phone_number']) && !preg_match('/^60[0-9]{9,10}$/', $_POST['phone_number'])) {
        $errors[] = 'Phone number must start with 60 and contain 9 or 10 digits.';
    }

    # Store other form data; age, gender, address can be optional
    $age          = (isset($_POST['age']) && $_POST['age'] !== '') ? (int)$_POST['age'] : null;
    $gender       = in_array($_POST['gender'] ?? '', ['Male', 'Female'], true) ? $_POST['gender'] : '';
    $address      = trim($_POST['address'] ?? '');
    $phone_number = trim($_POST['phone_number'] ?? '');

    # If there are no errors, proceed to insert data
    if (empty($errors)) {
        # Encrypt the password
        $password = password_hash($password_in, PASSWORD_DEFAULT);

        # Check if the username or email already exists
        $stmt = mysqli_prepare($dbc, "SELECT employee_id FROM rms_employee WHERE username = ? OR email = ?");
        mysqli_stmt_bind_param($stmt, 'ss', $username_in, $email_in);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        $exists = mysqli_stmt_num_rows($stmt) > 0;
        mysqli_stmt_close($stmt);

        if (!$exists) {
            # Insert new employee data into the database
            $stmt = mysqli_prepare($dbc, "INSERT INTO rms_employee (username, email, password, age, gender, address, phone_number) VALUES (?, ?, ?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, 'sssisss', $username_in, $email_in, $password, $age, $gender, $address, $phone_number);
            $ok = mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            if ($ok) {
                # Success message
                echo '<h1 id="mainhead">Thank you!</h1>
                      <p>Employee ' . e($username_in) . ' has been successfully added.</p><p><br /></p>';
                include ('./includes/footer.html'); # Include footer and exit
                exit();
            } else {
                # System error message (no technical details shown)
                echo '<h1 id="mainhead">System Error</h1>
                      <p class="error">There was an error adding the employee. Please try again.</p>';
                include ('./includes/footer.html');
                exit();
            }
        } else { # If username or email already exists
            echo '<h1 id="mainhead">Error!</h1>
                  <p class="error">This username or email already exists.</p>';
        }
    } else { # error message
        echo '<h1 id="mainhead">Error!</h1>
              <p class="error">The following errors occurred:<br />';
        foreach ($errors as $msg) { # Loop through and print each error
            echo ' - ' . e($msg) . "<br />\n";
        }
        echo '</p><p>Please try again.</p><p><br /></p>';
    }

    mysqli_close($dbc); # Close the database connection
}
?>

<h2>Add Employee</h2>
<form action="employ_add.php" method="post" class="form-container">
    <?php echo csrf_field(); ?>
    <p>Username: <input type="text" name="username" size="20" maxlength="30" value="<?php if (isset($_POST['username'])) echo e($_POST['username']); ?>" class="input-field" /></p>
    <p>Email: <input type="email" name="email" size="20" maxlength="50" value="<?php if (isset($_POST['email'])) echo e($_POST['email']); ?>" class="input-field" /></p>
    <p>Password: <input type="password" name="password" size="20" maxlength="72" minlength="8" class="input-field" /></p>
    <p>Age: <input type="number" name="age" size="5" value="<?php if (isset($_POST['age'])) echo e($_POST['age']); ?>" class="input-field" /></p>
    <p>Gender: 
        <select name="gender" class="input-field">
            <option value="Male" <?php if (isset($_POST['gender']) && $_POST['gender'] == 'Male') echo 'selected'; ?>>Male</option>
            <option value="Female" <?php if (isset($_POST['gender']) && $_POST['gender'] == 'Female') echo 'selected'; ?>>Female</option>
        </select>
    </p>
    <p>Address: <input type="text" name="address" size="30" maxlength="100" value="<?php if (isset($_POST['address'])) echo e($_POST['address']); ?>" class="input-field" /></p>
    <p>Phone Number: <input type="text" name="phone_number" size="15" maxlength="15" value="<?php if (isset($_POST['phone_number'])) echo e($_POST['phone_number']); ?>" placeholder="+60xxxxxxxxx" class="input-field" /></p>
    <p><input type="submit" name="submit" value="Add Employee" class="submit-btn" /></p>
</form>

<?php
include ('./includes/footer.html'); #footer
?>

<style>
    body {
        font-family: 'Arial', sans-serif;
        background-color: #f9f9f9;
        color: #333;
        margin: 0;
        padding: 0;
    }

    h1, h2 {
        text-align: center;
        color: #4a90e2;
    }

    .form-container {
        max-width: 700px;
        margin: 20px auto;
        background-color: #fff;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
    }

    .form-container p {
        margin-bottom: 20px;
    }

    .input-field {
        width: 100%;
        padding: 12px;
        border-radius: 8px;
        border: 1px solid #ccc;
        font-size: 16px;
        margin-top: 8px;
    }

    .input-field:focus {
        outline: none;
        border-color: #4a90e2;
        box-shadow: 0 0 8px rgba(74, 144, 226, 0.2);
    }

    .submit-btn {
        background-color: #4a90e2;
        color: white;
        border: none;
        padding: 14px;
        font-size: 18px;
        cursor: pointer;
        border-radius: 8px;
        width: 100%;
        transition: background-color 0.3s ease;
    }

    .submit-btn:hover {
        background-color: #357abd;
    }

    .error {
        color: red;
        font-size: 14px;
        text-align: center;
    }

    .input-field.error {
        border-color: red;
        background-color: #ffe6e6;
    }
</style>
