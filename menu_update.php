<?php 
require_once __DIR__ . '/includes/security.php';
require_admin(); # only admins can change the menu

$page_title = 'menu_update';
include ('./includes/header_admin.html'); #header

require_once ('mysqli.php'); # dbc connection
global $dbc;

# Check if the form has been submitted.
if (isset($_POST['submit'])) {
    csrf_check();

    $errors = array(); #error array

    # Check for a menu selection.
    $id = (int)($_POST['id'] ?? 0);
    if ($id <= 0) {
        $errors[] = 'You forgot to select the menu.';
    }

    # optional stuff (prepared statements below keep these safe, no escaping needed).
    $menu        = trim($_POST['menu'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price_in    = trim($_POST['price'] ?? '');

    if ($price_in !== '' && (!is_numeric($price_in) || (float)$price_in < 0)) {
        $errors[] = 'Price must be a number, for example 9.50.';
    }

    if (empty($errors)) { # If no error

        # Start a list to store the pieces of the query
        $query_parts = [];
        $params = [];
        $types = '';

        if ($menu !== '') {
            $query_parts[] = "menu=?";
            $params[] = $menu;
            $types .= 's';
        }
        if ($description !== '') {
            $query_parts[] = "description=?";
            $params[] = $description;
            $types .= 's';
        }
        if ($price_in !== '') {
            $query_parts[] = "price=?";
            $params[] = round((float)$price_in, 2);
            $types .= 'd';
        }

        # If no fields were updated, show an error.
        if (empty($query_parts)) {
            echo '<h1 id="mainhead">Error!</h1>
                <p class="error">You need to enter at least one field to update.</p>';
        } else {
            # Column names are fixed above; only the values come from the user.
            $query = "UPDATE rms_menu SET " . implode(", ", $query_parts) . " WHERE id=?";
            $params[] = $id;
            $types .= 'i';

            $ok = false;
            try {
                $stmt = mysqli_prepare($dbc, $query);
                mysqli_stmt_bind_param($stmt, $types, ...$params);
                $ok = mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            } catch (mysqli_sql_exception $ex) {
                $ok = false;
            }

            if ($ok) {
                echo '<h1 id="mainhead">Menu Updated</h1>
                <p>The menu has been updated.</p><p><br /></p>';
            } else {
                echo '<h1 id="mainhead">System Error</h1>
                <p class="error">The menu could not be updated due to a system error. We apologize for any inconvenience.</p>';
            }

            mysqli_close($dbc);
            include ('./includes/footer.html');
            exit();
        }

    } else { # Report the errors.
        echo '<h1 id="mainhead">Error!</h1>
        <p class="error">The following error(s) occurred:<br />';
        foreach ($errors as $msg) { # Print each error.
            echo ' - ' . e($msg) . "<br />\n";
        }
        echo '</p><p>Please try again.</p><p><br /></p>';
    } 
}

$query = "SELECT id, menu, description, price FROM rms_menu";
$result = @mysqli_query($dbc, $query);

if ($result && mysqli_num_rows($result) > 0) {
    echo '<h2 class="form-header">Update Menu</h2>
    <form action="menu_update.php" method="post" class="form-container">
    ' . csrf_field() . '
    <div class="form-group">
        <label for="menu">Menu:</label>
        <select name="id" id="menu" class="form-control">';

    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        echo '<option value="' . (int)$row['id'] . '">' . e($row['menu']) . '</option>';
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
