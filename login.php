<?php
// LOGIN: enter email + password -> verify credentials -> read users.role -> correct dashboard.
require __DIR__ . '/includes/auth.php';
if (current_user()) redirect('dashboard.php');

$error = '';
$email = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $st = $pdo->prepare('SELECT user_id, password, role FROM users WHERE email = ?');
    $st->execute([$email]);
    $row = $st->fetch();
    if ($row && password_verify($password, $row['password']) && in_array($row['role'], ['volunteer', 'organizer', 'super_admin'], true)) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = (int)$row['user_id'];
        redirect('dashboard.php');          // dashboard.php shows the dashboard for the user's role
    }
    $error = 'Invalid email or password.';
}
$flash = $_SESSION['flash'] ?? null; unset($_SESSION['flash']);
?><!DOCTYPE html>
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Login · Community Volunteer Management System</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link href="assets/css/style.css" rel="stylesheet"></head>
<body class="auth-body">
<div class="auth-wrap">
  <section class="auth-hero">
    <span class="brand-mark lg"><i class="bi bi-hand-thumbs-up-fill"></i></span>
    <h1>Welcome back</h1>
    <p>Log in to manage volunteers, activities, attendance and your own volunteer records.</p>
  </section>
  <section class="auth-panel">
    <div class="auth-card">
      <h2>Login</h2>
      <?php if ($flash): ?><div class="alert alert-<?= h($flash['type']) ?>"><?= h($flash['msg']) ?></div><?php endif; ?>
      <?php if ($error): ?><div class="alert alert-danger"><?= h($error) ?></div><?php endif; ?>
      <form method="post" autocomplete="on">
        <?= csrf_field() ?>
        <div class="mb-3"><label class="form-label">Email / Username</label>
          <input class="form-control" type="text" name="email" value="<?= h($email) ?>" required autofocus></div>
        <div class="mb-3"><label class="form-label">Password</label>
          <input class="form-control" type="password" name="password" required></div>
        <button class="btn btn-primary w-100" type="submit">Login</button>
      </form>
      <p class="text-center small mt-3 mb-0">No account yet? <a href="register.php">Register as a volunteer</a></p>
    </div>
  </section>
</div>
</body></html>
