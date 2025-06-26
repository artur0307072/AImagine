<?php
require_once 'db.php';

header('Content-Type: application/json');

$nome  = trim($_POST['nome'] ?? '');
$email = trim($_POST['email'] ?? '');
$senha = $_POST['senha'] ?? '';

if (!$nome || !$email || !$senha) {
    echo json_encode(['erro' => 'Preencha todos os campos!']);
    exit;
}

// Verifica se já existe usuário ou email
$stmt = $pdo->prepare("SELECT id FROM users WHERE name = ? OR email = ?");
$stmt->execute([$nome, $email]);
if ($stmt->fetch()) {
    echo json_encode(['erro' => 'Usuário ou e-mail já cadastrado!']);
    exit;
}

// Criptografa a senha
$senhaHash = password_hash($senha, PASSWORD_DEFAULT);

// Insere no banco
$stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
if ($stmt->execute([$nome, $email, $senhaHash])) {
    echo json_encode(['sucesso' => 'Cadastro realizado com sucesso!']);
} else {
    echo json_encode(['erro' => 'Erro ao cadastrar usuário.']);
}