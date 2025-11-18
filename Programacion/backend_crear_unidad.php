<?php
require 'db.php';

$numero = $_POST['numero_unidad'] ?? null;
$direccion = $_POST['direccion'] ?? null;
$descripcion = $_POST['descripcion'] ?? null;
$cuartos = $_POST['cuartos'] ?? null;
$banos = $_POST['banos'] ?? null;
$metros = $_POST['metros'] ?? null;
$estado = $_POST['estado'] ?? null;

if (!$numero || !$direccion || !$estado || $cuartos === null || $banos === null || $metros === null) {
    header("Location: backoffice.php?error=Datos incompletos");
    exit;
}

try {
    $sql = "INSERT INTO unidades 
            (numero_unidad, direccion, descripcion, cuartos, banos, metros, estado)
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$numero, $direccion, $descripcion, $cuartos, $banos, $metros, $estado]);

    header("Location: backoffice.php?mensaje=Unidad creada correctamente");
    exit;

} catch (PDOException $e) {
    header("Location: backoffice.php?error=" . urlencode("Error al crear unidad: " . $e->getMessage()));
    exit;
}
