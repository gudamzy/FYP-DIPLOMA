<?php 

$page_title = 'MENU ADD';
include ('./includes/header_admin.html'); #header

# Check if the form has been submitted.
if (isset($_POST['submit'])) {

    require_once ('mysqli.php'); # dbc connection.
    global $dbc;

    $errors = array(); 

    # Check for menu and price only (description can be empty).
    if (empty($_POST['menu']) || empty($_POST['price'])) {
        $errors[] = 'You forgot to enter menu and price.';
    }
    
    # description can be empty
    $description = !empty($_POST['description']) ? $_POST['description'] : '';

    if (empty($errors)) { #if no error

        # Check for previous registration.
        $query = "SELECT menu, description, price FROM rms_menu WHERE menu='{$_POST['menu']}' AND description='{$description}' AND price='{$_POST['price']}'";
        $result = @mysqli_query ($dbc,$query); # Run the dbc,query.
        if (mysqli_num_rows($result) == 0) {

            $query = "INSERT INTO rms_menu (menu, description, price) VALUES ('{$_POST['menu']}', '{$description}', '{$_POST['price']}')";        
            $result = @mysqli_query ($dbc,$query); # Run the dbc,query.

            if (mysqli_affected_rows($dbc) > 0) { 
                # Print message.
                echo '<h1 id="mainhead">Thank you!</h1>
                    <p>'. $_POST['menu']. ($description ? ', ' . $description : '') . ', ' . $_POST['price'] . ' has been added. </p><p><br /></p>';    

                include ('./includes/footer.html'); 
                exit();
            } else {
                echo '<h1 id="mainhead">System Error</h1>
                <p class="error">You could not be registered due to a system error. We apologize for any inconvenience.</p>'; // Public message.
                echo '<p>' . mysqli_error($dbc)  . '<br /><br />Query: ' . $query . '</p>'; // Debugging message.
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
            echo " - $msg<br />\n";
        }
        echo '</p><p>Please try again.</p><p><br /></p>';

    }

    mysqli_close($dbc); # Close the database connection.
} 
?>

<h2 class="form-header">Add New Menu</h2>
<form action="menu_add.php" method="post" class="form-container">
    <div class="form-group">
        <label for="menu">New Menu:</label>
        <input type="text" id="menu" name="menu" size="15" maxlength="15" value="<?php if (isset($_POST['menu'])) echo $_POST['menu']; ?>" class="form-control" />
    </div>
    
    <div class="form-group">
        <label for="description">Description:</label>
        <input type="text" id="description" name="description" value="<?php if (isset($_POST['description'])) echo $_POST['description']; ?>" class="form-control" />
    </div>
    
    <div class="form-group">
        <label for="price">Price:</label>
        <input type="number" id="price" name="price" value="<?php if (isset($_POST['price'])) echo $_POST['price']; ?>" class="form-control" step="0.01" />
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
