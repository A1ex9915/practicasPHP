<?php
// db.php
declare(strict_types=1);

function db(): PDO {
  $host = "127.0.0.1";
  $dbname = "registro_seguro";
  $user = "root";
  $pass = ""; 

  $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";

  try {
    $pdo = new PDO($dsn, $user, $pass, [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
      PDO::ATTR_EMULATE_PREPARES => false,
    ]);
    return $pdo;
  } catch (Throwable $e) {
   
    error_log("[DB] " . $e->getMessage());
    http_response_code(500);
    exit("Ocurrió un problema. Intenta más tarde.");
  }
}
