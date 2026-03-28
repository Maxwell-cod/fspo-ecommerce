<?php
require_once 'includes/config.php';
if (isLoggedIn()) redirect(SITE_URL . '/client/dashboard.php');
$pageTitle = 'Register';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['name']     ?? '');
    $email   = trim($_POST['email']    ?? '');
    $phone   = trim($_POST['phone']    ?? '');
    $pass    = $_POST['password']      ?? '';
    $confirm = $_POST['confirm_pass']  ?? '';
    $address = trim($_POST['address']  ?? '');

    if (!$name || !$email || !$pass) $errors[] = 'Name, email and password are required.';
    if ($pass !== $confirm) $errors[] = 'Passwords do not match.';
    if (strlen($pass) < 6)  $errors[] = 'Password must be at least 6 characters.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Invalid email address.';

    if (empty($errors)) {
        $db = getDB();
        $check = $db->prepare("SELECT id FROM users WHERE email=?");
        $check->execute([$email]);
        if ($check->fetch()) {
            $errors[] = 'An account with this email already exists. <a href="login.php" style="color:var(--gold)">Login?</a>';
        } else {
            $hash = password_hash($pass, PASSWORD_DEFAULT);
            $db->prepare("INSERT INTO users (name,email,phone,password,role,address) VALUES (?,?,?,?,'client',?)")
               ->execute([$name,$email,$phone,$hash,$address]);
            $uid = $db->lastInsertId();
            $_SESSION['user_id']    = $uid;
            $_SESSION['user_name']  = $name;
            $_SESSION['user_role']  = 'client';
            $_SESSION['user_email'] = $email;
            setFlash('success', 'Account created! Welcome to FSPO Ltd, ' . $name . '!');
            redirect(SITE_URL . '/client/dashboard.php');
        }
    }
}

include 'includes/header.php';
?>

<div class="form-card" style="max-width:520px">
    <h2>Create Account</h2>
    <p>Join FSPO Ltd and start shopping</p>

    <?php if ($errors): ?>
    <div class="alert alert-danger"><?= implode('<br>', $errors) ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-grid">
            <div class="form-group">
                <label>Full Name *</label>
                <input type="text" name="name" class="form-control" placeholder="Jean Claude" value="<?= sanitize($_POST['name'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label>Phone Number</label>
                <input type="tel" name="phone" class="form-control" placeholder="+250 7XX XXX XXX" value="<?= sanitize($_POST['phone'] ?? '') ?>">
            </div>
        </div>
        <div class="form-group">
            <label>Email Address *</label>
            <input type="email" name="email" class="form-control" placeholder="you@gmail.com" value="<?= sanitize($_POST['email'] ?? '') ?>" required>
        </div>
        <div class="form-group">
            <label>Delivery Address</label>
            <input type="text" name="address" class="form-control" placeholder="Kigali, Kimironko..." value="<?= sanitize($_POST['address'] ?? '') ?>">
        </div>
        <div class="form-grid">
            <div class="form-group">
                <label>Password *</label>
                <input type="password" name="password" class="form-control" placeholder="Min. 6 characters" required>
            </div>
            <div class="form-group">
                <label>Confirm Password *</label>
                <input type="password" name="confirm_pass" class="form-control" placeholder="Repeat password" required>
            </div>
        </div>
        <button type="submit" class="btn btn-gold btn-block" style="margin-bottom:16px">Create Account →</button>
        <p style="text-align:center;font-size:14px;color:var(--text-muted)">
            Already have an account? <a href="login.php" style="color:var(--gold)">Sign in</a>
        </p>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
