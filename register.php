<?php
// register.php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/db.php';

// Log opcional (carpeta logs debe existir)
function log_server_error(string $message): void {
  $dir = __DIR__ . '/logs';
  if (!is_dir($dir)) return;
  $line = "[" . date('Y-m-d H:i:s') . "] " . $message . PHP_EOL;
  file_put_contents($dir . '/app.log', $line, FILE_APPEND);
}

function flash(string $type, string $text): void {
  $_SESSION['flash'] = ['type' => $type, 'text' => $text];
  header("Location: index.php");
  exit;
}

// 1) Método correcto
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  exit("Método no permitido.");
}

// 2) CSRF
$csrf = $_POST['csrf'] ?? '';
if (empty($_SESSION['csrf']) || !hash_equals($_SESSION['csrf'], $csrf)) {
  flash('err', 'Solicitud inválida. Intenta de nuevo.');
}

// 3) Leer y sanitizar (sanitizar != validar)
$nombre = trim((string)($_POST['nombre'] ?? ''));
$email  = trim((string)($_POST['email'] ?? ''));
$pass   = (string)($_POST['password'] ?? '');
$confirm= (string)($_POST['confirm'] ?? '');
$tel    = trim((string)($_POST['telefono'] ?? ''));

// Sanitización básica
$nombre = preg_replace('/\s+/', ' ', $nombre); // normaliza espacios
$email  = strtolower($email);

// 4) Validaciones backend (tipo, longitud, formato)
$errors = [];

if ($nombre === '' || mb_strlen($nombre) < 2 || mb_strlen($nombre) > 80) {
  $errors[] = "Nombre inválido.";
}

// filter_var valida formato; longitud también se controla
if ($email === '' || mb_strlen($email) > 120 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
  $errors[] = "Correo inválido.";
}

// Password: longitud + reglas ejemplo (letra y número)
if (strlen($pass) < 8 || strlen($pass) > 72 || !preg_match('/[A-Za-z]/', $pass) || !preg_match('/[0-9]/', $pass)) {
  $errors[] = "Contraseña inválida.";
}
if ($confirm !== $pass) {
  $errors[] = "Las contraseñas no coinciden.";
}

// Teléfono: permitir +, espacios, guiones, pero guardar normalizado
$telDigits = preg_replace('/\D+/', '', $tel);
if ($telDigits === '' || strlen($telDigits) < 10 || strlen($telDigits) > 15) {
  $errors[] = "Teléfono inválido.";
}

// Si hay errores, mensaje genérico + claro
if (!empty($errors)) {
  flash('err', 'Verifica tus datos e intenta nuevamente.');
}

// 5) Hash seguro (NO texto plano)
$passwordHash = password_hash($pass, PASSWORD_BCRYPT);

// 6) Evitar SQL Injection: consultas preparadas
try {
  $pdo = db();

  // Verificar si el email ya existe (prepared statement)
  $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = :email LIMIT 1");
  $stmt->execute([':email' => $email]);
  if ($stmt->fetch()) {
    flash('err', 'Ese correo ya está registrado. Usa otro o inicia sesión.');
  }

  // Insert
  $insert = $pdo->prepare("
    INSERT INTO usuarios (nombre, email, telefono, password_hash)
    VALUES (:nombre, :email, :telefono, :password_hash)
  ");

  $insert->execute([
    ':nombre' => $nombre,
    ':email' => $email,
    ':telefono' => $telDigits,
    ':password_hash' => $passwordHash
  ]);

  flash('ok', 'Registro exitoso. Tu cuenta fue creada correctamente.');

} catch (Throwable $e) {
  // No mostrar error técnico al usuario
  log_server_error("Register error: " . $e->getMessage());
  error_log("Register error: " . $e->getMessage());

  flash('err', 'No se pudo completar el registro. Intenta más tarde.');
}
