<?php
require 'db.php';
session_start();

$id = $_GET['id'] ?? null;

if (!$id) {
    header("Location: backoffice.php?error=ID inválido");
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT archivo FROM reportes_unidad WHERE idReporte=?");
    $stmt->execute([$id]);
    $file = $stmt->fetchColumn();
    if ($file && file_exists($file)) unlink($file);

    $pdo->prepare("DELETE FROM reportes_unidad WHERE idReporte=?")->execute([$id]);
    header("Location: backoffice.php?mensaje=Reporte eliminado");
    exit;
} catch (PDOException $e) {
    header("Location: backoffice.php?error=" . urlencode("Error al eliminar: " . $e->getMessage()));
    exit;
}
?>
