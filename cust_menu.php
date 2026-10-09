<?php
$page_title = 'RESTAURANT MENU';
include('./includes/header_cust.html');
require_once('mysqli.php'); #dbc connection
global $dbc;

# Fetch menu
$query  = "SELECT menu, description, price FROM rms_menu ORDER BY id";
$result = mysqli_query($dbc, $query);

# Group menu items into categories (each category has its own colour)
$categories = [
    'Set Meals'   => ['color' => 'blue',   'items' => []],
    'Nasi Goreng' => ['color' => 'green',  'items' => []],
    'Drinks'      => ['color' => 'orange', 'items' => []],
    'Others'      => ['color' => 'blue',   'items' => []],
];

if ($result) {
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        $name = strtoupper(trim($row['menu']));
        $desc = strtoupper(trim($row['description']));

        if (strpos($name, 'SET') === 0) {
            $categories['Set Meals']['items'][] = $row;
        } elseif ($desc === 'NASI GORENG' || strpos($name, 'NASI GORENG') === 0) {
            $categories['Nasi Goreng']['items'][] = $row;
        } elseif ($desc === 'MINUMAN') {
            $categories['Drinks']['items'][] = $row;
        } else {
            $categories['Others']['items'][] = $row;
        }
    }
    mysqli_free_result($result);
}

$total = 0;
foreach ($categories as $c) {
    $total += count($c['items']);
}
?>
<style>
  .hero {
    position: relative;
    background: #2c3e50 url('image/resss.jpeg') center / cover no-repeat;
    padding: 60px 20px;
    text-align: center;
    color: #fff;
  }
  .hero::before {
    content: "";
    position: absolute;
    inset: 0;
    background: rgba(0, 0, 0, 0.35);
  }
  .hero > * { position: relative; }
  .hero h1 { font-size: 3rem; margin: 0 0 10px; font-weight: bold; }
  .hero p  { font-size: 1.4rem; margin: 0; color: #f0f0f0; }

  .menu-page {
    max-width: 1200px;
    margin: 0 auto;
    padding: 30px 20px 50px;
    font-family: Arial, sans-serif;
  }
  .menu-section { margin-bottom: 36px; }
  .menu-section h2 {
    font-size: 1.6rem;
    font-weight: bold;
    margin: 0 0 16px;
  }
  .menu-section.blue h2   { color: #007bff; }
  .menu-section.green h2  { color: #2e7d32; }
  .menu-section.orange h2 { color: #ff6f00; }

  .menu-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 20px;
  }
  .menu-card {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    padding: 20px;
    border: 1px solid #ddd;
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s, box-shadow 0.3s;
  }
  .menu-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 18px rgba(0, 0, 0, 0.12);
  }
  .blue .menu-card   { background: #e3f2fd; }
  .green .menu-card  { background: #f1f8e9; }
  .orange .menu-card { background: #fff8e1; }

  .menu-card h3 {
    margin: 0 0 6px;
    font-size: 1.15rem;
    font-weight: bold;
    color: #333;
  }
  .menu-card .desc {
    margin: 0 0 14px;
    font-size: 0.95rem;
    line-height: 1.5;
    color: #555;
  }
  .menu-card .price {
    font-size: 1.4rem;
    font-weight: bold;
  }
  .blue .price   { color: #007bff; }
  .green .price  { color: #28a745; }
  .orange .price { color: #ff9800; }

  .menu-cta {
    text-align: center;
    margin-top: 10px;
  }
  .menu-cta a {
    display: inline-block;
    background: #2c3e50;
    color: #fff;
    text-decoration: none;
    font-size: 1.2rem;
    font-weight: bold;
    padding: 14px 40px;
    border-radius: 8px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.15);
    transition: background 0.3s;
  }
  .menu-cta a:hover { background: #34495e; }
  .menu-empty {
    text-align: center;
    color: #dc3545;
    font-size: 1.1rem;
  }
  @media (max-width: 768px) {
    .hero h1 { font-size: 2rem; }
    .hero p  { font-size: 1.1rem; }
    .menu-grid { grid-template-columns: 1fr; }
  }
</style>

<div class="hero">
  <h1>Our Menu</h1>
  <p>Freshly cooked meals and drinks, made to order.</p>
</div>

<div class="menu-page">
<?php if ($total === 0): ?>
  <p class="menu-empty">There are currently no menu items.</p>
<?php else: ?>
  <?php foreach ($categories as $category => $cat): ?>
    <?php if (count($cat['items']) === 0) continue; ?>
    <section class="menu-section <?php echo $cat['color']; ?>">
      <h2><?php echo htmlspecialchars($category); ?></h2>
      <div class="menu-grid">
        <?php foreach ($cat['items'] as $item):
            $desc = trim($item['description']);
            # Hide descriptions that only repeat the category (e.g. "MINUMAN")
            $showDesc = $desc !== '' && !in_array(strtoupper($desc), ['NASI GORENG', 'MINUMAN'], true);
        ?>
          <div class="menu-card">
            <div>
              <h3><?php echo htmlspecialchars($item['menu']); ?></h3>
              <?php if ($showDesc): ?>
                <p class="desc"><?php echo htmlspecialchars($desc); ?></p>
              <?php endif; ?>
            </div>
            <span class="price">RM <?php echo number_format((float)$item['price'], 2); ?></span>
          </div>
        <?php endforeach; ?>
      </div>
    </section>
  <?php endforeach; ?>

  <div class="menu-cta">
    <a href="cust_table.php">Order Now</a>
  </div>
<?php endif; ?>
</div>

<?php
mysqli_close($dbc); # Close the database connection.
include('./includes/footer.html'); #footer
?>
