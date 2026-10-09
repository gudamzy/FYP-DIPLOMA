<?php
session_start();  

# Check if table number is submitted via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    # Store the table number in the session
    $_SESSION['table_no'] = $_POST['table_no'];
    # Redirect to the order page with the table number in the URL
    header("Location: cust_order.php?table_no=" . $_SESSION['table_no']);
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Table Number</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7fc;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .container {
            background-color: white;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        h2 {
            color: #4a90e2;
            margin-bottom: 20px;
            font-size: 24px;
        }

        label {
            font-size: 18px;
            margin-bottom: 10px;
            display: block;
            color: #333;
        }

        input[type="number"] {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            font-size: 16px;
            border: 2px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            outline: none;
        }

        input[type="number"]:focus {
            border-color: #4a90e2;
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #4a90e2;
            border: none;
            color: white;
            font-size: 18px;
            cursor: pointer;
            border-radius: 4px;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #357abd;
        }

        .footer {
            margin-top: 20px;
            font-size: 14px;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Enter Table Number</h2>
        <form method="POST" action="">
            <label for="table_no">Table Number:</label>
            <input type="number" name="table_no" min="1" max="90" required>
            <button type="submit">Proceed to Menu</button>
        </form>
    </div>
</body>
</html>
