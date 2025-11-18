<?php
session_start();
require 'db.php'; // este archivo debe definir $pdo (conexión PDO)

// Verificar sesión
if (!isset($_SESSION['user_id'])) {
    die("Acceso no autorizado");
}

// Verificar si se envió el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['archivo'])) {
    $id_usuario = $_SESSION['user_id'];
    $tipo = $_POST['tipo'] ?? '';
    $archivo = $_FILES['archivo'];

    // Validar tipo de archivo
    $permitidos = ['pdf', 'jpg', 'jpeg', 'png'];
    $ext = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, $permitidos)) {
        die("Tipo de archivo no permitido.");
    }

    // Crear carpeta si no existe
    $carpeta = 'uploads/comprobantes/';
    if (!file_exists($carpeta)) {
        mkdir($carpeta, 0777, true);
    }

    // Guardar archivo
    $nombreArchivo = uniqid("comp_") . "." . $ext;
    $rutaArchivo = $carpeta . $nombreArchivo;

    if (move_uploaded_file($archivo['tmp_name'], $rutaArchivo)) {
        // Insertar en la base de datos usando PDO
        $stmt = $pdo->prepare("INSERT INTO pagos (id_usuario, tipo, archivo, estado) 
                               VALUES (?, ?, ?, 'pendiente')");
        $stmt->execute([$id_usuario, $tipo, $rutaArchivo]);

        $_SESSION['mensaje'] = " Comprobante subido correctamente y en revisión.";
        $_SESSION['tipo'] = "exito";
    } else {
        $_SESSION['mensaje'] = " Error al subir el archivo.";
        $_SESSION['tipo'] = "error";
    }
}

// Volver al frontend
header("Location: frontend.php");
exit;
?>
