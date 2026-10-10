<?php
require_once __DIR__ . '/includes/security.php'; # safe session settings

# How many tables the restaurant has (change this number if needed)
$total_tables = 12;

# Form submitted: remember the table and go to the menu
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $table_no = (int)($_POST['table_no'] ?? 0);

    if ($table_no >= 1 && $table_no <= $total_tables) {
        $_SESSION['table_no'] = $table_no;
        header('Location: cust_order.php?table_no=' . $table_no);
        exit;
    }
    $error = 'Please choose a table number between 1 and ' . $total_tables . '.';
}

$selected = (int)($_SESSION['table_no'] ?? 0);

$page_title = 'CHOOSE TABLE';
include('./includes/header_cust.html'); #header
?>
<style>
    .table-banner {
        position: relative;
        background: #2c3e50 url('image/resss.jpeg') center / cover no-repeat;
        color: #fff;
        text-align: center;
        padding: 56px 20px 96px;
        font-family: Arial, sans-serif;
    }
    .table-banner::before {
        content: "";
        position: absolute;
        inset: 0;
        background: linear-gradient(180deg, rgba(44, 62, 80, 0.55), rgba(44, 62, 80, 0.85));
    }
    .table-banner > * { position: relative; }
    .steps-bar {
        display: inline-flex;
        gap: 8px;
        margin-bottom: 18px;
        font-size: 0.85rem;
        flex-wrap: wrap;
        justify-content: center;
    }
    .steps-bar span {
        padding: 6px 14px;
        border-radius: 20px;
        background: rgba(255, 255, 255, 0.12);
        color: #dfe6ec;
    }
    .steps-bar span.on {
        background: #28a745;
        color: #fff;
        font-weight: bold;
    }
    .table-banner h1 { margin: 0 0 10px; font-size: 2.6rem; }
    .table-banner p  { margin: 0; font-size: 1.1rem; color: #ecf0f1; }

    .table-wrap {
        max-width: 900px;
        margin: -60px auto 0;
        padding: 0 20px 30px;
        position: relative;
        font-family: Arial, sans-serif;
        color: #333;
    }
    .table-card {
        background: #fff;
        border: 1px solid #e3e7ec;
        border-radius: 14px;
        box-shadow: 0 10px 24px rgba(0, 0, 0, 0.12);
        padding: 28px;
    }
    .table-card h2 {
        margin: 0 0 6px;
        color: #2c3e50;
        font-size: 1.4rem;
    }
    .table-card .hint {
        margin: 0 0 22px;
        color: #777;
    }
    .error {
        background: #fdecea;
        border: 1px solid #f5c6cb;
        color: #c0392b;
        border-radius: 10px;
        padding: 12px 16px;
        margin-bottom: 18px;
    }

    /* Table picker: each table is a radio button styled as a card */
    .table-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
        gap: 14px;
    }
    .table-option input {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }
    .table-option label {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 6px;
        height: 110px;
        border: 2px solid transparent;
        border-radius: 12px;
        cursor: pointer;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.08);
        transition: transform 0.2s, box-shadow 0.2s, border-color 0.2s, background 0.2s;
    }
    .table-option:nth-child(3n+1) label { background: #e3f2fd; }
    .table-option:nth-child(3n+2) label { background: #f1f8e9; }
    .table-option:nth-child(3n+3) label { background: #fff8e1; }
    .table-option label:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 18px rgba(0, 0, 0, 0.12);
    }
    .table-option .icon { font-size: 1.6rem; }
    .table-option .label {
        font-size: 0.8rem;
        color: #777;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    .table-option .num {
        font-size: 1.8rem;
        font-weight: bold;
        color: #2c3e50;
        line-height: 1;
    }
    .table-option input:checked + label {
        background: #2c3e50;
        border-color: #28a745;
        transform: translateY(-4px);
        box-shadow: 0 10px 20px rgba(44, 62, 80, 0.35);
    }
    .table-option input:checked + label .num,
    .table-option input:checked + label .label { color: #fff; }
    .table-option input:focus-visible + label { outline: 3px solid #28a745; outline-offset: 2px; }

    .actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 14px;
        flex-wrap: wrap;
        margin-top: 26px;
        padding-top: 22px;
        border-top: 1px solid #eef0f4;
    }
    .chosen {
        color: #555;
        font-size: 1rem;
    }
    .chosen strong {
        color: #28a745;
        font-size: 1.3rem;
    }
    .continue-btn {
        background: #28a745;
        color: #fff;
        border: none;
        border-radius: 8px;
        padding: 14px 34px;
        font-size: 1.05rem;
        font-weight: bold;
        cursor: pointer;
        box-shadow: 0 4px 12px rgba(40, 167, 69, 0.35);
        transition: background 0.2s, transform 0.2s;
    }
    .continue-btn:hover { background: #218838; transform: translateY(-2px); }
    .continue-btn:disabled {
        background: #b5bfc8;
        box-shadow: none;
        cursor: not-allowed;
        transform: none;
    }

    @media (max-width: 600px) {
        .table-banner h1 { font-size: 1.9rem; }
        .table-grid { grid-template-columns: repeat(3, 1fr); }
        .table-option label { height: 96px; }
        .actions { flex-direction: column; align-items: stretch; text-align: center; }
    }
</style>

<div class="table-banner">
    <div class="steps-bar">
        <span class="on">1. Table</span>
        <span>2. Menu</span>
        <span>3. Cart</span>
        <span>4. Pay</span>
    </div>
    <h1>Choose Your Table</h1>
    <p>Look for the number on your table, then tap it below.</p>
</div>

<div class="table-wrap">
    <form class="table-card" method="post" action="cust_table.php">
        <h2>Where are you sitting?</h2>
        <p class="hint">Your food will be served to this table.</p>

        <?php if (!empty($error)): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <div class="table-grid">
            <?php for ($i = 1; $i <= $total_tables; $i++): ?>
                <div class="table-option">
                    <input type="radio" name="table_no" id="t<?php echo $i; ?>" value="<?php echo $i; ?>" <?php echo $i === $selected ? 'checked' : ''; ?>>
                    <label for="t<?php echo $i; ?>">
                        <span class="icon">🍽️</span>
                        <span class="label">Table</span>
                        <span class="num"><?php echo $i; ?></span>
                    </label>
                </div>
            <?php endfor; ?>
        </div>

        <div class="actions">
            <div class="chosen" id="chosenText">
                <?php echo $selected ? 'Selected: <strong>Table ' . $selected . '</strong>' : 'No table selected yet'; ?>
            </div>
            <button type="submit" class="continue-btn" id="continueBtn" <?php echo $selected ? '' : 'disabled'; ?>>Continue to Menu →</button>
        </div>
    </form>
</div>

<script>
    // Update the "Selected" text and enable the button when a table is picked
    document.querySelectorAll('input[name="table_no"]').forEach(function (radio) {
        radio.addEventListener('change', function () {
            document.getElementById('chosenText').innerHTML = 'Selected: <strong>Table ' + this.value + '</strong>';
            document.getElementById('continueBtn').disabled = false;
        });
    });
</script>

<?php
include('./includes/footer.html'); #footer
?>
