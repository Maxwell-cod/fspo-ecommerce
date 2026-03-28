<?php
require_once '../includes/config.php';
requireLogin(); requireAdmin();
$pageTitle = 'Settings & API Credentials';
$db = getDB();

// Create settings table if not exists
$db->exec("CREATE TABLE IF NOT EXISTS settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)");

// Helper: get/set setting
function getSetting(PDO $db, string $key, string $default = ''): string {
    $s = $db->prepare("SELECT setting_value FROM settings WHERE setting_key=?");
    $s->execute([$key]);
    $r = $s->fetch();
    return $r ? $r['setting_value'] : $default;
}
function setSetting(PDO $db, string $key, string $value): void {
    $db->prepare("INSERT INTO settings (setting_key,setting_value) VALUES (?,?) ON DUPLICATE KEY UPDATE setting_value=?, updated_at=NOW()")->execute([$key,$value,$value]);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $group = $_POST['group'] ?? '';

    if ($group === 'site') {
        setSetting($db,'site_name',       trim($_POST['site_name']       ?? ''));
        setSetting($db,'site_phone',      trim($_POST['site_phone']      ?? ''));
        setSetting($db,'site_email',      trim($_POST['site_email']      ?? ''));
        setSetting($db,'site_address',    trim($_POST['site_address']    ?? ''));
        setSetting($db,'site_hours',      trim($_POST['site_hours']      ?? ''));
        setSetting($db,'delivery_fee',    trim($_POST['delivery_fee']    ?? '2000'));
        setFlash('success','Site settings saved!');
    }

    if ($group === 'mtn') {
        setSetting($db,'mtn_api_user',       trim($_POST['mtn_api_user']       ?? ''));
        setSetting($db,'mtn_api_key',        trim($_POST['mtn_api_key']        ?? ''));
        setSetting($db,'mtn_subscription_key',trim($_POST['mtn_subscription_key']?? ''));
        setSetting($db,'mtn_environment',    trim($_POST['mtn_environment']    ?? 'sandbox'));
        setSetting($db,'mtn_account_holder', trim($_POST['mtn_account_holder'] ?? ''));
        setSetting($db,'mtn_number',         trim($_POST['mtn_number']         ?? ''));
        setSetting($db,'mtn_enabled',        isset($_POST['mtn_enabled']) ? '1' : '0');
        setFlash('success','MTN MoMo API settings saved!');
    }

    if ($group === 'airtel') {
        setSetting($db,'airtel_client_id',     trim($_POST['airtel_client_id']     ?? ''));
        setSetting($db,'airtel_client_secret', trim($_POST['airtel_client_secret'] ?? ''));
        setSetting($db,'airtel_environment',   trim($_POST['airtel_environment']   ?? 'sandbox'));
        setSetting($db,'airtel_number',        trim($_POST['airtel_number']        ?? ''));
        setSetting($db,'airtel_enabled',       isset($_POST['airtel_enabled']) ? '1' : '0');
        setFlash('success','Airtel Money API settings saved!');
    }

    if ($group === 'banks') {
        foreach (['bk','equity','cogebanque','kcb','access','gt','ab'] as $bank) {
            setSetting($db,"bank_{$bank}_name",    trim($_POST["bank_{$bank}_name"]    ?? ''));
            setSetting($db,"bank_{$bank}_account", trim($_POST["bank_{$bank}_account"] ?? ''));
            setSetting($db,"bank_{$bank}_enabled", isset($_POST["bank_{$bank}_enabled"]) ? '1' : '0');
        }
        setFlash('success','Bank settings saved!');
    }

    redirect(SITE_URL.'/admin/settings.php');
}

