<?php
declare(strict_types=1);
session_start();

if (empty($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}

$rol = $_SESSION['tipo_usuario'] ?? 'usuario';

if ($rol === 'admin') {
  header("Location: panel_admin.php");
  exit;
}

header("Location: panel_usuario.php");
exit;
