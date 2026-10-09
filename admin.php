<?php
$page_title = 'ADMIN';
include ('./includes/header_admin.html');
?>

<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f8f9fa;
        margin: 0;
        padding: 0;
    }

    .container {
        max-width: 1200px;
        margin: 50px auto;
        padding: 20px;
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    .header-section {
        text-align: center;
        margin-bottom: 30px;
    }

    .header-section h2 {
        font-size: 2.5rem;
        color: #003366;
        margin-bottom: 10px;
    }

    .header-section p {
        color: #555;
        font-size: 1.2rem;
    }

    .feature-section {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px;
        background: linear-gradient(45deg, #003366, #005a99);
        border-radius: 10px;
        color: #fff;
        margin-bottom: 30px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
    }

    .feature-section img {
        max-width: 40%;
        border-radius: 10px;
    }

    .feature-section .feature-text {
        flex: 1;
        padding-left: 20px;
    }

    .feature-section .feature-text h3 {
        font-size: 1.8rem;
        margin-bottom: 10px;
    }

    .feature-section .feature-text p {
        font-size: 1.1rem;
        line-height: 1.6;
    }

    .card-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 20px;
    }

    .card {
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        padding: 20px;
        text-align: center;
        text-decoration: none;
        color: inherit;
    }

    .card img {
        max-width: 80px;
        margin-bottom: 15px;
    }

    .card h3 {
        font-size: 1.5rem;
        color: #003366;
        margin-bottom: 10px;
    }

    .card p {
        font-size: 1rem;
        color: #555;
    }

    footer {
        text-align: center;
        margin-top: 40px;
        padding: 15px;
        background: #003366;
        color: #fff;
        font-size: 0.9rem;
    }
</style>

<div class="container">
    <!-- Header Section -->
    <div class="header-section">
        <h2>Welcome to the Restaurant Management System</h2>
        <p>Streamline your restaurant's operations with our comprehensive admin tools.</p>
    </div>

    <!-- Feature Section -->
    <div class="feature-section">
        <img src="image/cartoon.png" alt="Laki" style="height: 400px;">
        <div class="feature-text">
            <h3>Manage Your Restaurant with Ease</h3>
            <p>Our system provides you with all the tools you need to manage menus, customer orders, and much more—all in one user-friendly platform.</p>
        </div>
    </div>

    <!-- Card Section -->
    <div class="card-container">
        <div class="card">
            <img src="image/burger.png" alt="Menu Management">
            <h3>Menu Management</h3>
            <p>Add, update, or remove menu items seamlessly from the database.</p>
        </div>

        <div class="card">
            <img src="image/order.png" alt="Order Tracking">
            <h3>Order Tracking</h3>
            <p>View and manage all customer orders with real-time updates.</p>
        </div>

        <div class="card">
            <img src="image/staff.jpg" alt="Staff Management">
            <h3>Staff Management</h3>
            <p>Get valuable insights into customer preferences and sales trends.</p>
        </div>
    </div>
</div>



<?php
include ('./includes/footer.html');
?>
