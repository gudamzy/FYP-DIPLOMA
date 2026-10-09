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

# Fetch menu and group into categories (each category has its own colour)
$categories = [
    'Set Meals'   => ['color' => 'blue',   'items' => []],
    'Nasi Goreng' => ['color' => 'green',  'items' => []],
    'Drinks'      => ['color' => 'orange', 'items' => []],
    'Others'      => ['color' => 'blue',   'items' => []],
];
$result = mysqli_query($dbc, "SELECT * FROM rms_menu ORDER BY id");
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $name = strtoupper(trim($row['menu']));
        $desc = strtoupper(trim($row['description']));
        if (strpos($name, 'SET') === 0) {
            $categories['Set Meals']['items'][] = $row;
        } elseif ($desc === 'NASI GORENG' || strpos($name, 'NASI GORENG') === 0) {
            $categories['Nasi Goreng']['items'][] = $row;
        } elseif ($desc === 'MINUMAN') {
            $categories['Drinks']['items'][] = $row;
        } else {
            $categories['Others']['items'][] = $row;
        }
    }
}

$added = $_GET['added'] ?? null;
$t = htmlspecialchars($table_no);
$cart_url = 'cart.php?table_no=' . urlencode($table_no);
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
            background: #fff;
            margin: 0;
            padding: 0 0 100px;
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
            max-width: 1200px;
            margin: 0 auto;
            padding: 28px 20px;
        }
        .toast {
            background: #e8f5e9;
            border: 1px solid #c8e6c9;
            color: #2e7d32;
            padding: 14px 18px;
            border-radius: 10px;
            margin-bottom: 24px;
        }
        .menu-section { margin-bottom: 34px; }
        .menu-section h2 {
            margin: 0 0 16px;
            font-size: 1.6rem;
            font-weight: bold;
        }
        .blue h2   { color: #007bff; }
        .green h2  { color: #2e7d32; }
        .orange h2 { color: #ff6f00; }

        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
        }
        .menu-card {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .menu-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 18px rgba(0, 0, 0, 0.12);
        }
        .blue .menu-card   { background: #e3f2fd; }
        .green .menu-card  { background: #f1f8e9; }
        .orange .menu-card { background: #fff8e1; }
        .menu-card h3 {
            margin: 0 0 6px;
            font-size: 1.1rem;
            color: #333;
        }
        .menu-card .desc {
            margin: 0 0 14px;
            font-size: 0.92rem;
            line-height: 1.5;
            color: #555;
        }
        .card-foot {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 10px;
        }
        .price { font-weight: bold; font-size: 1.3rem; }
        .blue .price   { color: #007bff; }
        .green .price  { color: #28a745; }
        .orange .price { color: #ff9800; }
        .add-btn {
            background: #2c3e50;
            color: #fff;
            border: none;
            padding: 10px 18px;
            border-radius: 6px;
            font-size: 0.95rem;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.2s;
        }
        .add-btn:hover { background: #34495e; }

        .cart-bar {
            position: fixed;
            left: 0;
            right: 0;
            bottom: 0;
            background: #2c3e50;
            color: #fff;
            padding: 14px 20px;
            box-shadow: 0 -4px 12px rgba(0, 0, 0, 0.15);
        }
        .cart-bar-inner {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
        }
        .cart-info { font-size: 1rem; color: #dfe6ec; }
        .cart-info strong { color: #fff; font-size: 1.3rem; }
        .cart-btn {
            background: #28a745;
            color: #fff;
            text-decoration: none;
            font-weight: bold;
            font-size: 1.05rem;
            padding: 12px 26px;
            border-radius: 8px;
            white-space: nowrap;
        }
        .cart-btn:hover { background: #218838; }
        @media (max-width: 768px) {
            .banner h1 { font-size: 1.8rem; }
            .menu-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

    <nav>
        <div class="logo">Place Your Order</div>
        <div class="nav-right">
            <a href="cust_menu.php">MENU</a>
            <a href="<?php echo $cart_url; ?>">CART</a>
            <span class="table-badge">Table <?php echo $t; ?></span>
        </div>
    </nav>

    <div class="banner">
        <h1>Choose Your Meal</h1>
        <p>Add items to your cart, then check out when you are ready.</p>
    </div>

    <div class="page">
        <?php if ($added !== null): ?>
            <div class="toast">✓ <strong><?php echo htmlspecialchars($added); ?></strong> was added to your cart.</div>
        <?php endif; ?>

        <?php foreach ($categories as $category => $cat): ?>
            <?php if (count($cat['items']) === 0) continue; ?>
            <section class="menu-section <?php echo $cat['color']; ?>">
                <h2><?php echo htmlspecialchars($category); ?></h2>
                <div class="menu-grid">
                    <?php foreach ($cat['items'] as $row):
                        $desc = trim($row['description']);
                        $showDesc = $desc !== '' && !in_array(strtoupper($desc), ['NASI GORENG', 'MINUMAN'], true);
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
                                <button type="submit" name="add_to_cart" class="add-btn">+ Add</button>
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
                <?php echo $cart_count; ?> item<?php echo $cart_count == 1 ? '' : 's'; ?> &middot; <strong>RM <?php echo number_format($cart_total, 2); ?></strong>
            </div>
            <a class="cart-btn" href="<?php echo $cart_url; ?>">View Cart →</a>
        </div>
    </div>

</body>
</html>
