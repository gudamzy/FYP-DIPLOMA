<?php
require_once __DIR__ . '/includes/security.php'; # safe session settings
require __DIR__ . '/vendor/autoload.php'; # Stripe + Dompdf (Composer)
require_once __DIR__ . '/config.php';      # Stripe secret key ($stripe_secret_key)
require_once __DIR__ . '/mysqli.php';      # Database connection ($dbc)
use Dompdf\Dompdf;
global $dbc;

/* ------------------------------------------------------------------
 * 1. Coming back from Stripe: confirm the payment, then save the order
 *    (once only), empty the cart and unlock the "Easy Payment" card.
 * ------------------------------------------------------------------ */
$stripe_session_id = $_GET['session_id'] ?? null;

if ($stripe_session_id) {
    if (!isset($_SESSION['saved_sessions'])) {
        $_SESSION['saved_sessions'] = [];
    }

    # Skip if this payment was already saved (e.g. the page was refreshed)
    if (!in_array($stripe_session_id, $_SESSION['saved_sessions'], true)) {
        $paid = false;
        try {
            \Stripe\Stripe::setApiKey($stripe_secret_key);
            $checkout = \Stripe\Checkout\Session::retrieve($stripe_session_id);
            $paid = ($checkout->payment_status === 'paid');
        } catch (Exception $e) {
            $_SESSION['order_error'] = 'We could not confirm your payment with Stripe. Please contact the restaurant.';
        }

        $table_no = $_SESSION['table_no'] ?? null;

        if ($paid && !empty($_SESSION['cart']) && $table_no !== null) {
            # Prepare the order details
            $order_items = [];
            $total = 0;
            foreach ($_SESSION['cart'] as $cart_item) {
                $qty   = (int)$cart_item['quantity'];
                $price = (float)$cart_item['price'];
                $total += $price * $qty;
                $order_items[] = "{$qty} x {$cart_item['menu_name']} (RM " . number_format($price, 2) . ")";
            }
            $order_details = implode(', ', $order_items);

            # Save the order
            $status = 'Pending';
            $stmt = $dbc->prepare("INSERT INTO rms_order (tables_no, orders, price, total_sum, status) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("isdds", $table_no, $order_details, $total, $total, $status);

            if ($stmt->execute()) {
                $_SESSION['last_order'] = [
                    'id'       => $stmt->insert_id,
                    'table_no' => $table_no,
                    'items'    => $order_items,
                    'details'  => $order_details,
                    'total'    => $total,
                    'time'     => date('d M Y, h:i A'),
                ];
                $_SESSION['cart']             = [];    # empty the cart for the next order
                $_SESSION['has_paid']         = true;  # unlocks the "Easy Payment" card on cust.php
                $_SESSION['saved_sessions'][] = $stripe_session_id;
            } else {
                $_SESSION['order_error'] = 'Your payment went through, but the order could not be saved. Please show this page to our staff.';
            }
            $stmt->close();
        } elseif (!$paid && !isset($_SESSION['order_error'])) {
            $_SESSION['order_error'] = 'Your payment has not been completed.';
        }
    }

    # Reload without the session id, so refreshing does not repeat anything
    header('Location: success.php');
    exit;
}

$order = $_SESSION['last_order'] ?? null;

/* ------------------------------------------------------------------
 * 2. Download the receipt as a PDF (no database changes here)
 * ------------------------------------------------------------------ */
if (isset($_GET['download']) && $order) {
    $rows = '';
    foreach ($order['items'] as $line) {
        $rows .= '<li>' . htmlspecialchars($line) . '</li>';
    }
    $html = '
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Order Receipt</title>
        <style>
            body { font-family: Arial, sans-serif; color: #333; }
            .header { text-align: center; background: #2c3e50; color: #fff; padding: 18px; }
            .header h1 { margin: 0 0 6px; }
            .box { margin: 24px 0; padding: 16px; background: #e3f2fd; border-radius: 8px; }
            .total { font-size: 20px; font-weight: bold; color: #28a745; }
            ul { margin: 8px 0 0 18px; padding: 0; }
        </style>
    </head>
    <body>
        <div class="header">
            <h1>Order Receipt</h1>
            <p>Order #' . (int)$order['id'] . ' &middot; Table ' . htmlspecialchars($order['table_no']) . '</p>
        </div>
        <div class="box">
            <p><strong>Date:</strong> ' . htmlspecialchars($order['time']) . '</p>
            <p><strong>Order Details:</strong></p>
            <ul>' . $rows . '</ul>
        </div>
        <p class="total">Total Paid: RM ' . number_format($order['total'], 2) . '</p>
        <p>Thank you for dining with us!</p>
    </body>
    </html>';

    $dompdf = new Dompdf();
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();
    $dompdf->stream('Order_Receipt_' . (int)$order['id'] . '.pdf', ['Attachment' => true]);
    exit;
}

/* ------------------------------------------------------------------
 * 3. Show the page
 * ------------------------------------------------------------------ */
$order_error = $_SESSION['order_error'] ?? null;
unset($_SESSION['order_error']);

# Live order status from the database (Pending / Completed)
$live_status = null;
if ($order) {
    $stmt = $dbc->prepare("SELECT status FROM rms_order WHERE id = ?");
    $stmt->bind_param("i", $order['id']);
    if ($stmt->execute()) {
        $stmt->bind_result($live_status);
        $stmt->fetch();
    }
    $stmt->close();
}
mysqli_close($dbc);

$page_title = 'ORDER RECEIPT';
include('./includes/header_cust.html'); #header
?>
<style>
    .receipt-banner {
        background: linear-gradient(135deg, #2c3e50, #34495e);
        color: #fff;
        text-align: center;
        padding: 46px 20px 90px;
        font-family: Arial, sans-serif;
    }
    .receipt-banner .tick {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        background: #28a745;
        color: #fff;
        font-size: 2rem;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 16px;
        box-shadow: 0 6px 16px rgba(40, 167, 69, 0.45);
    }
    .receipt-banner .tick.muted { background: #7f8c8d; box-shadow: none; }
    .receipt-banner h1 { margin: 0 0 8px; font-size: 2.2rem; }
    .receipt-banner p  { margin: 0; color: #dfe6ec; font-size: 1.05rem; }

    .receipt-wrap {
        max-width: 760px;
        margin: -60px auto 0;
        padding: 0 20px;
        font-family: Arial, sans-serif;
        color: #333;
        position: relative;
    }
    .receipt-card {
        background: #fff;
        border: 1px solid #e3e7ec;
        border-radius: 12px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }
    .receipt-meta {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        text-align: center;
        border-bottom: 1px solid #eef0f4;
    }
    .receipt-meta div { padding: 18px 10px; }
    .receipt-meta div + div { border-left: 1px solid #eef0f4; }
    .receipt-meta small {
        display: block;
        color: #888;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 6px;
    }
    .receipt-meta strong { font-size: 1.15rem; color: #2c3e50; }

    .status {
        display: inline-block;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.9rem;
        font-weight: bold;
    }
    .status.pending { background: #fff3e0; color: #e67e00; }
    .status.done    { background: #e8f5e9; color: #2e7d32; }

    .items { padding: 8px 24px; }
    .item {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        padding: 14px 0;
        border-bottom: 1px dashed #e0e4ea;
    }
    .item:last-child { border-bottom: none; }
    .item span:last-child { font-weight: bold; color: #007bff; white-space: nowrap; }

    .receipt-total {
        display: flex;
        justify-content: space-between;
        align-items: baseline;
        background: #f1f8e9;
        padding: 18px 24px;
    }
    .receipt-total span { font-weight: bold; font-size: 1.1rem; }
    .receipt-total strong { font-size: 1.8rem; color: #28a745; }

    .receipt-actions {
        display: flex;
        gap: 12px;
        justify-content: center;
        flex-wrap: wrap;
        margin: 26px 0 10px;
    }
    .btn-primary, .btn-dark, .btn-outline {
        display: inline-block;
        text-decoration: none;
        font-weight: bold;
        padding: 13px 26px;
        border-radius: 8px;
        border: 2px solid transparent;
        transition: background 0.3s, color 0.3s;
    }
    .btn-primary { background: #28a745; border-color: #28a745; color: #fff; }
    .btn-primary:hover { background: #218838; border-color: #218838; }
    .btn-dark { background: #2c3e50; border-color: #2c3e50; color: #fff; }
    .btn-dark:hover { background: #34495e; border-color: #34495e; }
    .btn-outline { border-color: #2c3e50; color: #2c3e50; }
    .btn-outline:hover { background: #2c3e50; color: #fff; }

    .notice {
        background: #fdecea;
        border: 1px solid #f5c6cb;
        color: #c0392b;
        border-radius: 10px;
        padding: 14px 18px;
        margin-bottom: 18px;
    }
    .empty-card {
        background: #fff8e1;
        border: 1px solid #ddd;
        border-radius: 12px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        text-align: center;
        padding: 40px 24px;
    }
    .empty-card h2 { color: #ff6f00; margin: 0 0 10px; }
    .empty-card p  { color: #555; margin: 0 0 22px; }

    @media (max-width: 600px) {
        .receipt-meta { grid-template-columns: 1fr; }
        .receipt-meta div + div { border-left: none; border-top: 1px solid #eef0f4; }
        .receipt-banner h1 { font-size: 1.7rem; }
    }
</style>

<?php if ($order): ?>
    <div class="receipt-banner">
        <div class="tick">✓</div>
        <h1>Thank You for Your Order!</h1>
        <p>Your payment was successful. Our kitchen is preparing your food.</p>
    </div>
<?php else: ?>
    <div class="receipt-banner">
        <div class="tick muted">🧾</div>
        <h1>Your Order</h1>
        <p>Your receipt will appear here after you pay.</p>
    </div>
<?php endif; ?>

<div class="receipt-wrap">
    <?php if ($order_error): ?>
        <div class="notice"><?php echo htmlspecialchars($order_error); ?></div>
    <?php endif; ?>

    <?php if ($order): ?>
        <div class="receipt-card">
            <div class="receipt-meta">
                <div><small>Order No.</small><strong>#<?php echo (int)$order['id']; ?></strong></div>
                <div><small>Table</small><strong><?php echo htmlspecialchars($order['table_no']); ?></strong></div>
                <div>
                    <small>Status</small>
                    <?php $done = strcasecmp((string)$live_status, 'Completed') === 0; ?>
                    <span class="status <?php echo $done ? 'done' : 'pending'; ?>">
                        <?php echo $done ? 'Ready' : 'Preparing'; ?>
                    </span>
                </div>
            </div>

            <div class="items">
                <?php foreach ($order['items'] as $line):
                    # "2 x SET A (RM 10.00)" -> name part and price part
                    $name  = preg_replace('/\s*\(RM [0-9.,]+\)$/', '', $line);
                    preg_match('/\(RM ([0-9.,]+)\)$/', $line, $m);
                ?>
                    <div class="item">
                        <span><?php echo htmlspecialchars($name); ?></span>
                        <span><?php echo isset($m[1]) ? 'RM ' . htmlspecialchars($m[1]) . ' each' : ''; ?></span>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="receipt-total">
                <span>Total Paid</span>
                <strong>RM <?php echo number_format($order['total'], 2); ?></strong>
            </div>
        </div>

        <div class="receipt-actions">
            <a href="success.php?download=true" class="btn-primary">Download Receipt</a>
            <a href="success.php" class="btn-outline">Refresh Status</a>
            <a href="cust.php" class="btn-dark">Back to Home</a>
        </div>
    <?php else: ?>
        <div class="empty-card">
            <h2>No order yet</h2>
            <p>Place an order and pay to see your receipt and order status here.</p>
            <a href="cust_table.php" class="btn-dark">Start an Order</a>
        </div>
    <?php endif; ?>
</div>

<?php
include('./includes/footer.html'); # footer
?>
