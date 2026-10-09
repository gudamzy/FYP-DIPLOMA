<?php
session_start();
require_once 'mysqli.php';  #dbc connection

# Get the table number from the form submission or URL
$table_no = $_POST['table_no'] ?? $_GET['table_no'] ?? null;

if ($table_no === null || $table_no === '') {
    echo "Table number is missing.";
    exit;
}

# Initialize the cart if it doesn't exist
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

# Add item to cart
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_to_cart'])) {
    $menu_id    = $_POST['menu_id'];
    $menu_name  = $_POST['menu_name'];
    $menu_price = $_POST['menu_price'];

    # Check if the item already exists in the cart
    $found = false;
    foreach ($_SESSION['cart'] as &$cart_item) {
        if ($cart_item['menu_id'] == $menu_id) {
            $cart_item['quantity']++;
            $found = true;
            break;
        }
    }
    unset($cart_item);

    # If not found, add a new item to the cart
    if (!$found) {
        $_SESSION['cart'][] = [
            'menu_id'   => $menu_id,
            'menu_name' => $menu_name,
            'price'     => $menu_price,
            'quantity'  => 1
        ];
    }

    # Redirect so refreshing the page does not add the item again
    header('Location: cust_order.php?table_no=' . urlencode($table_no) . '&added=' . urlencode($menu_name));
    exit;
}

# Cart summary
$cart_count = 0;
$cart_total = 0;
foreach ($_SESSION['cart'] as $item) {
    $cart_count += (int)$item['quantity'];
    $cart_total += (float)$item['price'] * (int)$item['quantity'];
}

# Fetch menu and group into categories
$categories = [
    'Set Meals'   => [],
    'Nasi Goreng' => [],
    'Minuman'     => [],
    'Lain-lain'   => [],
];
$result = mysqli_query($dbc, "SELECT * FROM rms_menu ORDER BY id");
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $name = strtoupper(trim($row['menu']));
        $desc = strtoupper(trim($row['description']));
        if (strpos($name, 'SET') === 0) {
            $categories['Set Meals'][] = $row;
        } elseif ($desc === 'NASI GORENG' || strpos($name, 'NASI GORENG') === 0) {
            $categories['Nasi Goreng'][] = $row;
        } elseif ($desc === 'MINUMAN') {
            $categories['Minuman'][] = $row;
        } else {
            $categories['Lain-lain'][] = $row;
        }
    }
}

