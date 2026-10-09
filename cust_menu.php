<?php
$page_title = 'RESTAURANT MENU';
include('./includes/header_cust.html');
require_once('mysqli.php'); #dbc connection
global $dbc;

# Fetch menu
$query  = "SELECT menu, description, price FROM rms_menu ORDER BY id";
$result = mysqli_query($dbc, $query);

# Group menu items into categories
$categories = [
    'Set Meals'   => [],
    'Nasi Goreng' => [],
    'Minuman'     => [],
    'Lain-lain'   => [],
];

if ($result) {
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        $name = strtoupper(trim($row['menu']));
        $desc = strtoupper(trim($row['description']));

        if (strpos($name, 'SET') === 0) {
            $categories['Set Meals'][] = $row;
        } elseif ($desc === 'NASI GORENG' || strpos($name, 'NASI GORENG') === 0) {
            $categories['Nasi Goreng'][] = $row;
        } elseif ($desc === 'MINUMAN') {
            $categories['Minuman'][] = $row;
        } else {
            $categories['Lain-lain'][] = $row;
        }
    }
    mysqli_free_result($result);
}

$total = array_sum(array_map('count', $categories));
?>
<style>
  .menu-page {
    max-width: 1100px;
    margin: 0 auto;
    padding: 32px 20px 60px;
    font-family: Arial, sans-serif;
  }
  .menu-hero {
    text-align: center;
    margin-bottom: 36px;
  }
  .menu-hero h1 {
    margin: 0 0 8px;
    font-size: 34px;
    letter-spacing: 2px;
    color: #003366;
  }
  .menu-hero p {
    margin: 0;
    color: #666;
    font-size: 15px;
  }
  .menu-section {
    margin-bottom: 40px;
  }
  .menu-section h2 {
    display: flex;
    align-items: center;
    gap: 12px;
    margin: 0 0 18px;
    font-size: 20px;
    color: #003366;
    text-transform: uppercase;
    letter-spacing: 1px;
  }
  .menu-section h2::after {
    content: "";
    flex: 1;
    height: 2px;
    background: linear-gradient(90deg, #003366, transparent);
  }
  .menu-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
    gap: 18px;
  }
  .menu-card {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    background: #fff;
    border: 1px solid #e6e9ef;
    border-radius: 12px;
    padding: 18px 20px;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.06);
    transition: transform 0.2s, box-shadow 0.2s;
  }
  .menu-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 18px rgba(0, 51, 102, 0.15);
  }
  .menu-card h3 {
    margin: 0 0 6px;
    font-size: 17px;
    color: #222;
  }
  .menu-card .desc {
    margin: 0 0 16px;
    font-size: 13px;
    line-height: 1.5;
    color: #777;
  }
  .menu-card .price {
    align-self: flex-start;
    background: #003366;
    color: #fff;
    font-weight: bold;
    font-size: 15px;
    padding: 6px 14px;
    border-radius: 20px;
  }
  .menu-cta {
    text-align: center;
    margin-top: 10px;
  }
  .menu-cta a {
    display: inline-block;
    background: linear-gradient(45deg, #ff5f6d, #ffc3a0);
    color: #fff;
    text-decoration: none;
    font-size: 18px;
    font-weight: bold;
    padding: 14px 36px;
    border-radius: 30px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
    transition: all 0.3s;
  }
  .menu-cta a:hover {
    background: linear-gradient(45deg, #ff416c, #ff4b2b);
    transform: translateY(-2px);
  }
  .menu-empty {
    text-align: center;
    color: #c0392b;
    font-size: 16px;
  }
  @media (max-width: 600px) {
    .menu-hero h1 { font-size: 26px; }
    .menu-grid { grid-template-columns: 1fr; }
  }
</style>

<div class="menu-page">
  <div class="menu-hero">
    <h1>RESTAURANT MENU</h1>
    <p>Pilih hidangan kegemaran anda</p>
  </div>

<?php if ($total === 0): ?>
  <p class="menu-empty">Tiada menu buat masa ini.</p>
<?php else: ?>
  <?php foreach ($categories as $category => $items): ?>
    <?php if (count($items) === 0) continue; ?>
    <section class="menu-section">
      <h2><?php echo htmlspecialchars($category); ?></h2>
      <div class="menu-grid">
        <?php foreach ($items as $item):
            $desc = trim($item['description']);
            # Hide the description when it only repeats the category name
            $showDesc = $desc !== '' && strcasecmp($desc, $category) !== 0;
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
    <a href="cust_table.php">Order Sekarang</a>
  </div>
<?php endif; ?>
</div>

<?php
mysqli_close($dbc); # Close the database connection.
include('./includes/footer.html'); #footer
?>
