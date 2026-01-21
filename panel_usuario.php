  <?php
declare(strict_types=1);
session_start();

if (empty($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}
?>
<h2>Panel Usuario</h2>
<p>Bienvenido, <?= htmlspecialchars($_SESSION['nombre']) ?></p>
<p>Rol: usuario</p>
<a href="logout.php">Cerrar sesión</a>
