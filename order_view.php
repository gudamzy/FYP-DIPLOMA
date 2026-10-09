<?php
$page_title = 'Order History';
include('./includes/header_admin.html'); #header
require_once('mysqli.php'); # dbc connection
global $dbc;

# Page header.
echo "<h1 class='page-title'>Order History</h1>\n";

# Query to fetch the order history.
$query = "SELECT tables_no, orders, time, total_sum FROM rms_order ORDER BY time DESC";
$result = mysqli_query($dbc, $query);

if (mysqli_num_rows($result) > 0) {

    # Table
    echo '<div class="table-container">
            <table class="order-table" cellspacing="0" cellpadding="10">
            <thead>
                <tr>
                    <th>Table No</th>
                    <th>Orders</th>
                    <th>Time</th>
                    <th>Total Sum (RM)</th>
                </tr>
            </thead>
            <tbody>';

    # Fetch and print all the records.
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        echo '<tr>
                <td>' . htmlspecialchars($row['tables_no']) . '</td>
                <td>' . htmlspecialchars($row['orders']) . '</td>
                <td>' . htmlspecialchars($row['time']) . '</td>
                <td>RM ' . number_format($row['total_sum'], 2) . '</td>
              </tr>';
    }
    echo '</tbody></table></div>';
    mysqli_free_result($result);
} else {
    echo '<p class="error-message">There are currently no orders.</p>';
}

mysqli_close($dbc); # Close the database connection.
include('./includes/footer.html'); # footer.
?>

<style>
    /* General page styling */
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f9;
        color: #333;
    }
    
    .page-title {
        text-align: center;
        color: #4CAF50;
        margin-top: 30px;
    }

    /* Table container styling */
    .table-container {
        width: 80%;
        margin: 0 auto;
        padding: 20px;
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    /* Table styling */
    .order-table {
        width: 100%;
        border-collapse: collapse;
    }

    .order-table th, .order-table td {
        padding: 12px 15px;
        text-align: left;
    }

    .order-table th {
        background-color: #4CAF50;
        color: #fff;
        font-size: 1.1em;
    }

    .order-table tr:nth-child(even) {
        background-color: #f2f2f2;
    }

    .order-table tr:hover {
        background-color: #ddd;
    }

    .order-table td {
        font-size: 1em;
    }

    /* Error message styling */
    .error-message {
        color: #f44336;
        font-size: 1.2em;
        text-align: center;
        margin-top: 20px;
    }
</style>
