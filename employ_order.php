<?php
require_once __DIR__ . '/includes/security.php';
require_staff(); # login check before anything is printed
$page_title = 'Current Order';
include('./includes/header_employ.html');
require_once('mysqli.php'); # dbc connection
global $dbc;

# Page header.
echo "<h1 class='page-header'>Order List</h1>\n";

# Query to fetch the order details
$query = "SELECT tables_no, orders, time, status FROM rms_order";
$result = mysqli_query($dbc, $query);

if (mysqli_num_rows($result) > 0) {

    # Table 
    echo '<table class="order-table">
            <thead>
                <tr>
                    <th>Table No</th>
                    <th>Orders</th>
                    <th>Time</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>';

    # Fetch and print all the records.
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        echo '<tr>
                <td>' . e($row['tables_no']) . '</td>
                <td>' . e($row['orders']) . '</td>
                <td>' . e($row['time']) . '</td>
                <td>' . e($row['status']) . '</td>
              </tr>';
    }

    echo '</tbody>
        </table>';
    mysqli_free_result($result);
} else {
    echo '<div class="alert error">There are currently no orders.</div>';
}

mysqli_close($dbc); # Close the database connection.
include('./includes/footer.html'); #footer
?>

<!-- Add the CSS directly to the page for styling -->
<style>
    body {
        font-family: 'Arial', sans-serif;
        background-color: #f9f9f9;
        color: #333;
        margin: 0;
        padding: 0;
    }

    .page-header {
        text-align: center;
        color: #007bff;
        font-size: 2.5em;
        margin-top: 20px;
        margin-bottom: 30px;
    }

    .order-table {
        width: 80%;
        margin: 0 auto;
        border-collapse: collapse;
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .order-table th, .order-table td {
        padding: 12px 15px;
        text-align: left;
        font-size: 16px;
    }

    .order-table th {
        background-color: #007bff;
        color: white;
        font-weight: bold;
    }

    .order-table tr:nth-child(even) {
        background-color: #f2f2f2;
    }

    .order-table tr:hover {
        background-color: #eaeaea;
        cursor: pointer;
    }

    .order-table td {
        border-top: 1px solid #ddd;
        border-bottom: 1px solid #ddd;
    }

    .alert {
        padding: 15px;
        border-radius: 8px;
        text-align: center;
        font-size: 16px;
        width: 80%;
        margin: 0 auto;
    }

    .alert.error {
        background-color: #f8d7da;
        color: #721c24;
    }
</style>
