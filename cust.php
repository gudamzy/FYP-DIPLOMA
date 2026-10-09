<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RESTAURANT MANAGEMENT SYSTEM</title>
    
    <?php
    $page_title = 'CUSTOMER';
    include ('./includes/header_cust.html');#header
    ?>
    
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            color: #333;
        }

        header {
            background-color: #003366;
            color: #fff;
            padding: 10px 0;
            text-align: center;
            font-size: 1.5rem;
            font-weight: bold;
        }

        .main-section {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 40px;
            background: linear-gradient(to right, #003366, #003b6f);
            color: #fff;
            border-radius: 15px;
            margin: 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        .text-section {
            max-width: 50%;
        }

        .text-section h1 {
            font-size: 3rem;
            margin-bottom: 20px;
        }

        .text-section h1 span {
            color: #ffda79;
        }

        .text-section p {
            color: #f1f1f1;
            line-height: 1.6;
            margin-bottom: 30px;
        }

        .rating {
            display: flex;
            align-items: center;
            margin-top: 15px;
        }

        .rating img {
            width: 20px;
            margin-right: 10px;
        }

        .rating span {
            color: #ffda79;
        }

        .image-section {
            position: relative;
        }

        .image-section img {
            border-radius: 10px;
            max-width: 100%;
            height: auto;
        }

        .image-section .icon {
            position: absolute;
            bottom: -20px;
            right: -20px;
            display: flex;
            align-items: center;
            background-color: #fff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            border-radius: 50%;
            padding: 10px;
        }

        .image-section .icon img {
            width: 40px;
            margin: 0 5px;
        }
    </style>
</head>
<body>

<header>
    Restaurant Management System
</header>

<section class="main-section">
    <div class="text-section">
        <h1>Beautiful Place,<br> <span>Delicious Food</span>.</h1>
        <p>We bring you a journey of flavor, crafted with passion and tradition, delivering unforgettable moments with every bite.</p>
        <div class="rating">
            <img src="image/star.png" alt="Food Image">
            <span>4.8 out of 5 based on 3000+ reviews</span>
        </div>
    </div>
    <div class="image-section">
        <img src="image/restaurant.jpg" alt="Food Image">
    </div>
</section>

<?php
include('./includes/footer.html'); # footer
?>

</body>
</html>
