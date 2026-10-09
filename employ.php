<?php
$page_title = 'Employee Panel'; 
include('./includes/header_employ.html'); #header
?>

<div class="hero" style="background-image: url('image/resss.jpeg'); background-size: cover; padding: 50px; text-align: center; color: white; background-color: #003366;">
    <h1 style="font-size: 3rem; margin-bottom: 10px; font-weight: bold;">Welcome to the Employee Panel</h1>
    <p style="font-size: 1.5rem; color: #f0f0f0;">Manage your restaurant effortlessly with our powerful tools and insights.</p>
</div>


<div class="stats-panel" style="display: flex; justify-content: space-around; margin: 20px auto; text-align: center; flex-wrap: wrap; gap: 20px;">
    <div class="stat" style="padding: 20px; border: 1px solid #ddd; border-radius: 10px; width: 30%; min-width: 250px; background-color: #e3f2fd; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
        <h2 style="color: #007bff; font-size: 2rem; font-weight: bold;">**</h2>
        <p style="font-size: 1.2rem; font-weight: 600;">Pending Orders</p>
    </div>
    <div class="stat" style="padding: 20px; border: 1px solid #ddd; border-radius: 10px; width: 30%; min-width: 250px; background-color: #e8f5e9; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
        <h2 style="color: #28a745; font-size: 2rem; font-weight: bold;">**</h2>
        <p style="font-size: 1.2rem; font-weight: 600;">Total Menu Items</p>
    </div>
    <div class="stat" style="padding: 20px; border: 1px solid #ddd; border-radius: 10px; width: 30%; min-width: 250px; background-color: #fff3e0; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
        <h2 style="color: #ff9800; font-size: 2rem; font-weight: bold;">85%</h2>
        <p style="font-size: 1.2rem; font-weight: 600;">Positive Feedback</p>
    </div>
</div>

<div class="feature-section" style="display: flex; justify-content: space-around; flex-wrap: wrap; padding: 20px; gap: 20px;">
    <div class="card" style="width: 30%; padding: 20px; text-align: center; border: 1px solid #ddd; border-radius: 10px; background-color: #e3f2fd; box-shadow: 0 4px 6px rgba(0,0,0,0.1); transition: transform 0.3s, box-shadow 0.3s;">
        <img src="image/manageorder.jpg" alt="Order Management" style="height: 90px; margin-bottom: 10px;">
        <h3 style="color: #007bff; font-weight: bold;">Order Management</h3>
        <p style="font-size: 1rem;">Keep your orders fresh .</p>
    </div>

    <div class="card" style="width: 30%; padding: 20px; text-align: center; border: 1px solid #ddd; border-radius: 10px; background-color: #f1f8e9; box-shadow: 0 4px 6px rgba(0,0,0,0.1); transition: transform 0.3s, box-shadow 0.3s;">
        <img src="image/process.jpg" alt="Order Processing" style="height: 90px; margin-bottom: 10px;">
        <h3 style="color: #2e7d32; font-weight: bold;">Order Processing</h3>
        <p style="font-size: 1rem;">Track, prioritize, and manage customer orders seamlessly.</p>
    </div>

    <div class="card" style="width: 30%; padding: 20px; text-align: center; border: 1px solid #ddd; border-radius: 10px; background-color: #fff8e1; box-shadow: 0 4px 6px rgba(0,0,0,0.1); transition: transform 0.3s, box-shadow 0.3s;">
	<img src="image/feedback.png" alt="Customer" style="height: 90px; margin-bottom: 10px;">
        <h3 style="color: #ff6f00; font-weight: bold;">Customer Feedback</h3>
        <p style="font-size: 1rem;">Access reviews to understand customer preferences and improve services.</p>
    </div>
</div>

<style>
    

    /* Responsive Design */
    @media (max-width: 768px) {
        .stats-panel, .feature-section {
            flex-direction: column;
            align-items: center;
        }
        .stat, .card {
            width: 90%;
        }
    }
</style>

<?php
include('./includes/footer.html'); #footer
?>
