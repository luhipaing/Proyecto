<?php
require 'db.php';
session_start();

$idUnidad = $_POST['idUnidad'] ?? null;
$titulo = trim($_POST['titulo'] ?? '');
$descripcion = trim($_POST['descripcion'] ?? '');
$archivoRuta = null;

if (!$idUnidad || !$titulo || !$descripcion) {
    header("Location: backoffice.php?error=Datos incompletos");
    exit;
}

if (!empty($_FILES['archivo']['name'])) {
    $carpeta = "uploads/reportes/";
    if (!is_dir($carpeta)) mkdir($carpeta, 0777, true);

    $nombreArchivo = time() . "_" . basename($_FILES['archivo']['name']);
    $rutaDestino = $carpeta . $nombreArchivo;

    if (move_uploaded_file($_FILES['archivo']['tmp_name'], $rutaDestino)) {
        $archivoRuta = $rutaDestino;
    }
}

try {
    $stmt = $pdo->prepare("INSERT INTO reportes_unidad (idUnidad, titulo, descripcion, archivo) VALUES (?, ?, ?, ?)");
    $stmt->execute([$idUnidad, $titulo, $descripcion, $archivoRuta]);
    header("Location: backoffice.php?mensaje=Reporte guardado correctamente");
    exit;
} catch (PDOException $e) {
    header("Location: backoffice.php?error=" . urlencode("Error: " . $e->getMessage()));
    exit;
}
?>
