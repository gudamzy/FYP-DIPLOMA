<?php
require_once __DIR__ . '/includes/security.php'; # safe session settings
require_once 'mysqli.php';  # Database connection

# Get the table number (from the link, or from the session saved earlier)
if (isset($_GET['table_no']) && $_GET['table_no'] !== '') {
    $_SESSION['table_no'] = $_GET['table_no'];
}
$table_no = $_SESSION['table_no'] ?? null;

# Only real table numbers (1-12) are accepted
$table_no = filter_var($table_no, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 12]]);
if ($table_no === false) {
    $table_no = null;
}

if ($table_no === null) {
    echo "Table number is missing.";
    exit;
}

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

# Quantity update code
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $menu_id = (int)($_POST['menu_id'] ?? 0);

    # Increase quantity
    if (isset($_POST['increase'])) {
        foreach ($_SESSION['cart'] as &$cart_item) {
            if ($cart_item['menu_id'] == $menu_id && $cart_item['quantity'] < 50) {
                $cart_item['quantity']++;
            }
        }
        unset($cart_item);
    }
    # Decrease quantity
    if (isset($_POST['decrease'])) {
        foreach ($_SESSION['cart'] as &$cart_item) {
            if ($cart_item['menu_id'] == $menu_id && $cart_item['quantity'] > 1) {
                $cart_item['quantity']--;
            }
        }
        unset($cart_item);
    }
    # Remove item from cart
    if (isset($_POST['remove'])) {
        foreach ($_SESSION['cart'] as $key => $cart_item) {
            if ($cart_item['menu_id'] == $menu_id) {
                unset($_SESSION['cart'][$key]);
            }
        }
    }

    # Re-index the cart to maintain proper order after removal
    $_SESSION['cart'] = array_values($_SESSION['cart']);

    header("Location: cart.php"); # Refresh the page after finishing
    exit();
}

# Totals
$total = 0;
$count = 0;
foreach ($_SESSION['cart'] as $cart_item) {
    $total += $cart_item['price'] * $cart_item['quantity'];
    $count += (int)$cart_item['quantity'];
}

