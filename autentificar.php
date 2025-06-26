<?php
require 'db.php';
session_start();

$usuario = $_POST['usuario'] ?? '';
$senha = md5($_POST['senha'] ?? '');

$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE usuario = ? AND senha = ?");
$stmt->execute([$usuario, $senha]);

if ($stmt->rowCount()) {
    $_SESSION['logado'] = true;
    header('Location: deepseek_form.php');
    exit;
} else {
    header('Location: login.html?erro=1');
    exit;
}
?>
