<?php
require_once __DIR__ . '/includes/security.php';
require_staff(); # login check before anything is printed

require_once('mysqli.php'); #dbc connection
global $dbc;

# Handle the form FIRST, then reload the page so the list is up to date
# and refreshing the browser does not send the form again.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submitOrder'])) {
    csrf_check();

    $order_id = (int)($_POST['order_id'] ?? 0);
    $ready    = $_POST['ready'] ?? '';

    if ($order_id <= 0 || !in_array($ready, ['Y', 'N'], true)) {
        $_SESSION['order_ready_flash'] = 'invalid';
    } else {
        $status = $ready === 'Y' ? 'Completed' : 'Pending';
        try {
            $stmt = mysqli_prepare($dbc, "UPDATE rms_order SET status = ? WHERE id = ?");
            mysqli_stmt_bind_param($stmt, 'si', $status, $order_id);
            $ok = mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        } catch (mysqli_sql_exception $ex) {
            $ok = false;
        }
        $_SESSION['order_ready_flash'] = $ok ? 'ok' : 'error';
    }
    header('Location: order_ready.php');
    exit;
}
$flash = $_SESSION['order_ready_flash'] ?? '';
unset($_SESSION['order_ready_flash']);

$page_title = 'TO MARK ORDERS AS READY';
include('./includes/header_employ.html'); #header

echo "<h1>TO MARK ORDERS AS READY</h1>\n";

# Fetch and display pending orders
$query = "SELECT id, tables_no, orders, time, status FROM rms_order WHERE status = 'Pending'";
$result = mysqli_query($dbc, $query);

if (!$result) {
    echo '<p class="error" style="color: #dc3545;">Could not load the orders. Please try again later.</p>';
    include('./includes/footer.html');
    exit;
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
        echo '<tr><td>' . e($row['tables_no']) . '</td><td>' . e($row['orders']) . '</td><td>' . e($row['time']) . '</td><td>' . e($row['status']) . '</td></tr>';
        $orders[] = $row;
    }
    echo '</table>';
    mysqli_free_result($result);

    # Display the form for marking as ready
    echo "<h3>MARK ORDER AS READY</h3>\n";
    echo '<form action="" method="post" style="width: 60%; margin: 0 auto; padding: 20px; border: 1px solid #ddd; background-color: #f9f9f9;">';
    echo csrf_field();

    echo '<p><label for="order_id">Select order to mark as ready:</label>';
    echo '<select name="order_id" id="order_id" style="width: 100%; padding: 10px; margin-top: 5px;">';

    foreach ($orders as $order) {
        echo '<option value="' . e($order['id']) . '">' . 'Table ' . e($order['tables_no']) . ' - ' . e($order['orders']) . ' (' . e($order['time']) . ')</option>';
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

# Result message from the last submit (shown in the same place as before)
if ($flash === 'ok') {
    echo '<p>Order status updated successfully.</p>';
} elseif ($flash === 'invalid') {
    echo '<p class="error" style="color: #dc3545;">Invalid input. Please try again.</p>';
} elseif ($flash === 'error') {
    echo '<p class="error" style="color: #dc3545;">System error. Could not update the order status.</p>';
}

mysqli_close($dbc); # Close the database connection.
include('./includes/footer.html'); #footer
?>
