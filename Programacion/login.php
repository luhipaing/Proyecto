<?php
session_start();
require 'db.php';

$document_number = trim($_POST['idUser'] ?? '');
$password = $_POST['password'] ?? '';

if (!$document_number || !$password) { die('Faltan datos'); }

$stmt = $pdo->prepare('SELECT * FROM usuarios WHERE idUser = ?');
$stmt->execute([$document_number]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuario || !password_verify($password, $usuario['password_hash'])) {
    die('Cédula o contraseña inválida');
}

if ($usuario['status'] !== 'active') {
    die('Tu cuenta no está habilitada. Estado: ' . htmlspecialchars($usuario['status']));
}


$_SESSION['user_id'] = $usuario['idUser'];
$_SESSION['nombre'] = $usuario['nombre'];
$_SESSION['rol'] = $usuario['rol'];


header('Location: frontend.php');
exit;
?>
