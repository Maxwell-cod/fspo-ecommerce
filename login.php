<?php
require_once 'includes/config.php';
if (isLoggedIn()) redirect(SITE_URL . (isAdmin() ? '/admin/dashboard.php' : '/client/dashboard.php'));
$pageTitle = 'Login';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['password'] ?? '';

    if (!$email || !$pass) {
        $errors[] = 'Please fill in all fields.';
    } else {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM users WHERE email=?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($pass, $user['password'])) {
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['user_email']= $user['email'];
            setFlash('success', 'Welcome back, ' . $user['name'] . '!');
            $redirect = $_GET['redirect'] ?? '';
            if ($redirect && strpos($redirect, SITE_URL) === 0) redirect($redirect);
            redirect(SITE_URL . ($user['role'] === 'admin' ? '/admin/dashboard.php' : '/client/dashboard.php'));
        } else {
            $errors[] = 'Invalid email or password.';
        }
    }
}

include 'includes/header.php';
?>

<div class="form-card">
    <h2>Welcome Back</h2>
    <p>Sign in to your FSPO Ltd account</p>

    <?php if ($errors): ?>
    <div class="alert alert-danger"><?= implode('<br>', array_map('sanitize', $errors)) ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label>Email Address</label>
            <input type="email" name="email" class="form-control" placeholder="you@example.com" value="<?= sanitize($_POST['email'] ?? '') ?>" required autofocus>
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" class="form-control" placeholder="••••••••" required>
        </div>
        <button type="submit" class="btn btn-gold btn-block" style="margin-bottom:16px">Sign In →</button>
        <p style="text-align:center;font-size:14px;color:var(--text-muted)">
            Don't have an account? <a href="register.php" style="color:var(--gold)">Register here</a>
        </p>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
