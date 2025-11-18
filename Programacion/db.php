<?php
$host = '192.168.1.102'; // IP del servidor MySQL
$db   = 'cooperativas';
$user = 'ldiaz';
$pass = '1234';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Conexión exitosa al servidor MySQL remoto";
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>
