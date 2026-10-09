<?php
session_start();
$page_title = 'CUSTOMER';

# The "Easy Payment" card only opens once this customer has paid for an order
# (success.php sets $_SESSION['has_paid'] after a successful payment).
$has_paid = !empty($_SESSION['has_paid']);

include ('./includes/header_cust.html'); #header

# Background photo for the big hero box at the top.
# To use a different photo, put it in the "image" folder and change the file name here.
$hero_image = 'https://images.unsplash.com/photo-1544880665-abed6125538b?auto=format&fit=crop&w=1920&q=70';
?>
<style>
    /* ===== Option B: full-width photo hero ===== */
    .hero-full {
        position: relative;
        min-height: 520px;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        background-color: #2c3e50;
        background-position: center;
        background-size: cover;
        background-repeat: no-repeat;
        color: #fff;
        font-family: Arial, sans-serif;
        padding: 60px 20px;
    }
    .hero-full::before {
        content: "";
        position: absolute;
        inset: 0;
        background: linear-gradient(180deg, rgba(44, 62, 80, 0.45), rgba(44, 62, 80, 0.75));
    }
    .hero-full .inner {
        position: relative;
        max-width: 760px;
    }
    .photo-credit {
        position: absolute;
        top: 12px;
        right: 16px;
        font-size: 0.75rem;
        color: rgba(255, 255, 255, 0.6);
        text-decoration: none;
    }
    .photo-credit:hover {
        color: #fff;
    }
    .eyebrow {
        display: inline-block;
        border: 1px solid rgba(255, 255, 255, 0.35);
        color: #dfe6ec;
        font-size: 0.85rem;
        letter-spacing: 2px;
        padding: 7px 16px;
        border-radius: 20px;
        margin-bottom: 22px;
    }
    .hero-full h1, .hero-full p {
        text-shadow: 0 2px 12px rgba(0, 0, 0, 0.45);
    }
    .hero-full h1 {
        font-size: 3.6rem;
        line-height: 1.1;
        margin: 0 0 20px;
    }
    .hero-full h1 span { color: #28a745; }
    .hero-full p {
        font-size: 1.2rem;
        line-height: 1.7;
        color: #ecf0f1;
        margin: 0 auto 32px;
        max-width: 620px;
    }
    .hero-buttons {
        display: flex;
        gap: 14px;
        justify-content: center;
        flex-wrap: wrap;
    }
    .btn-primary, .btn-outline {
        display: inline-block;
        text-decoration: none;
        font-weight: bold;
        font-size: 1.05rem;
        padding: 14px 34px;
        border-radius: 8px;
        border: 2px solid transparent;
        transition: all 0.3s;
    }
    .btn-primary {
        background: #28a745;
        border-color: #28a745;
        color: #fff;
        box-shadow: 0 4px 12px rgba(40, 167, 69, 0.4);
    }
    .btn-primary:hover { background: #218838; border-color: #218838; transform: translateY(-2px); }
    .btn-outline { border-color: #fff; color: #fff; }
    .btn-outline:hover { background: #fff; color: #2c3e50; }

    /* Stats strip overlapping the hero */
    .wrap {
        max-width: 1240px;
        margin: 0 auto;
        padding: 0 20px;
        font-family: Arial, sans-serif;
        color: #333;
    }
    .stats {
        position: relative;
        margin-top: -60px;
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 24px;
    }
    .stat {
        padding: 26px 20px;
        border: 1px solid #ddd;
        border-radius: 10px;
        text-align: center;
        box-shadow: 0 6px 14px rgba(0, 0, 0, 0.12);
    }
    .stat h2 { margin: 0 0 6px; font-size: 2.4rem; }
    .stat p  { margin: 0; font-weight: 600; font-size: 1.1rem; }
    .stat.blue   { background: #e3f2fd; } .stat.blue h2   { color: #007bff; }
    .stat.green  { background: #e8f5e9; } .stat.green h2  { color: #28a745; }
    .stat.orange { background: #fff3e0; } .stat.orange h2 { color: #ff9800; }

    .section-title {
        text-align: center;
        margin: 56px 0 26px;
    }
    .section-title h2 {
        margin: 0 0 8px;
        font-size: 2rem;
        color: #2c3e50;
    }
    .section-title p { margin: 0; color: #777; }

    .feature-section {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 24px;
        margin-bottom: 20px;
    }
    .card {
        display: block;
        text-decoration: none;
        color: #333;
        background: #fff;
        padding: 28px 24px;
        text-align: center;
        border: 1px solid #e3e7ec;
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.08);
        transition: transform 0.3s, box-shadow 0.3s;
    }
    .card:hover { transform: translateY(-4px); box-shadow: 0 10px 18px rgba(0, 0, 0, 0.12); }
    .card .emoji {
        width: 64px;
        height: 64px;
        margin: 0 auto 14px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.9rem;
    }
    .card.blue .emoji   { background: #e3f2fd; }
    .card.green .emoji  { background: #e8f5e9; }
    .card.orange .emoji { background: #fff3e0; }
    .card.locked {
        cursor: not-allowed;
        opacity: 0.6;
        filter: grayscale(0.4);
    }
    .card.locked:hover {
        transform: none;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.08);
    }
    .card-tag {
        display: inline-block;
        margin-top: 14px;
        font-size: 0.85rem;
        font-weight: bold;
        color: #888;
        background: #eceff1;
        padding: 6px 12px;
        border-radius: 20px;
    }
    .card-tag.ok {
        color: #2e7d32;
        background: #e8f5e9;
    }
    .card h3 { margin: 0 0 8px; font-size: 1.25rem; color: #2c3e50; }
    .card p  { margin: 0; line-height: 1.5; color: #666; }

    @media (max-width: 860px) {
        .hero-full { min-height: 440px; }
        .hero-full h1 { font-size: 2.4rem; }
        .stats, .feature-section { grid-template-columns: 1fr; }
        .stats { margin-top: -40px; }
    }
</style>

<section class="hero-full" style="background-image: url('<?php echo htmlspecialchars($hero_image); ?>');">
    <div class="inner">
        <span class="eyebrow">RESTAURANT MANAGEMENT SYSTEM</span>
        <h1>Beautiful Place,<br><span>Delicious Food</span>.</h1>
        <p>We bring you a journey of flavor, crafted with passion and tradition, delivering unforgettable moments with every bite.</p>
        <div class="hero-buttons">
            <a class="btn-primary" href="cust_table.php">Order Now</a>
            <a class="btn-outline" href="cust_menu.php">View Menu</a>
        </div>
    </div>
    <a class="photo-credit" href="https://unsplash.com/photos/people-inside-restaurant-with-mason-jar-pendant-lamps-gf_FM7eNJSw" target="_blank" rel="noopener">Photo: Rosie Sun / Unsplash</a>
</section>

<div class="wrap">
    <div class="stats">
        <div class="stat blue">
            <h2>4.8 ★</h2>
            <p>Average Rating</p>
        </div>
        <div class="stat green">
            <h2>3000+</h2>
            <p>Happy Reviews</p>
        </div>
        <div class="stat orange">
            <h2>Daily</h2>
            <p>Freshly Cooked</p>
        </div>
    </div>

    <div class="section-title">
        <h2>Why Dine With Us</h2>
        <p>Good food, quick service and an easy way to order.</p>
    </div>

    <div class="feature-section">
        <a class="card blue" href="cust_menu.php">
            <div class="emoji">🍛</div>
            <h3>Fresh Menu</h3>
            <p>Set meals, nasi goreng and drinks, cooked fresh every day.</p>
        </a>
        <a class="card green" href="cust_table.php">
            <div class="emoji">🍽️</div>
            <h3>Order From Your Table</h3>
            <p>Pick your table number, choose your food and send your order.</p>
        </a>
        <?php if ($has_paid): ?>
            <a class="card orange" href="success.php">
                <div class="emoji">💳</div>
                <h3>Easy Payment</h3>
                <p>Pay securely online and track your order status.</p>
                <span class="card-tag ok">View your order →</span>
            </a>
        <?php else: ?>
            <div class="card orange locked" aria-disabled="true" title="Place and pay for an order first">
                <div class="emoji">💳</div>
                <h3>Easy Payment</h3>
                <p>Pay securely online and track your order status.</p>
                <span class="card-tag">🔒 Available after payment</span>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
include('./includes/footer.html'); # footer
?>
