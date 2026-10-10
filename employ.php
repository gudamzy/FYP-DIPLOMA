<?php
require_once __DIR__ . '/includes/security.php';
require_staff(); # login check before anything is printed
$page_title = 'Employee Panel';
include('./includes/header_employ.html'); #header
require_once('mysqli.php'); #dbc connection
global $dbc;

# Live numbers for the stat boxes
function count_rows($dbc, $sql) {
    $result = @mysqli_query($dbc, $sql);
    if (!$result) {
        return 0;
    }
    $row = mysqli_fetch_row($result);
    mysqli_free_result($result);
    return (int)$row[0];
}

$pending_orders = count_rows($dbc, "SELECT COUNT(*) FROM rms_order WHERE status = 'Pending'");
$total_menu     = count_rows($dbc, "SELECT COUNT(*) FROM rms_menu");
mysqli_close($dbc);
?>

<div class="hero" style="background-image: url('image/resss.jpeg'); background-size: cover; padding: 50px; text-align: center; color: white; background-color: #003366;">
    <h1 class="fade-up" style="font-size: 3rem; margin-bottom: 10px; font-weight: bold;">Welcome to the Employee Panel</h1>
    <p class="fade-up" style="font-size: 1.5rem; color: #f0f0f0; animation-delay: 0.15s;">Manage your restaurant effortlessly with our powerful tools and insights.</p>
</div>


<div class="stats-panel" style="display: flex; justify-content: space-around; margin: 20px auto; text-align: center; flex-wrap: wrap; gap: 20px;">
    <a href="order_ready.php" class="stat fade-up" style="padding: 20px; border: 1px solid #ddd; border-radius: 10px; width: 30%; min-width: 250px; background-color: #e3f2fd; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); animation-delay: 0.2s;">
        <h2 class="count" data-target="<?php echo $pending_orders; ?>" style="color: #007bff; font-size: 2rem; font-weight: bold;"><?php echo $pending_orders; ?></h2>
        <p style="font-size: 1.2rem; font-weight: 600;">Pending Orders</p>
    </a>
    <div class="stat fade-up" style="padding: 20px; border: 1px solid #ddd; border-radius: 10px; width: 30%; min-width: 250px; background-color: #e8f5e9; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); animation-delay: 0.3s;">
        <h2 class="count" data-target="<?php echo $total_menu; ?>" style="color: #28a745; font-size: 2rem; font-weight: bold;"><?php echo $total_menu; ?></h2>
        <p style="font-size: 1.2rem; font-weight: 600;">Total Menu Items</p>
    </div>
    <div class="stat fade-up" style="padding: 20px; border: 1px solid #ddd; border-radius: 10px; width: 30%; min-width: 250px; background-color: #fff3e0; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); animation-delay: 0.4s;">
        <h2 class="count" data-target="85" data-suffix="%" style="color: #ff9800; font-size: 2rem; font-weight: bold;">85%</h2>
        <p style="font-size: 1.2rem; font-weight: 600;">Positive Feedback</p>
    </div>
</div>

<div class="feature-section" style="display: flex; justify-content: space-around; flex-wrap: wrap; padding: 20px; gap: 20px;">
    <a href="employ_order.php" class="card fade-up" style="width: 30%; padding: 20px; text-align: center; border: 1px solid #ddd; border-radius: 10px; background-color: #e3f2fd; box-shadow: 0 4px 6px rgba(0,0,0,0.1); transition: transform 0.3s, box-shadow 0.3s; animation-delay: 0.5s;">
        <img src="image/manageorder.jpg" alt="Order Management" style="height: 90px; margin-bottom: 10px;">
        <h3 style="color: #007bff; font-weight: bold;">Order Management</h3>
        <p style="font-size: 1rem;">Keep your orders fresh .</p>
    </a>

    <a href="order_ready.php" class="card fade-up" style="width: 30%; padding: 20px; text-align: center; border: 1px solid #ddd; border-radius: 10px; background-color: #f1f8e9; box-shadow: 0 4px 6px rgba(0,0,0,0.1); transition: transform 0.3s, box-shadow 0.3s; animation-delay: 0.6s;">
        <img src="image/process.jpg" alt="Order Processing" style="height: 90px; margin-bottom: 10px;">
        <h3 style="color: #2e7d32; font-weight: bold;">Order Processing</h3>
        <p style="font-size: 1rem;">Track, prioritize, and manage customer orders seamlessly.</p>
    </a>

    <div class="card fade-up" style="width: 30%; padding: 20px; text-align: center; border: 1px solid #ddd; border-radius: 10px; background-color: #fff8e1; box-shadow: 0 4px 6px rgba(0,0,0,0.1); transition: transform 0.3s, box-shadow 0.3s; animation-delay: 0.7s;">
	<img src="image/feedback.png" alt="Customer" style="height: 90px; margin-bottom: 10px;">
        <h3 style="color: #ff6f00; font-weight: bold;">Customer Feedback</h3>
        <p style="font-size: 1rem;">Access reviews to understand customer preferences and improve services.</p>
    </div>
</div>

<style>
    /* Links look the same as the original boxes */
    a.stat, a.card {
        display: block;
        color: inherit;
        text-decoration: none;
    }

    /* Boxes lift up when you point at them (or tap on a phone) */
    .stat, .card {
        box-sizing: border-box; /* keeps 3 boxes in one row on smaller laptops */
        transition: transform 0.3s ease, box-shadow 0.3s ease !important;
    }
    .stat:hover, .card:hover,
    .stat:active, .card:active {
        transform: translateY(-8px) scale(1.02);
        box-shadow: 0 14px 24px rgba(0, 0, 0, 0.18) !important;
    }

    /* Picture inside the feature boxes zooms a little */
    .card img {
        transition: transform 0.3s ease;
    }
    .card:hover img {
        transform: scale(1.08) rotate(-2deg);
    }

    /* Everything slides in gently when the page opens */
    .fade-up {
        animation: fadeUp 0.6s ease backwards;
    }
    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(20px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    /* Turn animations off for people who prefer less motion */
    @media (prefers-reduced-motion: reduce) {
        .fade-up { animation: none; }
        .stat, .card, .card img { transition: none !important; }
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .stats-panel, .feature-section {
            flex-direction: column;
            align-items: center;
        }
        .stat, .card {
            width: 90% !important;
        }
    }
</style>

<script>
    // Numbers count up from 0 when the page opens
    document.querySelectorAll('.count').forEach(function (el) {
        var target = parseInt(el.getAttribute('data-target'), 10) || 0;
        var suffix = el.getAttribute('data-suffix') || '';
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches || target === 0) {
            el.textContent = target + suffix;
            return;
        }
        var start = null, duration = 1000;
        function step(time) {
            if (!start) start = time;
            var progress = Math.min((time - start) / duration, 1);
            el.textContent = Math.round(target * progress) + suffix;
            if (progress < 1) requestAnimationFrame(step);
        }
        el.textContent = '0' + suffix;
        requestAnimationFrame(step);
    });
</script>

<?php
include('./includes/footer.html'); #footer
?>
