<?php
$page_title = 'RESTAURANT MENU';
include ('./includes/header_admin.html'); #header
require_once ('mysqli.php'); # dbc connection.
global $dbc;

# Fetch menu
$query  = "SELECT menu, description, price FROM rms_menu ORDER BY id";
$result = mysqli_query($dbc, $query);
$count  = $result ? mysqli_num_rows($result) : 0;
?>
<style>
  .admin-menu {
    max-width: 1000px;
    margin: 0 auto;
    padding: 30px 20px 60px;
    font-family: Arial, sans-serif;
  }
  .admin-menu-head {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 12px;
    margin-bottom: 20px;
  }
  .admin-menu-head h1 {
    margin: 0;
    font-size: 26px;
    color: #003366;
  }
  .admin-menu-head .count {
    color: #666;
    font-size: 14px;
  }
  .admin-menu-head .add-btn {
    background: #003366;
    color: #fff;
    text-decoration: none;
    padding: 10px 18px;
    border-radius: 8px;
    font-weight: bold;
    font-size: 14px;
  }
  .admin-menu-head .add-btn:hover {
    background: #00509e;
  }
  .table-wrap {
    overflow-x: auto;
    border-radius: 12px;
    box-shadow: 0 4px 14px rgba(0, 0, 0, 0.08);
  }
  .menu-table {
    width: 100%;
    border-collapse: collapse;
    background: #fff;
    font-size: 14px;
  }
  .menu-table thead th {
    background: #003366;
    color: #fff;
    text-align: left;
    padding: 14px 16px;
    font-weight: 600;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    font-size: 12px;
  }
  .menu-table td {
    padding: 13px 16px;
    border-bottom: 1px solid #eef0f4;
    color: #444;
  }
  .menu-table tbody tr:nth-child(even) {
    background: #f8f9fb;
  }
  .menu-table tbody tr:hover {
    background: #eaf2fb;
  }
  .menu-table .no {
    width: 50px;
    color: #999;
  }
  .menu-table .name {
    font-weight: bold;
    color: #222;
  }
  .menu-table .price {
    text-align: right;
    white-space: nowrap;
    font-weight: bold;
    color: #003366;
  }
  .menu-table th.price {
    text-align: right;
    color: #fff;
  }
  .empty {
    text-align: center;
    color: #c0392b;
    padding: 30px;
  }
</style>

<div class="admin-menu">
  <div class="admin-menu-head">
    <div>
      <h1>Restaurant Menu</h1>
      <div class="count"><?php echo $count; ?> item dalam menu</div>
    </div>
    <a class="add-btn" href="menu_add.php">+ Tambah Menu</a>
  </div>

<?php if ($count > 0): ?>
  <div class="table-wrap">
    <table class="menu-table">
      <thead>
        <tr>
          <th class="no">#</th>
          <th>Menu</th>
          <th>Description</th>
          <th class="price">Price</th>
        </tr>
      </thead>
      <tbody>
      <?php
      $i = 1;
      while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
          echo '<tr>';
          echo '<td class="no">' . $i++ . '</td>';
          echo '<td class="name">' . htmlspecialchars($row['menu']) . '</td>';
          echo '<td>' . htmlspecialchars($row['description']) . '</td>';
          echo '<td class="price">RM ' . number_format((float)$row['price'], 2) . '</td>';
          echo '</tr>';
      }
      ?>
      </tbody>
    </table>
  </div>
<?php else: ?>
  <p class="empty">Tiada menu buat masa ini.</p>
<?php endif; ?>
</div>

<?php
if ($result) {
    mysqli_free_result($result);
}
mysqli_close($dbc); # Close the database connection.
include ('./includes/footer.html'); # footer.
?>
