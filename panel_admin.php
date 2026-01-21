<?php
declare(strict_types=1);
session_start();

if (empty($_SESSION['user_id']) || ($_SESSION['tipo_usuario'] ?? '') !== 'admin') {
  http_response_code(403);
  exit("Acceso no autorizado.");
}
?>
<h2>Panel Admin</h2>
<p>Bienvenido, <?= htmlspecialchars($_SESSION['nombre']) ?></p>
<p>Rol: admin</p>
<a href="logout.php">Cerrar sesión</a>
