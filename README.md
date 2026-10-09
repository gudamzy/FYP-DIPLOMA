# Restaurant Management System (RMS)

Final Year Project (Diploma). A web-based restaurant management system built with PHP and MySQL, with online payment through Stripe.

## Features

**Customer**
- Browse the menu
- Choose a table
- Add items to cart and place orders
- Pay online with Stripe

**Employee**
- Log in to the employee panel
- View incoming orders
- Mark orders as ready

**Admin**
- Log in to the admin panel
- Add, update and delete menu items
- Add, view and delete employees
- View and delete orders

## Tech Stack

- PHP
- MySQL / MariaDB
- HTML & CSS
- Stripe PHP library (installed with Composer)
- XAMPP (local development)

## Getting Started

### 1. Requirements
- [XAMPP](https://www.apachefriends.org/) (Apache + MySQL)
- [Composer](https://getcomposer.org/)
- A Stripe account (test mode is enough)

### 2. Clone the project
Clone into the XAMPP `htdocs` folder:
```bash
cd C:\xampp\htdocs
git clone https://github.com/gudamzy/FYP-DIPLOMA.git
cd FYP-DIPLOMA
```

### 3. Install dependencies
```bash
composer install
```

### 4. Set up the database
1. Start **Apache** and **MySQL** in the XAMPP Control Panel.
2. Open phpMyAdmin at `http://localhost/phpmyadmin`.
3. Create a database named `restaurant`.
4. Import the `restaurant.sql` file into it.

Database connection settings are in `mysqli.php` (default: user `root`, no password, host `localhost`).

### 5. Add your Stripe key
Create a file named `config.php` in the project folder:
```php
<?php
$stripe_secret_key = "sk_test_your_key_here";
```
Get your test secret key from the Stripe Dashboard → Developers → API keys.

> `config.php` is listed in `.gitignore` so the key is never committed. Do not put real keys in any other file.

### 6. Run
Open in your browser:
```
http://localhost/FYP-DIPLOMA/mainpage.html
```

- Admin login: `admin_login.php`
- Employee login: `employ_login.php`

For test payments, use Stripe's test card `4242 4242 4242 4242` with any future expiry date and any CVC.

## Project Structure

```
FYP-DIPLOMA/
├── image/            # Images and logos
├── includes/         # Shared headers, footer and CSS
├── admin*.php        # Admin pages
├── employ*.php       # Employee pages
├── cust*.php         # Customer pages
├── menu_*.php        # Menu management
├── order_*.php       # Order management
├── cart.php          # Shopping cart
├── gateway.php       # Stripe payment
├── success.php       # Payment success page
├── mysqli.php        # Database connection
└── config.php        # Stripe key (not in repo, create it yourself)
```