$added = $_GET['added'] ?? null;
$t = htmlspecialchars($table_no);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Order</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: Arial, sans-serif;
            background: #f4f7fc;
            margin: 0;
            padding: 0 0 100px;
            color: #333;
        }
        .topbar {
            background: #003366;
            color: #fff;
            padding: 18px 20px;
        }
        .topbar-inner {
            max-width: 1100px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }
        .topbar h1 {
            margin: 0;
            font-size: 22px;
            letter-spacing: 1px;
        }
        .table-badge {
            background: #fff;
            color: #003366;
            font-weight: bold;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
        }
        .table-badge a {
            color: #ff5f6d;
            text-decoration: none;
            margin-left: 8px;
            font-weight: normal;
            font-size: 13px;
        }
        .page {
            max-width: 1100px;
            margin: 0 auto;
            padding: 28px 20px;
        }
        .toast {
            background: #e8f7ee;
            border: 1px solid #b7e4c7;
            color: #1e7b45;
            padding: 12px 16px;
            border-radius: 10px;
            margin-bottom: 24px;
            font-size: 14px;
        }
        .menu-section { margin-bottom: 36px; }
        .menu-section h2 {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 0 0 16px;
            font-size: 18px;
            color: #003366;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .menu-section h2::after {
            content: "";
            flex: 1;
            height: 2px;
            background: linear-gradient(90deg, #003366, transparent);
        }
        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
            gap: 16px;
        }
        .menu-card {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            background: #fff;
            border: 1px solid #e6e9ef;
            border-radius: 12px;
            padding: 18px 20px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.06);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .menu-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 18px rgba(0, 51, 102, 0.15);
        }
        .menu-card h3 {
            margin: 0 0 6px;
            font-size: 16px;
            color: #222;
        }
        .menu-card .desc {
            margin: 0 0 14px;
            font-size: 13px;
            line-height: 1.5;
            color: #777;
        }
        .card-foot {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 10px;
        }
        .price {
            font-weight: bold;
            font-size: 16px;
            color: #003366;
        }
        .add-btn {
            background: #003366;
            color: #fff;
            border: none;
            padding: 9px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.2s;
        }
        .add-btn:hover { background: #00509e; }
        .cart-bar {
            position: fixed;
            left: 0;
            right: 0;
            bottom: 0;
            background: #fff;
            border-top: 1px solid #e6e9ef;
            box-shadow: 0 -4px 14px rgba(0, 0, 0, 0.08);
            padding: 14px 20px;
        }
        .cart-bar-inner {
            max-width: 1100px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
        }
        .cart-info { font-size: 14px; color: #555; }
        .cart-info strong { color: #003366; font-size: 18px; }
        .cart-btn {
            background: linear-gradient(45deg, #ff5f6d, #ffc3a0);
            color: #fff;
            text-decoration: none;
            font-weight: bold;
            font-size: 16px;
            padding: 12px 28px;
            border-radius: 25px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.12);
            white-space: nowrap;
        }
        .cart-btn:hover { background: linear-gradient(45deg, #ff416c, #ff4b2b); }
        @media (max-width: 600px) {
            .topbar h1 { font-size: 18px; }
            .menu-grid { grid-template-columns: 1fr; }
            .cart-btn { padding: 11px 20px; font-size: 15px; }
        }
    </style>
</head>
<body>

    <div class="topbar">
        <div class="topbar-inner">
            <h1>PILIH MENU</h1>
            <div class="table-badge">
                Meja <?php echo $t; ?>
                <a href="cust_table.php">Tukar</a>
            </div>
        </div>
    </div>

    <div class="page">
        <?php if ($added !== null): ?>
            <div class="toast">✓ <strong><?php echo htmlspecialchars($added); ?></strong> ditambah ke cart.</div>
        <?php endif; ?>

        <?php foreach ($categories as $category => $items): ?>
            <?php if (count($items) === 0) continue; ?>
            <section class="menu-section">
                <h2><?php echo htmlspecialchars($category); ?></h2>
                <div class="menu-grid">
                    <?php foreach ($items as $row):
                        $desc = trim($row['description']);
                        $showDesc = $desc !== '' && strcasecmp($desc, $category) !== 0;
                    ?>
                        <div class="menu-card">
                            <div>
                                <h3><?php echo htmlspecialchars($row['menu']); ?></h3>
                                <?php if ($showDesc): ?>
                                    <p class="desc"><?php echo htmlspecialchars($desc); ?></p>
                                <?php endif; ?>
                            </div>
                            <form method="POST" action="cust_order.php" class="card-foot">
                                <input type="hidden" name="menu_id" value="<?php echo htmlspecialchars($row['id']); ?>">
                                <input type="hidden" name="menu_name" value="<?php echo htmlspecialchars($row['menu']); ?>">
                                <input type="hidden" name="menu_price" value="<?php echo htmlspecialchars($row['price']); ?>">
                                <input type="hidden" name="table_no" value="<?php echo $t; ?>">
                                <span class="price">RM <?php echo number_format((float)$row['price'], 2); ?></span>
                                <button type="submit" name="add_to_cart" class="add-btn">+ Tambah</button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endforeach; ?>
    </div>

    <div class="cart-bar">
        <div class="cart-bar-inner">
            <div class="cart-info">
                <?php echo $cart_count; ?> item &middot; <strong>RM <?php echo number_format($cart_total, 2); ?></strong>
            </div>
            <a class="cart-btn" href="cart.php?table_no=<?php echo urlencode($table_no); ?>">View Cart →</a>
        </div>
    </div>

</body>
</html>
