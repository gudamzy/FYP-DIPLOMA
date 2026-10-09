<?php 

$page_title = 'Employee Register';
include ('./includes/header_admin.html'); #header

# Check if the form has been submitted
if (isset($_POST['submit'])) {

    require_once ('mysqli.php'); # dbc connection
    global $dbc;

    $errors = array(); # error array

    # Check if required fields (username, email, password) are filled
    if (empty($_POST['username']) || empty($_POST['email']) || empty($_POST['password'])) {
        $errors[] = 'Please enter username, email, and password.';
    }

    # Check if the email contains '@'
    if (isset($_POST['email']) && strpos($_POST['email'], '@') === false) {
        $errors[] = 'Email must contain the "@" symbol.';
    }

    # Check if the phone number starts with 60 (for Malaysia numbers)
    if (isset($_POST['phone_number']) && !preg_match('/^60[0-9]{9,10}$/', $_POST['phone_number'])) {
        $errors[] = 'Phone number must start with 60 and contain 9 or 10 digits.';
    }

    # Store other form data; age, gender, address can be optional
    $age = !empty($_POST['age']) ? $_POST['age'] : null;
    $gender = !empty($_POST['gender']) ? $_POST['gender'] : null;
    $address = !empty($_POST['address']) ? $_POST['address'] : null;
    $phone_number = !empty($_POST['phone_number']) ? $_POST['phone_number'] : null;

    # Encrypt the password
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    # If there are no errors, proceed to insert data
    if (empty($errors)) {

        # Check if the username or email already exists
        $query = "SELECT username, email FROM rms_employee WHERE username='{$_POST['username']}' OR email='{$_POST['email']}'";
        $result = @mysqli_query($dbc, $query); # Run the dbc,query
        if (mysqli_num_rows($result) == 0) {

            # Insert new employee data into the database
            $query = "INSERT INTO rms_employee (username, email, password, age, gender, address, phone_number) 
                      VALUES ('{$_POST['username']}', '{$_POST['email']}', '{$password}', '{$age}', '{$gender}', '{$address}', '{$phone_number}')";
            $result = @mysqli_query($dbc, $query); # Run the dbc,query

            if (mysqli_affected_rows($dbc) > 0) {
                # Success message
                echo '<h1 id="mainhead">Thank you!</h1>
                      <p>Employee ' . $_POST['username'] . ' has been successfully added.</p><p><br /></p>';    
                include ('./includes/footer.html'); # Include footer and exit
                exit();
            } else {
                # System error message
                echo '<h1 id="mainhead">System Error</h1>
                      <p class="error">There was an error adding the employee. We apologize for any inconvenience.</p>'; 
                echo '<p>' . mysqli_error($dbc) . '</p>'; # Debugging info
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
            echo " - $msg<br />\n";
        }
        echo '</p><p>Please try again.</p><p><br /></p>';
    }

    mysqli_close($dbc); # Close the database connection
}

?>

<h2>Add Employee</h2>
<form action="employ_add.php" method="post" class="form-container">
    <p>Username: <input type="text" name="username" size="20" maxlength="30" value="<?php if (isset($_POST['username'])) echo $_POST['username']; ?>" class="input-field" /></p>
    <p>Email: <input type="email" name="email" size="20" maxlength="50" value="<?php if (isset($_POST['email'])) echo $_POST['email']; ?>" class="input-field" /></p>
    <p>Password: <input type="password" name="password" size="20" maxlength="50" class="input-field" /></p>
    <p>Age: <input type="number" name="age" size="5" value="<?php if (isset($_POST['age'])) echo $_POST['age']; ?>" class="input-field" /></p>
    <p>Gender: 
        <select name="gender" class="input-field">
            <option value="Male" <?php if (isset($_POST['gender']) && $_POST['gender'] == 'Male') echo 'selected'; ?>>Male</option>
            <option value="Female" <?php if (isset($_POST['gender']) && $_POST['gender'] == 'Female') echo 'selected'; ?>>Female</option>
        </select>
    </p>
    <p>Address: <input type="text" name="address" size="30" maxlength="100" value="<?php if (isset($_POST['address'])) echo $_POST['address']; ?>" class="input-field" /></p>
    <p>Phone Number: <input type="text" name="phone_number" size="15" maxlength="15" value="<?php if (isset($_POST['phone_number'])) echo $_POST['phone_number']; ?>" placeholder="+60xxxxxxxxx" class="input-field" /></p>
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
