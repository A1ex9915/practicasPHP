<?php
declare(strict_types=1);
session_start();

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

if (!empty($_SESSION['user_id'])) {
  header("Location: panel.php");
  exit;
}

if (empty($_SESSION['csrf'])) {
  $_SESSION['csrf'] = bin2hex(random_bytes(32));
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Iniciar sesión</title>
  <style>
    body{font-family:system-ui;margin:0;background:#f4f7ff;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:16px;}
    .card{max-width:420px;width:100%;background:#fff;border:1px solid #e2e8f0;border-radius:16px;box-shadow:0 18px 40px rgba(2,8,23,.10);padding:20px;}
    h2{margin:0 0 8px;}
    p{margin:0 0 16px;color:#64748b;font-size:13px;}
    label{display:block;font-weight:700;font-size:13px;margin:10px 0 6px;}
    input{width:100%;padding:12px;border:1px solid #e2e8f0;border-radius:12px;outline:none;}
    input:focus{border-color:#6366f1;box-shadow:0 0 0 4px rgba(99,102,241,.16);}
    .btn{margin-top:14px;width:100%;padding:12px;border:0;border-radius:12px;background:#111827;color:#fff;font-weight:800;cursor:pointer;}
    .msg{padding:12px;border-radius:12px;margin-bottom:12px;border:1px solid transparent;font-size:14px}
    .ok{background:#ecfdf5;color:#065f46;border-color:#a7f3d0}
    .err{background:#fef2f2;color:#991b1b;border-color:#fecaca}
    a{color:#6366f1;text-decoration:none;font-weight:700}
  </style>
</head>
<body>
  <div class="card">
    <h2>Iniciar sesión</h2>
    <p>Accede con tu correo y contraseña.</p>

    <?php if ($flash): ?>
      <div class="msg <?= $flash['type'] === 'ok' ? 'ok' : 'err' ?>">
        <?= htmlspecialchars($flash['text'], ENT_QUOTES, 'UTF-8') ?>
      </div>
    <?php endif; ?>

    <form method="POST" action="login_process.php" novalidate>
      <label for="email">Correo</label>
      <input id="email" name="email" type="email" maxlength="120" required>

      <label for="password">Contraseña</label>
      <input id="password" name="password" type="password" minlength="8" maxlength="72" required>

      <input type="hidden" name="csrf" value="<?= $_SESSION['csrf'] ?>">

      <button class="btn" type="submit">Entrar</button>
      <p style="margin-top:12px;">¿No tienes cuenta? <a href="index.php">Registrarte</a></p>
    </form>
  </div>
</body>
</html>
