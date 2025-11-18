<?php
require 'db.php';
session_start();

// Solo admin puede publicar
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    die("Acceso denegado");
}

if (!empty($_POST['titulo']) && !empty($_POST['contenido'])) {

    $idUser = $_SESSION['user_id']; // ← ID del admin que está publicando

    try {
        $stmt = $pdo->prepare("
            INSERT INTO avisos (titulo, contenido,fecha, idUser) 
            VALUES (?, ?, NOW(), ?)
        ");
        $stmt->execute([
            $_POST['titulo'],
            $_POST['contenido'],
            $idUser
        ]);

        header("Location: backoffice.php?mensaje=Aviso+publicado");
        exit;

    } catch (PDOException $e) {
        echo "Error al guardar aviso: " . $e->getMessage();
    }
} else {
    echo "Completa todos los campos.";
}
?>
