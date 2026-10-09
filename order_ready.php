<?php
$page_title = 'TO MARK ORDERS AS READY';
include('./includes/header_employ.html'); #header

require_once('mysqli.php'); #dbc connection
global $dbc;

echo "<h1>TO MARK ORDERS AS READY</h1>\n";

# Fetch and display pending orders
$query = "SELECT id, tables_no, orders, time, status FROM rms_order WHERE status = 'Pending'";
$result = mysqli_query($dbc, $query);

if (!$result) {
    die('<p class="error" style="color: #dc3545;">Error fetching orders: ' . mysqli_error($dbc) . '</p>');
}

if (mysqli_num_rows($result) > 0) {
    echo "<h3>ORDER PENDING</h3>\n";
    echo "<p>There are currently " . mysqli_num_rows($result) . " pending orders.</p>\n";

    # Table header.
    echo '<table align="center" cellspacing="0" cellpadding="5" border="1" style="border-collapse: collapse; width: 80%; text-align: left;">
    <tr><th>Table No</th><th>Orders</th><th>Time</th><th>Status</th></tr>';

    # Fetch and print all the records.
    $orders = [];
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        echo '<tr><td>' . $row['tables_no'] . '</td><td>' . $row['orders'] . '</td><td>' . $row['time'] . '</td><td>' . $row['status'] . '</td></tr>';
        $orders[] = $row;
    }
    echo '</table>';
    mysqli_free_result($result);

    # Display the form for marking as ready
    echo "<h3>MARK ORDER AS READY</h3>\n";
    echo '<form action="" method="post" style="width: 60%; margin: 0 auto; padding: 20px; border: 1px solid #ddd; background-color: #f9f9f9;">';

    echo '<p><label for="order_id">Select order to mark as ready:</label>';
    echo '<select name="order_id" id="order_id" style="width: 100%; padding: 10px; margin-top: 5px;">';

    foreach ($orders as $order) {
        echo '<option value="' . $order['id'] . '">' . 'Table ' . $order['tables_no'] . ' - ' . $order['orders'] . ' (' . $order['time'] . ')</option>';
    }

    echo '</select></p>';

    echo '<p><b>Ready:</b>
    <input type="radio" name="ready" value="Y" required/> YES
    <input type="radio" name="ready" value="N" required/> NO
    </p>';

    echo '<p><input type="submit" name="submitOrder" value="SUBMIT" style="padding: 10px 20px; background-color: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer;"/></p>';
    echo '</form>';
} else {
    echo '<p class="error" style="color: #dc3545; text-align: center;">There are currently NO PENDING ORDERS.</p>';
}

# form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submitOrder'])) {
    $order_id = $_POST['order_id'];
    $ready = $_POST['ready'];

    # inputs part
    if (empty($order_id) || !in_array($ready, ['Y', 'N'])) {
        echo '<p class="error" style="color: #dc3545;">Invalid input. Please try again.</p>';
    } else {
        $status = $ready === 'Y' ? 'Completed' : 'Pending';

        # Update the order status 
        $stmt = $dbc->prepare("UPDATE rms_order SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $order_id);
        if ($stmt->execute()) {
            echo '<p>Order status updated successfully.</p>';
        } else {
            echo '<p class="error" style="color: #dc3545;">System error. Could not update the order status.</p>';
            echo '<p>' . $dbc->error . '</p>';
        }
        $stmt->close();
    }
}

mysqli_close($dbc); # Close the database connection.
include('./includes/footer.html'); #footer
?>