$t        = htmlspecialchars($table_no);
$menu_url = 'cust_order.php?table_no=' . urlencode($table_no);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: Arial, sans-serif;
            background: #fff;
            margin: 0;
            padding: 0;
            color: #333;
        }
        nav {
            background-color: #2c3e50;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }
        .logo {
            color: #fff;
            font-size: 1.6rem;
            font-weight: bold;
        }
        .nav-right {
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .nav-right a {
            color: #fff;
            text-decoration: none;
            padding: 10px 18px;
            border-radius: 5px;
            font-size: 1.05rem;
            transition: background-color 0.3s;
        }
        .nav-right a:hover { background-color: #34495e; }
        .table-badge {
            background: #ecf0f1;
            color: #2c3e50;
            font-weight: bold;
            padding: 8px 16px;
            border-radius: 20px;
            margin-left: 8px;
        }

        .banner {
            position: relative;
            background: #2c3e50 url('image/resss.jpeg') center / cover no-repeat;
            padding: 40px 20px;
            text-align: center;
            color: #fff;
        }
        .banner::before {
            content: "";
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, 0.4);
        }
        .banner > * { position: relative; }
        .banner h1 { margin: 0 0 8px; font-size: 2.4rem; }
        .banner p  { margin: 0; font-size: 1.15rem; color: #f0f0f0; }

        .page {
            max-width: 1000px;
            margin: 0 auto;
            padding: 28px 20px 50px;
        }
        .layout {
            display: grid;
            grid-template-columns: 1fr 320px;
            gap: 24px;
            align-items: start;
        }
        .cart-list {
            background: #e3f2fd;
            border: 1px solid #ddd;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .cart-list h2, .summary h2 {
            margin: 0;
            padding: 16px 20px;
            font-size: 1.3rem;
        }
        .cart-list h2 { color: #007bff; border-bottom: 1px solid #cfe3f7; }
        .cart-row {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 16px 20px;
            background: #fff;
            border-bottom: 1px solid #e8eef5;
        }
        .cart-row:last-child { border-bottom: none; }
        .item-info { flex: 1; min-width: 0; }
        .item-name {
            font-weight: bold;
            font-size: 1.05rem;
            margin: 0 0 4px;
        }
        .item-unit { font-size: 0.85rem; color: #777; }
        .qty {
            display: flex;
            align-items: center;
            border: 1px solid #ccd6e0;
            border-radius: 6px;
            overflow: hidden;
        }
        .qty button {
            width: 34px;
            height: 34px;
            border: none;
            background: #ecf0f1;
            color: #2c3e50;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
        }
        .qty button:hover { background: #bdc3c7; }
        .qty button:disabled {
            color: #b0bac4;
            background: #f4f6f7;
            cursor: default;
        }
        .qty span {
            min-width: 34px;
            text-align: center;
            font-weight: bold;
        }
        .subtotal {
            width: 90px;
            text-align: right;
            font-weight: bold;
            color: #007bff;
            white-space: nowrap;
        }
        .remove-btn {
            border: none;
            background: #fdecea;
            color: #dc3545;
            font-size: 0.85rem;
            font-weight: bold;
            cursor: pointer;
            padding: 8px 12px;
            border-radius: 6px;
        }
        .remove-btn:hover { background: #f8d7da; }

        .summary {
            background: #f1f8e9;
            border: 1px solid #ddd;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 20px;
        }
        .summary h2 { color: #2e7d32; border-bottom: 1px solid #dcedc8; }
        .summary-body { padding: 18px 20px 20px; }
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            color: #555;
        }
        .summary-total {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            border-top: 1px solid #dcedc8;
            padding-top: 14px;
            margin-top: 4px;
        }
        .summary-total span { font-weight: bold; font-size: 1.1rem; }
        .summary-total strong { font-size: 1.8rem; color: #28a745; }
        .pay-btn {
            width: 100%;
            margin-top: 18px;
            padding: 14px;
            border: none;
            border-radius: 8px;
            background: #28a745;
            color: #fff;
            font-size: 1.05rem;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.2s;
        }
        .pay-btn:hover { background: #218838; }
        .more-link {
            display: block;
            text-align: center;
            margin-top: 12px;
            color: #2c3e50;
            text-decoration: none;
            font-weight: 600;
        }
        .more-link:hover { text-decoration: underline; }
        .secure-note {
            text-align: center;
            font-size: 0.8rem;
            color: #888;
            margin-top: 10px;
        }

        .empty {
            text-align: center;
            background: #fff8e1;
            border: 1px solid #ddd;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 50px 20px;
        }
        .empty h2 { color: #ff6f00; margin: 0 0 10px; }
        .empty p { color: #555; margin: 0 0 22px; }
        .empty a {
            display: inline-block;
            background: #2c3e50;
            color: #fff;
            text-decoration: none;
            padding: 12px 28px;
            border-radius: 8px;
            font-weight: bold;
        }

        @media (max-width: 800px) {
            .layout { grid-template-columns: 1fr; }
            .summary { position: static; }
            .banner h1 { font-size: 1.8rem; }
        }
        @media (max-width: 520px) {
            .cart-row { flex-wrap: wrap; }
            .item-info { flex-basis: 100%; }
            .subtotal { width: auto; margin-left: auto; }
        }
    </style>
</head>
<body>

    <nav>
        <div class="logo">Your Cart</div>
        <div class="nav-right">
            <a href="cust_menu.php">MENU</a>
            <a href="<?php echo $menu_url; ?>">ORDER</a>
            <span class="table-badge">Table <?php echo $t; ?></span>
        </div>
    </nav>

    <div class="banner">
        <h1>Your Cart</h1>
        <p>Check your order before you pay.</p>
    </div>

    <div class="page">
        <?php if (empty($_SESSION['cart'])): ?>
            <div class="empty">
                <h2>Your cart is empty</h2>
                <p>Add some meals or drinks to get started.</p>
                <a href="<?php echo $menu_url; ?>">Go to Menu</a>
            </div>
        <?php else: ?>
            <div class="layout">
                <div class="cart-list">
                    <h2>Order Items</h2>
                    <?php foreach ($_SESSION['cart'] as $cart_item):
                        $qty = (int)$cart_item['quantity'];
                    ?>
                        <form method="POST" class="cart-row">
                            <input type="hidden" name="menu_id" value="<?php echo htmlspecialchars($cart_item['menu_id']); ?>">
                            <div class="item-info">
                                <p class="item-name"><?php echo htmlspecialchars($cart_item['menu_name']); ?></p>
                                <div class="item-unit">RM <?php echo number_format($cart_item['price'], 2); ?> each</div>
                            </div>
                            <div class="qty">
                                <button type="submit" name="decrease" aria-label="Decrease" <?php echo $qty <= 1 ? 'disabled' : ''; ?>>−</button>
                                <span><?php echo $qty; ?></span>
                                <button type="submit" name="increase" aria-label="Increase">+</button>
                            </div>
                            <div class="subtotal">RM <?php echo number_format($cart_item['price'] * $qty, 2); ?></div>
                            <button type="submit" name="remove" class="remove-btn">Remove</button>
                        </form>
                    <?php endforeach; ?>
                </div>

                <div class="summary">
                    <h2>Order Summary</h2>
                    <div class="summary-body">
                        <div class="summary-row">
                            <span>Table</span>
                            <span><?php echo $t; ?></span>
                        </div>
                        <div class="summary-row">
                            <span>Items</span>
                            <span><?php echo $count; ?></span>
                        </div>
                        <div class="summary-total">
                            <span>Total</span>
                            <strong>RM <?php echo number_format($total, 2); ?></strong>
                        </div>

                        <form method="POST" action="gateway.php">
                            <input type="hidden" name="table_no" value="<?php echo $t; ?>">
                            <input type="hidden" name="total" value="<?php echo number_format($total, 2, '.', ''); ?>">
                            <button class="pay-btn" type="submit">Confirm Order &amp; Pay</button>
                        </form>
                        <a class="more-link" href="<?php echo $menu_url; ?>">+ Add more items</a>
                        <div class="secure-note">Payments are processed securely by Stripe</div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

</body>
</html>
