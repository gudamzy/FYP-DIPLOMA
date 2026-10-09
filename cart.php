<?php
session_start();
require_once 'mysqli.php';  # Database connection

# Get the table number from the session
$table_no = $_SESSION['table_no'] ?? null;

if ($table_no === null) {
    echo "Table number is missing.";
    exit;
}

# Quantity update code
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    # Increase quantity
    if (isset($_POST['increase'])) {
        $menu_id = $_POST['menu_id'];
        foreach ($_SESSION['cart'] as &$cart_item) {
            if ($cart_item['menu_id'] == $menu_id) {
                $cart_item['quantity']++;
            }
        }
    }
    # Decrease quantity
    if (isset($_POST['decrease'])) {
        $menu_id = $_POST['menu_id'];
        foreach ($_SESSION['cart'] as &$cart_item) {
            if ($cart_item['menu_id'] == $menu_id && $cart_item['quantity'] > 1) {
                $cart_item['quantity']--;
            }
        }
    }
    # Remove item from cart
    if (isset($_POST['remove'])) {
        $menu_id = $_POST['menu_id'];
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7fc;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 80%;
            max-width: 1000px;
            margin: 20px auto;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h2, h3 {
            text-align: center;
            color: #4a90e2;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
        }

        th {
            background-color: #4a90e2;
            color: white;
        }

        td {
            background-color: #fff;
        }

        button {
            padding: 8px 16px;
            background-color: #4a90e2;
            border: none;
            color: white;
            cursor: pointer;
            border-radius: 4px;
            transition: background-color 0.3s ease;
            margin-right: 5px;
        }

        button:hover {
            background-color: #357abd;
        }

        .empty-cart {
            text-align: center;
            margin-top: 20px;
        }

        .empty-cart a {
            color: #4a90e2;
            text-decoration: none;
            font-size: 18px;
        }

        .empty-cart a:hover {
            text-decoration: underline;
        }

        .confirm-btn {
            width: 100%;
            padding: 12px;
            background-color: #4a90e2;
            border: none;
            color: white;
            font-size: 18px;
            cursor: pointer;
            border-radius: 4px;
            margin-top: 20px;
            transition: background-color 0.3s ease;
        }

        .confirm-btn:hover {
            background-color: #357abd;
        }
    </style>
</head>
<body>

    <div class="container">
        <h2>Table Number: <?php echo $table_no; ?></h2>
        <h3>Your Cart</h3>

        <?php if (empty($_SESSION['cart'])): ?>
            <div class="empty-cart">
                <p>Your cart is empty. <a href="cust_table.php">Go to Menu</a></p>
            </div>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Menu Item</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($_SESSION['cart'] as $cart_item): ?>
                        <tr>
                            <form method="POST">
                                <td><?php echo $cart_item['menu_name']; ?></td>
                                <td><?php echo number_format($cart_item['price'], 2); ?></td>
                                <td><?php echo $cart_item['quantity']; ?></td>
                                <td>
                                    <input type="hidden" name="menu_id" value="<?php echo $cart_item['menu_id']; ?>">
                                    <button type="submit" name="increase">+</button>
                                    <button type="submit" name="decrease">-</button>
                                    <button type="submit" name="remove">Remove</button>
                                </td>
                            </form>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <form method="POST" action="gateway.php">
                <input type="hidden" name="table_no" value="<?php echo $table_no; ?>">
                <input type="hidden" name="total" value="<?php
                $total = 0;
                foreach ($_SESSION['cart'] as $cart_item) {
                    $total += $cart_item['price'] * $cart_item['quantity'];
                }
                echo number_format($total, 2);
                ?>">
                <button class="confirm-btn" type="submit">Confirm Order and Proceed to Payment</button>
            </form>
        <?php endif; ?>
    </div>

</body>
</html>
