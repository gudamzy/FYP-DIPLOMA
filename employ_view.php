<?php
require_once __DIR__ . '/includes/security.php';
require_admin(); # login check before anything is printed
$page_title = 'EMPLOYEE LIST';
include ('./includes/header_admin.html');
require_once ('mysqli.php'); # dbc connection
global $dbc;

# Page header.
echo "<h1>EMPLOYEE LIST</h1>\n";

# Query to fetch the orders
$query = "SELECT username, email, age, gender, address, phone_number FROM rms_employee";
$result = mysqli_query($dbc, $query);

if (mysqli_num_rows($result) > 0) {

    # Table 
    echo '<table align="center" border="1" cellspacing="0" cellpadding="5" style="border-collapse: collapse; width: 80%;">
            <tr>
                <td align="left"><b>Username</b></td>
                <td align="left"><b>Email</b></td>
                <td align="left"><b>Password</b></td>
                <td align="left"><b>Age</b></td>
                <td align="left"><b>Gender</b></td>
                <td align="left"><b>Address</b></td>
                <td align="left"><b>Phone Number</b></td>
            </tr>';

    # Fetch and print all the records
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        echo '<tr>
                <td align="left">' . e($row['username']) . '</td>
                <td align="left">' . e($row['email']) . '</td>
                <td align="left">Hidden</td> <!-- Password is hidden -->
                <td align="left">' . e($row['age']) . '</td>
                <td align="left">' . e($row['gender']) . '</td>
                <td align="left">' . e($row['address']) . '</td>
                <td align="left">' . e($row['phone_number']) . '</td>
              </tr>';
    }
    echo '</table>';
    mysqli_free_result($result);
} else {
    echo '<p class="error">There are currently no employees.</p>';
}

mysqli_close($dbc); # Close the database connection.
include ('./includes/footer.html'); # footer.
?>
