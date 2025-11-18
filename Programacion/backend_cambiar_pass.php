<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$idUser = $_SESSION['user_id'];
$pass_actual = $_POST['pass_actual'] ?? '';
$pass_nueva = $_POST['pass_nueva'] ?? '';
$pass_confirmar = $_POST['pass_confirmar'] ?? '';

if (empty($pass_actual) || empty($pass_nueva) || empty($pass_confirmar)) {
    $_SESSION['mensaje_estado'] = " Todos los campos son obligatorios.";
    header('Location: perfil.php');
    exit;
}

if ($pass_nueva !== $pass_confirmar) {
    $_SESSION['mensaje_estado'] = " Las contraseñas nuevas no coinciden.";
    header('Location: perfil.php');
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT password_hash FROM usuarios WHERE idUser = ?");
    $stmt->execute([$idUser]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        $_SESSION['mensaje_estado'] = " Usuario no encontrado.";
        header('Location: perfil.php');
        exit;
    }

    if (!password_verify($pass_actual, $user['password_hash'])) {
        $_SESSION['mensaje_estado'] = " La contraseña actual es incorrecta.";
        header('Location: perfil.php');
        exit;
    }

    $hash = password_hash($pass_nueva, PASSWORD_DEFAULT);
    $update = $pdo->prepare("UPDATE usuarios SET password_hash = ? WHERE idUser = ?");
    $update->execute([$hash, $idUser]);

    $_SESSION['mensaje_estado'] = " Contraseña actualizada correctamente.";
    header('Location: perfil.php');
    exit;

} catch (PDOException $e) {
    $_SESSION['mensaje_estado'] = " Error al cambiar la contraseña: " . $e->getMessage();
    header('Location: perfil.php');
    exit;
}
?>