include '../includes/header.php';
?>
<div class="dashboard-layout">
  <aside class="sidebar">
    <div class="sidebar-user"><div class="avatar" style="background:var(--danger)">A</div><h4><?= sanitize($_SESSION['user_name']) ?></h4><small style="color:var(--gold)">Administrator</small></div>
    <nav class="sidebar-nav">
      <a href="dashboard.php"><span class="nav-icon-dash">📊</span> Dashboard</a>
      <a href="orders.php"><span class="nav-icon-dash">📦</span> All Orders</a>
      <a href="products.php"><span class="nav-icon-dash">🛍</span> Products</a>
      <a href="categories.php"><span class="nav-icon-dash">📂</span> Categories</a>
      <a href="users.php"><span class="nav-icon-dash">👥</span> Clients</a>
      <a href="messages.php"><span class="nav-icon-dash">✉</span> Messages</a>
      <a href="settings.php" class="active"><span class="nav-icon-dash">⚙</span> Settings & API</a>
      <a href="../index.php"><span class="nav-icon-dash">🌐</span> View Website</a>
      <a href="../logout.php"><span class="nav-icon-dash">🚪</span> Logout</a>
    </nav>
  </aside>

  <div class="dash-content">
    <div class="dash-header"><h1>⚙ Settings & Payment API</h1></div>

    <!-- SITE SETTINGS -->
    <div class="admin-form-card">
      <h3>🌐 Site Settings</h3>
      <form method="POST">
        <input type="hidden" name="group" value="site">
        <div class="form-grid">
          <div class="form-group"><label>Site Name</label><input type="text" name="site_name" class="form-control" value="<?= sanitize(getSetting($db,'site_name','FSPO Ltd')) ?>"></div>
          <div class="form-group"><label>Contact Phone</label><input type="text" name="site_phone" class="form-control" value="<?= sanitize(getSetting($db,'site_phone','+250 785 723 677')) ?>"></div>
          <div class="form-group"><label>Contact Email</label><input type="email" name="site_email" class="form-control" value="<?= sanitize(getSetting($db,'site_email','info@fspoltd.rw')) ?>"></div>
          <div class="form-group"><label>Delivery Fee (Rwf)</label><input type="number" name="delivery_fee" class="form-control" value="<?= sanitize(getSetting($db,'delivery_fee','2000')) ?>"></div>
          <div class="form-group" style="grid-column:1/-1"><label>Address</label><input type="text" name="site_address" class="form-control" value="<?= sanitize(getSetting($db,'site_address','Kigali-Gakinjiro, Rwanda')) ?>"></div>
          <div class="form-group" style="grid-column:1/-1"><label>Business Hours</label><input type="text" name="site_hours" class="form-control" value="<?= sanitize(getSetting($db,'site_hours','Mon – Sat / 07:00 AM – 08:00 PM')) ?>"></div>
        </div>
        <button type="submit" class="btn btn-gold">Save Site Settings</button>
      </form>
    </div>

    <!-- MTN MOMO API -->
    <div class="admin-form-card">
      <h3>📱 MTN Mobile Money API</h3>
      <div class="alert alert-info" style="margin-bottom:20px;font-size:13px">
        <strong>How to get credentials:</strong> Register at <a href="https://momodeveloper.mtn.com" target="_blank" style="color:var(--gold-light)">momodeveloper.mtn.com</a> → Create an app → Subscribe to Collections API → Get your API User, API Key and Subscription Key. For production, contact MTN Rwanda Business.
      </div>
      <form method="POST">
        <input type="hidden" name="group" value="mtn">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:18px">
          <input type="checkbox" name="mtn_enabled" id="mtn_en" value="1" <?= getSetting($db,'mtn_enabled')=='1'?'checked':'' ?> style="accent-color:var(--gold);width:17px;height:17px">
          <label for="mtn_en" style="font-size:14px;font-weight:600;cursor:pointer">Enable MTN MoMo payment</label>
        </div>
        <div class="form-grid">
          <div class="form-group"><label>API User (UUID)</label><input type="text" name="mtn_api_user" class="form-control" placeholder="xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx" value="<?= sanitize(getSetting($db,'mtn_api_user')) ?>"></div>
          <div class="form-group"><label>API Key</label><input type="text" name="mtn_api_key" class="form-control" placeholder="Your API Key" value="<?= sanitize(getSetting($db,'mtn_api_key')) ?>"></div>
          <div class="form-group"><label>Subscription Key (Ocp-Apim-Subscription-Key)</label><input type="text" name="mtn_subscription_key" class="form-control" placeholder="Primary or Secondary key" value="<?= sanitize(getSetting($db,'mtn_subscription_key')) ?>"></div>
          <div class="form-group"><label>Environment</label>
            <select name="mtn_environment" class="form-control">
              <option value="sandbox"    <?= getSetting($db,'mtn_environment')=='sandbox'?'selected':'' ?>>Sandbox (Testing)</option>
              <option value="production" <?= getSetting($db,'mtn_environment')=='production'?'selected':'' ?>>Production (Live)</option>
            </select>
          </div>
          <div class="form-group"><label>Account Holder Name (displayed to customer)</label><input type="text" name="mtn_account_holder" class="form-control" placeholder="FSPO Ltd" value="<?= sanitize(getSetting($db,'mtn_account_holder','FSPO Ltd')) ?>"></div>
          <div class="form-group"><label>MTN Number for Manual Payments</label><input type="text" name="mtn_number" class="form-control" placeholder="+250785723677" value="<?= sanitize(getSetting($db,'mtn_number','+250785723677')) ?>"></div>
        </div>
        <button type="submit" class="btn btn-gold">Save MTN Settings</button>
      </form>
    </div>

    <!-- AIRTEL MONEY API -->
    <div class="admin-form-card">
      <h3>📲 Airtel Money API</h3>
      <div class="alert alert-info" style="margin-bottom:20px;font-size:13px">
        <strong>How to get credentials:</strong> Visit <a href="https://developers.airtel.africa" target="_blank" style="color:var(--gold-light)">developers.airtel.africa</a> → Register → Create an app → Get Client ID and Client Secret. For Rwanda production, contact Airtel Rwanda.
      </div>
      <form method="POST">
        <input type="hidden" name="group" value="airtel">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:18px">
          <input type="checkbox" name="airtel_enabled" id="airtel_en" value="1" <?= getSetting($db,'airtel_enabled')=='1'?'checked':'' ?> style="accent-color:var(--gold);width:17px;height:17px">
          <label for="airtel_en" style="font-size:14px;font-weight:600;cursor:pointer">Enable Airtel Money payment</label>
        </div>
        <div class="form-grid">
          <div class="form-group"><label>Client ID</label><input type="text" name="airtel_client_id" class="form-control" placeholder="Your Client ID" value="<?= sanitize(getSetting($db,'airtel_client_id')) ?>"></div>
          <div class="form-group"><label>Client Secret</label><input type="text" name="airtel_client_secret" class="form-control" placeholder="Your Client Secret" value="<?= sanitize(getSetting($db,'airtel_client_secret')) ?>"></div>
          <div class="form-group"><label>Environment</label>
            <select name="airtel_environment" class="form-control">
              <option value="sandbox"    <?= getSetting($db,'airtel_environment')=='sandbox'?'selected':'' ?>>Sandbox (Testing)</option>
              <option value="production" <?= getSetting($db,'airtel_environment')=='production'?'selected':'' ?>>Production (Live)</option>
            </select>
          </div>
          <div class="form-group"><label>Airtel Number for Manual Payments</label><input type="text" name="airtel_number" class="form-control" placeholder="+250785723677" value="<?= sanitize(getSetting($db,'airtel_number','+250785723677')) ?>"></div>
        </div>
        <button type="submit" class="btn btn-gold">Save Airtel Settings</button>
      </form>
    </div>

    <!-- BANK SETTINGS -->
    <div class="admin-form-card">
      <h3>🏦 Bank Account Details</h3>
      <p style="color:var(--text-muted);font-size:13px;margin-bottom:20px">These details are shown to clients when they select bank transfer at checkout.</p>
      <form method="POST">
        <input type="hidden" name="group" value="banks">
        <?php
        $bankDefs = [
            'bk'         => 'Bank of Kigali (BK)',
            'equity'     => 'Equity Bank Rwanda',
            'cogebanque' => 'Cogebanque',
            'kcb'        => 'KCB Bank Rwanda',
            'access'     => 'Access Bank Rwanda',
            'gt'         => 'GT Bank Rwanda',
            'ab'         => 'AB Bank Rwanda',
        ];
        foreach ($bankDefs as $key => $label): ?>
        <div style="background:var(--dark3);border:1px solid var(--border);border-radius:8px;padding:16px;margin-bottom:14px">
          <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px">
            <strong style="font-size:14px"><?= $label ?></strong>
            <label style="display:flex;align-items:center;gap:8px;cursor:pointer">
              <input type="checkbox" name="bank_<?= $key ?>_enabled" value="1" <?= getSetting($db,"bank_{$key}_enabled")=='1'?'checked':'' ?> style="accent-color:var(--gold)">
              <span style="font-size:12px">Show to customers</span>
            </label>
          </div>
          <div class="form-grid" style="grid-template-columns:1fr 1fr">
            <div class="form-group" style="margin:0">
              <label style="font-size:12px">Display Name</label>
              <input type="text" name="bank_<?= $key ?>_name" class="form-control" value="<?= sanitize(getSetting($db,"bank_{$key}_name",$label)) ?>">
            </div>
            <div class="form-group" style="margin:0">
              <label style="font-size:12px">Account Number / IBAN</label>
              <input type="text" name="bank_<?= $key ?>_account" class="form-control" placeholder="e.g. 00040-06763401-51" value="<?= sanitize(getSetting($db,"bank_{$key}_account")) ?>">
            </div>
          </div>
        </div>
        <?php endforeach; ?>
        <button type="submit" class="btn btn-gold">Save Bank Settings</button>
      </form>
    </div>

  </div>
</div>
<?php include '../includes/footer.php'; ?>
