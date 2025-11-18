<?php
require 'db.php';

if (isset($_GET['id'], $_GET['estado'])) {
    $idPago = (int) $_GET['id'];
    $estado = $_GET['estado'];

    if (!in_array($estado, ['aprobado', 'rechazado'])) {
        header("Location: backoffice.php?error=Estado no válido");
        exit;
    }

    try {
        $stmt = $pdo->prepare("UPDATE pagos SET estado = ? WHERE idPago = ?");
        $stmt->execute([$estado, $idPago]);
        header("Location: backoffice.php?mensaje=Estado actualizado correctamente");
        exit;
    } catch (PDOException $e) {
        header("Location: backoffice.php?error=" . urlencode("Error SQL: " . $e->getMessage()));
        exit;
    }
} else {
    header("Location: backoffice.php?error=Parámetros faltantes");
    exit;
}
?>
