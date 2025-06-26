<?php
require 'db.php';
session_start();

if (isset($_SESSION['logado']) && $_SESSION['logado'] === true) {
    header('Location: home.php');
    exit;
}

$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = $_POST['usuario'] ?? '';
    $senha = md5($_POST['senha'] ?? '');

    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE usuario = ? AND senha = ?");
    $stmt->execute([$usuario, $senha]);

    if ($stmt->rowCount()) {
        $_SESSION['logado'] = true;
        $_SESSION['usuario'] = $usuario;
        header('Location: home.php');
        exit;
    } else {
        $erro = 'Usuário ou senha inválidos!';
    }
}
?>