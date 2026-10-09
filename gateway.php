<?php
session_start();
require __DIR__ . "/vendor/autoload.php"; #taking from vendor autoload stripe

# Stripe secret key
require_once 'config.php';

# Set API key for Stripe
\Stripe\Stripe::setApiKey($stripe_secret_key);

# Get the table number and total from POST request
$table_no = $_POST['table_no'] ?? null;
$total = $_POST['total'] ?? null;

if ($table_no === null || $total === null) {
    echo "Invalid order details.";
    exit;
}

# Prepare line items for Stripe based on session cart
$line_items = [];
foreach ($_SESSION['cart'] as $cart_item) {
    # Convert RM to cents (e.g., RM 20.00 = 2000 cents)
    $unit_amount = $cart_item['price'] * 100;

    $line_items[] = [
        "quantity" => $cart_item['quantity'],
        "price_data" => [
            "currency" => "myr",  # Use MYR for Malaysian Ringgit
            "unit_amount" => (int) $unit_amount,  # Convert to cents
            "product_data" => [
                "name" => $cart_item['menu_name'],
                "description" => $cart_item['description']
            ]
        ]
    ];
}

# Create Stripe checkout session with dynamic cart items
$checkout_session = \Stripe\Checkout\Session::create([
    "mode" => "payment",
    "success_url" => "http://localhost/rms/success.php", #if success go to success.php
    "cancel_url" => "http://localhost/rms/cart.php", #if fail go back to cart.php
    "locale" => "auto",
    "line_items" => $line_items
]);

# Redirect to the Stripe checkout page
http_response_code(303);
header("Location: " . $checkout_session->url);
exit;
?>
