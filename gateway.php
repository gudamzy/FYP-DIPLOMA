<?php
session_start();
require __DIR__ . "/vendor/autoload.php"; #taking from vendor autoload stripe
require_once __DIR__ . "/config.php";     #Stripe secret key ($stripe_secret_key), not in GitHub

# Set API key for Stripe
\Stripe\Stripe::setApiKey($stripe_secret_key);

# Get the table number and total from POST request
$table_no = $_POST['table_no'] ?? null;
$total = $_POST['total'] ?? null;

if ($table_no === null || $total === null) {
    echo "Invalid order details.";
    exit;
}

# Remember the table for success.php
$_SESSION['table_no'] = $table_no;

if (empty($_SESSION['cart'])) {
    echo "Your cart is empty.";
    exit;
}

# Work out this website's address automatically,
# e.g. http://localhost/FYP-DIPLOMA (XAMPP) or https://yoursite.infinityfreeapp.com (hosting)
$is_https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
$scheme   = $is_https ? 'https' : 'http';
$folder   = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
$base_url = $scheme . '://' . $_SERVER['HTTP_HOST'] . $folder;

# Prepare line items for Stripe based on session cart
$line_items = [];
foreach ($_SESSION['cart'] as $cart_item) {
    # Convert RM to cents (e.g., RM 20.00 = 2000 cents)
    $unit_amount = (int) round($cart_item['price'] * 100);

    $product_data = ["name" => $cart_item['menu_name']];
    if (!empty($cart_item['description'])) {
        $product_data["description"] = $cart_item['description'];
    }

    $line_items[] = [
        "quantity" => (int) $cart_item['quantity'],
        "price_data" => [
            "currency" => "myr",  # Use MYR for Malaysian Ringgit
            "unit_amount" => $unit_amount,
            "product_data" => $product_data
        ]
    ];
}

# Create Stripe checkout session with dynamic cart items
$checkout_session = \Stripe\Checkout\Session::create([
    "mode" => "payment",
    # {CHECKOUT_SESSION_ID} is filled in by Stripe, so success.php can confirm the payment
    "success_url" => $base_url . "/success.php?session_id={CHECKOUT_SESSION_ID}", #if success go to success.php
    "cancel_url" => $base_url . "/cart.php?table_no=" . urlencode($table_no), #if cancelled go back to cart.php
    "locale" => "auto",
    "line_items" => $line_items
]);

# Redirect to the Stripe checkout page
http_response_code(303);
header("Location: " . $checkout_session->url);
exit;
?>
