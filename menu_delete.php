<?php 

$page_title = 'Delete menu';
include ('./includes/header_admin.html');

# Check if the form has been submitted.
if (isset($_POST['submit'])) {

    require_once ('mysqli.php'); # dbc connection
    global $dbc;

    $errors = array(); # error

    # Check for menu selection.
    if (empty($_POST['id'])) {
        $errors[] = 'You forgot to select the menu.';
    } else {
        $id = $_POST['id'];
    }

    if (empty($errors)) { # no error

        # Delete the menu from the database.
        $query = "DELETE FROM rms_menu WHERE id=$id";
        $result = @mysqli_query($dbc, $query); # Run the dbc,query.

        if (mysqli_affected_rows($dbc) > 0) { 

            # Print a message.
            echo '<h1 id="mainhead">Menu Deleted</h1>
                  <p>The menu has been deleted.</p><p><br /></p>';

        } else { # if there is error
            echo '<h1 id="mainhead">System Error</h1>
                  <p class="error">The menu could not be deleted due to a system error. We apologize for any inconvenience.</p>'; 
            echo '<p>' . mysqli_error($dbc) . '<br /><br />Query: ' . $query . '</p>'; 
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

    mysqli_close($dbc); # Close the database connection.

} 

?>

<h2 class="form-header">Delete Menu</h2>
<form action="menu_delete.php" method="post" class="form-container">
    <div class="form-group">
        <label for="menu">Menu:</label>
        <select name="id" id="menu" class="form-control">
            <?php
            require_once ('mysqli.php'); # dbc connection.
            global $dbc;

            $query = "SELECT id, menu, description, price FROM rms_menu";
            $result = @mysqli_query($dbc, $query);

            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
                    echo '<option value="' . $row['id'] . '">' . $row['menu'] . ' - ' . $row['description'] . ' - $' . $row['price'] . '</option>';
                }
            } else {
                echo '<option value="">No menu available</option>';
            }

            mysqli_close($dbc); # Close the database connection.
            ?>
        </select>
    </div>

    <div class="form-group">
        <input type="submit" name="submit" value="DELETE Menu" class="btn-submit" />
    </div>
</form>

<?php
include ('./includes/footer.html');
?>

<!-- Add the CSS for improved design -->
<style>
    body {
        font-family: 'Arial', sans-serif;
        background-color: #f8f9fa;
        color: #333;
        margin: 0;
        padding: 0;
    }

    .form-header {
        text-align: center;
        color: #007bff;
        font-size: 2.2em;
        margin-top: 30px;
        margin-bottom: 20px;
        font-weight: 600;
    }

    .form-container {
        width: 50%;
        margin: 0 auto;
        background-color: #ffffff;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
    }

    .form-group {
        margin-bottom: 20px;
    }

    label {
        display: block;
        font-weight: 500;
        color: #333;
        margin-bottom: 8px;
        font-size: 16px;
    }

    .form-control {
        width: 100%;
        padding: 12px;
        font-size: 16px;
        border: 1px solid #ddd;
        border-radius: 6px;
        background-color: #f5f5f5;
        transition: all 0.3s ease;
    }

    .form-control:focus {
        border-color: #007bff;
        outline: none;
        background-color: #e9f7fe;
    }

    .btn-submit {
        background-color: #007bff;
        color: white;
        border: none;
        padding: 14px 20px;
        font-size: 18px;
        width: 100%;
        cursor: pointer;
        border-radius: 6px;
        transition: all 0.3s ease;
    }

    .btn-submit:hover {
        background-color: #0056b3;
        transform: scale(1.05);
    }

    .error {
        color: #dc3545;
        font-size: 16px;
        font-weight: bold;
        text-align: center;
    }

    #mainhead {
        text-align: center;
        color: #28a745;
        font-size: 2em;
    }

    p {
        text-align: center;
        font-size: 18px;
    }

    select {
        width: 100%;
    }
</style>
