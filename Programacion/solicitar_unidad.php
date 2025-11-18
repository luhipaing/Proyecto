<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || empty($_POST['id_unidad'])) {
    header('Location: frontend.php');
    exit;
}

$idUsuario = $_SESSION['user_id'];
$id_unidad = $_POST['id_unidad'];

$stmt = $pdo->prepare("INSERT INTO solicitudes_unidad (id_usuario, id_unidad) VALUES (?, ?)");
$stmt->execute([$idUsuario, $id_unidad]);

header('Location: frontend.php');
exit;
?>
