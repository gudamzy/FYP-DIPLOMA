<?php
require_once __DIR__ . '/includes/security.php';
require_admin(); # only admins can delete employees
 

$page_title = 'Delete Employee';
include ('./includes/header_admin.html');#header

# Check if the form has been submitted.
if (isset($_POST['submit'])) {
    csrf_check();

    require_once ('mysqli.php'); #dbc connection
    global $dbc;

    $errors = array(); #error

    # check employee ID selection.
    if (empty($_POST['id'])) {
        $errors[] = 'You forgot to select an employee.';
    } else {
        $employee_id = (int)$_POST['id'];
    }

    if (empty($errors)) { #if free error

        # Delete the employee from the database.
        $stmt = mysqli_prepare($dbc, "DELETE FROM rms_employee WHERE employee_id = ?");
        mysqli_stmt_bind_param($stmt, 'i', $employee_id);
        try {
            mysqli_stmt_execute($stmt);
            $deleted = mysqli_stmt_affected_rows($stmt);
        } catch (mysqli_sql_exception $ex) {
            $deleted = 0; # e.g. the record is still linked to other data
        }
        mysqli_stmt_close($stmt);

        if ($deleted > 0) { 

            # Print a message.
            echo '<h1 id="mainhead">Employee Deleted</h1>
                  <p>The employee has been deleted.</p><p><br /></p>';

        } else { # If there is error
            echo '<h1 id="mainhead">System Error</h1>
                  <p class="error">The employee could not be deleted due to a system error. We apologize for any inconvenience.</p>'; // Public message.
        }

        mysqli_close($dbc); # Close the database connection.
        exit();

    } else { # Report the errors.

        echo '<h1 id="mainhead">Error!</h1>
              <p class="error">The following error(s) occurred:<br />';
        foreach ($errors as $msg) { # Print each error.
            echo " - $msg<br />\n";
        }
        echo '</p><p>Please try again.</p><p><br /></p>';

    } 


} 

?>

<h2>Delete Employee</h2>
<?php

# Display the form.

require_once ('mysqli.php'); # dbc connection
global $dbc;

$query = "SELECT employee_id, username FROM rms_employee";
$result = @mysqli_query($dbc, $query);# run the dbc,query

if (mysqli_num_rows($result) > 0) {
    echo '<div class="form-container">
          <form action="employ_delete.php" method="post"> ' . csrf_field() . '
          <p>Employee: 
          <select name="id" class="input-field">';

    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        echo '<option value="' . (int)$row['employee_id'] . '">' . e($row['username']) . '</option>';
    }

    echo '</select></p>
          <p><input type="submit" name="submit" value="DELETE Employee" class="submit-btn" /></p>
          </form>
          </div>';
} else {
    echo '<p>No employees available to delete.</p>';
}

mysqli_close($dbc); // Close the database connection.

include ('./includes/footer.html');

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
        background-color: #e94e77;
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
        background-color: #c04e66;
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
