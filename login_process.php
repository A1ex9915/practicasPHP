<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/db.php';

function flash(string $type, string $text): void {
  $_SESSION['flash'] = ['type' => $type, 'text' => $text];
  header("Location: login.php");
  exit;
}

function log_server_error(string $message): void {
  $dir = __DIR__ . '/logs';
  if (!is_dir($dir)) return;
  file_put_contents($dir . '/app.log', "[".date('Y-m-d H:i:s')."] ".$message.PHP_EOL, FILE_APPEND);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  exit("Método no permitido.");
}

$csrf = $_POST['csrf'] ?? '';
if (empty($_SESSION['csrf']) || !hash_equals($_SESSION['csrf'], $csrf)) {
  flash('err', 'Solicitud inválida. Intenta de nuevo.');
}

$email = strtolower(trim((string)($_POST['email'] ?? '')));
$pass  = (string)($_POST['password'] ?? '');

if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
  flash('err', 'Correo o contraseña incorrectos.');
}
if (strlen($pass) < 8 || strlen($pass) > 72) {
  flash('err', 'Correo o contraseña incorrectos.');
}

try {
  $pdo = db();

  $stmt = $pdo->prepare("
    SELECT id, nombre, email, password_hash, tipo_usuario, estado
    FROM usuarios
    WHERE email = :email
    LIMIT 1
  ");
  $stmt->execute([':email' => $email]);
  $user = $stmt->fetch();

  if (!$user) {
    flash('err', 'Correo o contraseña incorrectos.');
  }

  if (($user['estado'] ?? 'activo') !== 'activo') {
    flash('err', 'Tu cuenta está bloqueada. Contacta al administrador.');
  }

  if (!password_verify($pass, $user['password_hash'])) {
    flash('err', 'Correo o contraseña incorrectos.');
  }

  session_regenerate_id(true);

  $_SESSION['user_id'] = (int)$user['id'];
  $_SESSION['nombre'] = $user['nombre'];
  $_SESSION['email'] = $user['email'];
  $_SESSION['tipo_usuario'] = $user['tipo_usuario'] ?? 'usuario';

  // opcional: guardar último login
  $upd = $pdo->prepare("UPDATE usuarios SET ultimo_login = NOW() WHERE id = :id");
  $upd->execute([':id' => (int)$user['id']]);

  header("Location: panel.php");
  exit;

} catch (Throwable $e) {
  log_server_error("Login error: " . $e->getMessage());
  error_log("Login error: " . $e->getMessage());
  flash('err', 'No se pudo iniciar sesión. Intenta más tarde.');
}
