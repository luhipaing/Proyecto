<?php
require 'db.php';
session_start();

// ⚠️ Verificamos que haya una sesión activa (por seguridad mínima)
if (!isset($_SESSION['user_id'])) {
    die('Acceso denegado: no hay sesión activa.');
}

$idUser   = $_POST['idUser']   ?? null;
$idUnidad = $_POST['idUnidad'] ?? null;

if (!$idUser || !$idUnidad) {
    header("Location: backoffice.php?error=Datos incompletos");
    exit;
}

try {
    // Liberar la unidad anterior (si el usuario ya tenía una)
    $stmt = $pdo->prepare("UPDATE unidades SET id_usuario = NULL, estado = 'disponible' WHERE id_usuario = ?");
    $stmt->execute([$idUser]);

    // Asignar nueva unidad al usuario seleccionado
    $stmt = $pdo->prepare("
        UPDATE unidades 
        SET id_usuario = ?, estado = 'asignada', fecha_asignacion = NOW() 
        WHERE idUnidad = ?
    ");
    $stmt->execute([$idUser, $idUnidad]);

    // Redirigir con mensaje de éxito
    header("Location: backoffice.php?mensaje=" . urlencode("Unidad asignada correctamente ✅"));
    exit;

} catch (PDOException $e) {
    // Redirigir con mensaje de error
    header("Location: backoffice.php?error=" . urlencode("Error al asignar unidad: " . $e->getMessage()));
    exit;
}
?>
