<?php
session_start();
require_once 'mysqli.php';  #dbc connection

# Get the table number from the form submission or URL
$table_no = $_POST['table_no'] ?? $_GET['table_no'] ?? null;

if ($table_no === null) {
    echo "Table number is missing.";
    exit;
}

# Initialize the cart if it doesn't exist
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

# Add item to cart 
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_to_cart'])) {
    $menu_id = $_POST['menu_id'];
    $menu_name = $_POST['menu_name'];
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

    # If not found, add a new item to the cart
    if (!$found) {
        $_SESSION['cart'][] = [
            'menu_id' => $menu_id,
            'menu_name' => $menu_name,
            'price' => $menu_price,
            'quantity' => 1
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Order</title>
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

        h2 {
            color: #4a90e2;
            text-align: center;
            margin-bottom: 20px;
        }

        h3 {
            color: #333;
            text-align: center;
            margin-bottom: 20px;
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
        }

        button:hover {
            background-color: #357abd;
        }

        a {
            color: #4a90e2;
            text-decoration: none;
            font-size: 18px;
            display: inline-block;
            margin-top: 20px;
            text-align: center;
            width: 100%;
            padding: 10px 0;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <div class="container">
        <h2>Table Number: <?php echo $table_no; ?></h2>
        <h3>Menu</h3>

        <form method="POST" action="cust_order.php">
            <input type="hidden" name="table_no" value="<?php echo $table_no; ?>">

            <table>
                <thead>
                    <tr>
                        <th>Menu Item</th>
                        <th>Description</th>
                        <th>Price</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Fetch menu items from the database
                    $query = "SELECT * FROM rms_menu";
                    $result = mysqli_query($dbc, $query);
                    while ($row = mysqli_fetch_assoc($result)):
                    ?>
                    <tr>
                        <td><?php echo $row['menu']; ?></td>
                        <td><?php echo $row['description']; ?></td>
                        <td><?php echo number_format($row['price'], 2); ?></td>
                        <td>
                            <form method="POST" action="cust_order.php" style="display:inline;">
                                <input type="hidden" name="menu_id" value="<?php echo $row['id']; ?>">
                                <input type="hidden" name="menu_name" value="<?php echo $row['menu']; ?>">
                                <input type="hidden" name="menu_price" value="<?php echo $row['price']; ?>">
                                <input type="hidden" name="table_no" value="<?php echo $table_no; ?>">
                                <button type="submit" name="add_to_cart">Add to Cart</button>
                            </form>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </form>

        <a href="cart.php?table_no=<?php echo $table_no; ?>">View Cart</a>
    </div>

</body>
</html>
