<?php 
require_once __DIR__ . '/includes/security.php';
require_admin(); # only admins can add menu items

$page_title = 'MENU ADD';
include ('./includes/header_admin.html'); #header

# Check if the form has been submitted.
if (isset($_POST['submit'])) {
    csrf_check();

    require_once ('mysqli.php'); # dbc connection.
    global $dbc;

    $errors = array(); 

    $menu_in     = trim($_POST['menu'] ?? '');
    $price_in    = trim($_POST['price'] ?? '');
    # description can be empty
    $description = trim($_POST['description'] ?? '');

    # Check for menu and price only (description can be empty).
    if ($menu_in === '' || $price_in === '') {
        $errors[] = 'You forgot to enter menu and price.';
    } elseif (!is_numeric($price_in) || (float)$price_in < 0) {
        $errors[] = 'Price must be a number, for example 9.50.';
    }

    if (empty($errors)) { #if no error
        $price = round((float)$price_in, 2);

        # Check for previous registration.
        $stmt = mysqli_prepare($dbc, "SELECT id FROM rms_menu WHERE menu = ? AND description = ? AND price = ?");
        mysqli_stmt_bind_param($stmt, 'ssd', $menu_in, $description, $price);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        $exists = mysqli_stmt_num_rows($stmt) > 0;
        mysqli_stmt_close($stmt);

        if (!$exists) {
            $stmt = mysqli_prepare($dbc, "INSERT INTO rms_menu (menu, description, price) VALUES (?, ?, ?)");
            mysqli_stmt_bind_param($stmt, 'ssd', $menu_in, $description, $price);
            $ok = mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            if ($ok) { 
                # Print message.
                echo '<h1 id="mainhead">Thank you!</h1>
                    <p>'. e($menu_in) . ($description !== '' ? ', ' . e($description) : '') . ', RM' . e(number_format($price, 2)) . ' has been added. </p><p><br /></p>';
                include ('./includes/footer.html'); 
                exit();
            } else {
                echo '<h1 id="mainhead">System Error</h1>
                <p class="error">The menu could not be added due to a system error. Please try again.</p>';
                include ('./includes/footer.html'); 
                exit();
            }
        } else { # already exist
            echo '<h1 id="mainhead">Error!</h1>
            <p class="error">This menu already exists.</p>';
        }

    } else { # Report the errors.
        echo '<h1 id="mainhead">Error!</h1>
        <p class="error">The following error(s) occurred:<br />';
        foreach ($errors as $msg) { # Print each error.
            echo ' - ' . e($msg) . "<br />\n";
        }
        echo '</p><p>Please try again.</p><p><br /></p>';
    }

    mysqli_close($dbc); # Close the database connection.
} 
?>

<h2 class="form-header">Add New Menu</h2>
<form action="menu_add.php" method="post" class="form-container">
    <?php echo csrf_field(); ?>
    <div class="form-group">
        <label for="menu">New Menu:</label>
        <input type="text" id="menu" name="menu" size="15" maxlength="60" value="<?php if (isset($_POST['menu'])) echo e($_POST['menu']); ?>" class="form-control" />
    </div>
    
    <div class="form-group">
        <label for="description">Description:</label>
        <input type="text" id="description" name="description" value="<?php if (isset($_POST['description'])) echo e($_POST['description']); ?>" class="form-control" />
    </div>
    
    <div class="form-group">
        <label for="price">Price:</label>
        <input type="number" id="price" name="price" value="<?php if (isset($_POST['price'])) echo e($_POST['price']); ?>" class="form-control" step="0.01" />
    </div>
    
    <div class="form-group">
        <input type="submit" name="submit" value="INSERT NEW MENU" class="btn-submit" />
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
</style>
