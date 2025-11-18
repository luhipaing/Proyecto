<?php
require 'db.php';
session_start();




if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: frontend.php");
    exit();
}

$idAviso = $_POST['idAviso'] ?? null;
$comentario = trim($_POST['comentario'] ?? '');
$idUser = $_SESSION['user_id'];

if (!$idAviso || $comentario === '') {
    header("Location: frontend.php?error=1");
    exit();
}

$stmt = $pdo->prepare("
    INSERT INTO comentarios (idAviso, idUser, comentario)
    VALUES (?, ?, ?)
");
$stmt->execute([$idAviso, $idUser, $comentario]);

header("Location: frontend.php#aviso-$idAviso");
exit();
