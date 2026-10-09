<?php 

$page_title = 'menu_update';
include ('./includes/header_admin.html'); #header

# Check if the form has been submitted.
if (isset($_POST['submit'])) {

    require_once ('mysqli.php'); # dbc connection
    global $dbc;

    $errors = array(); #error array

    # Check for a menu  selection.
    if (empty($_POST['id'])) {
        $errors[] = 'You forgot to select the menu.';
    } else {
        $id = $_POST['id'];
    }

    # optional stuff.
    $menu = !empty($_POST['menu']) ? mysqli_real_escape_string($dbc, trim($_POST['menu'])) : null;
    $description = !empty($_POST['description']) ? mysqli_real_escape_string($dbc, trim($_POST['description'])) : null;
    $price = !empty($_POST['price']) ? mysqli_real_escape_string($dbc, trim($_POST['price'])) : null;

    if (empty($errors)) { # If no error

        # Start a list to store the pieces of the query
        $query_parts = [];
        $params = [];

        # Add to query_parts if menu is set.
        if ($menu !== null) {
            $query_parts[] = "menu=?";
            $params[] = $menu;
        }

        # Add to query_parts if description is set.
        if ($description !== null) {
            $query_parts[] = "description=?";
            $params[] = $description;
        }

        # Add to query_parts if price is set.
        if ($price !== null) {
            $query_parts[] = "price=?";
            $params[] = $price;
        }

        # If no fields were updated, show an error.
        if (empty($query_parts)) {
            echo '<h1 id="mainhead">Error!</h1>
                <p class="error">You need to enter at least one field to update.</p>';
            exit();
        }

        # Create the SQL query step by step using the given fields.
        $query = "UPDATE rms_menu SET " . implode(", ", $query_parts) . " WHERE id=?";
        $params[] = $id;

        # Set up and run the query using the correct number of parameters.
        $stmt = mysqli_prepare($dbc, $query);

        # update based on parameter.
        $types = str_repeat('s', count($params) - 1) . 'i'; # 's' for string, 'i' for integer (id)
        mysqli_stmt_bind_param($stmt, $types, ...$params);

        mysqli_stmt_execute($stmt);

        if (mysqli_stmt_affected_rows($stmt) > 0) { # If no error
            # Print a message.
            echo '<h1 id="mainhead">Menu Updated</h1>
            <p>The menu has been updated.</p><p><br /></p>';
        } else { # If error occurs
            echo '<h1 id="mainhead">System Error</h1>
            <p class="error">The menu could not be updated due to a system error. We apologize for any inconvenience.</p>'; 
            echo '<p>' . mysqli_error($dbc) . '<br /><br />Query: ' . $query . '</p>'; 
        }

        mysqli_stmt_close($stmt); # Close the statement.
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

require_once ('mysqli.php'); # dbc connection
global $dbc;

$query = "SELECT id, menu, description, price FROM rms_menu";
$result = @mysqli_query($dbc, $query);

if (mysqli_num_rows($result) > 0) {
    echo '<h2 class="form-header">Update Menu</h2>
    <form action="menu_update.php" method="post" class="form-container">
    <div class="form-group">
        <label for="menu">Menu:</label>
        <select name="id" id="menu" class="form-control">';

    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        echo '<option value="' . $row['id'] . '">' . $row['menu'] . '</option>';
    }

    echo '</select></div>
    <div class="form-group">
        <label for="menu-name">Menu Name:</label>
        <input type="text" name="menu" id="menu-name" class="form-control" value="" />
    </div>
    <div class="form-group">
        <label for="description">Description:</label>
        <input type="text" name="description" id="description" class="form-control" value="" />
    </div>
    <div class="form-group">
        <label for="price">Price:</label>
        <input type="text" name="price" id="price" class="form-control" value="" />
    </div>
    <div class="form-group">
        <input type="submit" name="submit" value="Update Menu" class="btn-submit" />
    </div>
    </form>';
} else {
    echo '<p>No menu available to update.</p>';
}

mysqli_close($dbc); # Close the database connection.

include ('./includes/footer.html'); #footer
?>

<!-- Add the CSS for improved design -->
<style>
    body {
        font-family: 'Arial', sans-serif;
        background-color: #f4f4f4;
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
        font-weight: bold;
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
        font-weight: bold;
        color: #333;
        margin-bottom: 8px;
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
</style>
