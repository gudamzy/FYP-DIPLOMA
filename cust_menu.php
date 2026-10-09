<?php
$page_title = 'RESTAURANT MENU';
include('./includes/header_cust.html');
require_once('mysqli.php'); #dbc connection
global $dbc;

# Page header
echo "<h1 style='text-align: center; color: #003366; font-family: Arial, sans-serif; margin-bottom: 20px;'>RESTAURANT MENU</h1>\n";

#code fetch the orders
$query = "SELECT menu, description, price FROM rms_menu";
$result = mysqli_query($dbc, $query);

if (mysqli_num_rows($result) > 0) {

    #table
    echo '<div style="display: flex; justify-content: center; margin-top: 20px;">';
    echo '<table style="border-collapse: collapse; width: 80%; background-color: #f8f9fa; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);">';
    echo '<thead style="background-color: #003366; color: #fff;">';
    echo '<tr>
            <th style="padding: 15px; text-align: left;">Menu</th>
            <th style="padding: 15px; text-align: left;">Description</th>
            <th style="padding: 15px; text-align: left;">Price</th>
          </tr>';
    echo '</thead>';

    # Fetch and print all the records.
    echo '<tbody>';
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        echo '<tr style="border-bottom: 1px solid #ddd;">';
        echo '<td style="padding: 10px; font-family: Arial, sans-serif; color: #333;">' . $row['menu'] . '</td>';
        echo '<td style="padding: 10px; font-family: Arial, sans-serif; color: #666;">' . $row['description'] . '</td>';
        echo '<td style="padding: 10px; font-family: Arial, sans-serif; color: #003366; font-weight: bold;">' . $row['price'] . '</td>';
        echo '</tr>';
    }
    echo '</tbody>';
    echo '</table>';
    echo '</div>';

    mysqli_free_result($result);
} else {
    echo '<p style="text-align: center; color: red; font-family: Arial, sans-serif;">There are currently no orders.</p>';
}

mysqli_close($dbc); # Close the database connection.
include('./includes/footer.html'); #footer
?>
