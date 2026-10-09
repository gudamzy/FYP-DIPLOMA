<?php
require 'vendor/autoload.php'; # Include Dompdf autoloader for download
use Dompdf\Dompdf;

session_start();

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    echo "No items found in the cart.";
    exit;
}

$table_no = $_SESSION['table_no'] ?? null;

if ($table_no === null) {
    echo "Table number is missing.";
    exit;
}

# Prepare the order details
$order_items = [];
$total = 0;
foreach ($_SESSION['cart'] as $cart_item) {
    $item_total = $cart_item['price'] * $cart_item['quantity'];
    $total += $item_total;
    $order_items[] = "{$cart_item['quantity']} x {$cart_item['menu_name']} (RM " . number_format($cart_item['price'], 2) . ")";
}
$order_details = implode(', ', $order_items);

# Check if the download button was clicked
if (isset($_GET['download'])) {
    # Generate the HTML for the PDF
    $html = '
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Order Receipt</title>
        <style>
            body { font-family: Arial, sans-serif; }
            .header { text-align: center; }
            .order-details, .total { margin: 20px 0; }
        </style>
    </head>
    <body>
        <div class="header">
            <h1>Order Receipt</h1>
            <p>Table No: ' . htmlspecialchars($table_no) . '</p>
        </div>
        <div class="order-details">
            <p><strong>Order Details:</strong><br>' . nl2br(htmlspecialchars($order_details)) . '</p>
        </div>
        <div class="total">
            <p><strong>Total Amount:</strong> RM ' . number_format($total, 2) . '</p>
        </div>
        <p>Thank you for dining with us!</p>
    </body>
    </html>';

    # Initialize Dompdf
    $dompdf = new Dompdf();
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();
    $dompdf->stream("Order_Receipt_Table_$table_no.pdf", ["Attachment" => true]);

    #insert database
    require_once('mysqli.php');
    global $dbc;
    $query = "INSERT INTO rms_order (tables_no, orders, price, total_sum, status) 
              VALUES ('{$table_no}', '{$order_details}', '{$total}', '{$total}', 'Pending')";
    $result = @mysqli_query($dbc, $query);

    if (!$result) {
        echo '<h1 id="mainhead">System Error</h1>
        <p class="error">Your order could not be processed due to a system error. Please try again later.</p>';
        echo '<p>' . mysqli_error($dbc) . '<br /><br />Query: ' . $query . '</p>'; 
        exit; #error 
    }
    exit; 
}

# Database insertion only if "Back to Menu" or "Download Receipt" is clicked
if (isset($_GET['back_to_menu']) || isset($_GET['download'])) {
    require_once('mysqli.php'); 
    global $dbc;
    #database
    $query = "INSERT INTO rms_order (tables_no, orders, price, total_sum, status) 
              VALUES ('{$table_no}', '{$order_details}', '{$total}', '{$total}', 'Pending')";
    $result = @mysqli_query($dbc, $query); 

    if (!$result) {
        echo '<h1 id="mainhead">System Error</h1>
        <p class="error">Your order could not be processed due to a system error. Please try again later.</p>';
        echo '<p>' . mysqli_error($dbc) . '<br /><br />Query: ' . $query . '</p>'; 
        exit; #error
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Receipt</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 100%;
            max-width: 800px;
            margin: 30px auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header h1 {
            color: #4CAF50;
            font-size: 32px;
            margin: 0;
        }

        .header p {
            font-size: 18px;
            color: #666;
        }

        .order-details {
            margin-bottom: 20px;
            padding: 15px;
            background-color: #f9f9f9;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        }

        .order-details p {
            font-size: 16px;
            color: #333;
            margin: 10px 0;
        }

        .total-amount {
            font-size: 20px;
            color: #333;
            font-weight: bold;
            text-align: center;
            margin-bottom: 20px;
        }

        .footer {
            text-align: center;
            font-size: 16px;
            color: #777;
            margin-top: 30px;
        }

        .footer a {
            color: #4CAF50;
            text-decoration: none;
        }

        .download-btn {
            display: block;
            width: 200px;
            margin: 20px auto;
            padding: 10px 15px;
            text-align: center;
            background-color: transparent; /* Removed color */
            color: inherit; /* Use the default text color */
            text-decoration: none;
            border: 2px solid #4CAF50; /* Add a border with the same color as the original button */
            border-radius: 5px;
            font-weight: bold;
            box-shadow: none; /* Removed shadow */
        }

            .download-btn:hover {
                background-color: transparent; /* Ensure no background color on hover */
                color: #4CAF50; /* Change text color on hover */
                border-color: #45a049; /* Change border color on hover */
            }

    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Thank You for Your Order!</h1>
            <p>Your order from Table No: <strong><?php echo htmlspecialchars($table_no); ?></strong></p>
        </div>

        <div class="order-details">
            <p><strong>Order Details:</strong><br />
            <?php echo nl2br(htmlspecialchars($order_details)); ?></p>
        </div>

        <div class="total-amount">
            <p>Total Amount: RM <?php echo number_format($total, 2); ?></p>
        </div>

        <div class="footer">
            <a href="?download=true" class="download-btn">Download Receipt</a>
            <a href="cust.php" class="download-btn">Back to Menu</a>
        </div>
    </div>
</body>
</html>
