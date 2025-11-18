<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$idUser = $_SESSION['user_id'];
$nombre = $_POST['nombre'] ?? '';
$email = $_POST['email'] ?? '';
$telefono = $_POST['telefono'] ?? '';
$fechNac = $_POST['fechNac'] ?? '';

try {
    // Actualización de datos básicos
    $stmt = $pdo->prepare("UPDATE usuarios 
                           SET nombre = ?, email = ?, telefono = ?, fechNac = ?
                           WHERE idUser = ?");
    $stmt->execute([$nombre, $email, $telefono, $fechNac, $idUser]);

    // --- Foto de perfil (si se sube) ---
    if (!empty($_FILES['foto_perfil']['name'])) {
        $targetDir = "uploads/";
        $fileName = basename($_FILES['foto_perfil']['name']);
        $targetFilePath = $targetDir . time() . "_" . $fileName;
        $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));

        // Validar tipo de archivo
        $allowTypes = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($fileType, $allowTypes)) {
            if (move_uploaded_file($_FILES['foto_perfil']['tmp_name'], $targetFilePath)) {
                $stmt = $pdo->prepare("UPDATE usuarios SET foto_perfil = ? WHERE idUser = ?");
                $stmt->execute([$targetFilePath, $idUser]);
            }
        }
    }

    $_SESSION['mensaje_estado'] = " Datos actualizados correctamente.";
    header('Location: perfil.php');
    exit;

} catch (PDOException $e) {
    $_SESSION['mensaje_estado'] = " Error al actualizar el perfil: " . $e->getMessage();
    header('Location: perfil.php');
    exit;
}
