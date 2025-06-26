<?php
require_once 'db.php';
session_start();

// --- ADMIN PERPÉTUO ---
$adminUser = 'edu';
$adminEmail = 'dudupacheconogali@gmail.com';
$adminPass = 'edu';
$adminHash = password_hash($adminPass, PASSWORD_DEFAULT);

// Verifica se já existe
$stmt = $pdo->prepare("SELECT id FROM users WHERE name = ? OR email = ?");
$stmt->execute([$adminUser, $adminEmail]);
if (!$stmt->fetch()) {
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
    $stmt->execute([$adminUser, $adminEmail, $adminHash]);
}
// --- FIM ADMIN PERPÉTUO ---

if (isset($_SESSION['logado']) && $_SESSION['logado'] === true) {
    header('Location: home.html');
    exit;
}

$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = $_POST['login'] ?? '';
    $senha = $_POST['senha'] ?? '';

    // Busca por email OU nome de usuário
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? OR name = ?");
    $stmt->execute([$login, $login]);
    $user = $stmt->fetch();

    if ($user && password_verify($senha, $user['password'])) {
        $_SESSION['logado'] = true;
        $_SESSION['usuario'] = $user['name'];
        header('Location: home.html');
        exit;
    } else {
        $erro = 'E-mail/Usuário ou senha inválidos!';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Almagine</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            background: #1e1e3f;
            font-family: 'Segoe UI', Arial, sans-serif;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-box {
            background: #23234a;
            border-radius: 12px;
            box-shadow: 0 4px 24px 0 rgba(30,30,63,0.18);
            padding: 2.5rem 2rem 2rem 2rem;
            min-width: 320px;
            max-width: 90vw;
            text-align: center;
        }
        .logo {
            font-size: 2rem;
            font-weight: bold;
            color: #fff;
            margin-bottom: 1.5rem;
            letter-spacing: 2px;
        }
        .input-group {
            margin-bottom: 1.2rem;
            text-align: left;
        }
        label {
            color: #7afcff;
            font-weight: 500;
            display: block;
            margin-bottom: 0.3rem;
        }
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 0.7rem;
            border-radius: 6px;
            border: 1px solid #d1d1e0;
            font-size: 1rem;
            background: #f7f8fa;
        }
        .login-btn {
            background: linear-gradient(90deg, #7afcff, #ff87e0);
            color: #fff;
            border: none;
            font-weight: bold;
            border-radius: 50px;
            cursor: pointer;
            padding: 0.8rem 2.5rem;
            font-size: 1.1rem;
            margin-top: 0.5rem;
            transition: opacity 0.2s, transform 0.2s;
        }
        .login-btn:hover {
            opacity: 0.85;
            transform: translateY(-2px) scale(1.03);
            background: linear-gradient(90deg, #ff87e0, #7afcff);
        }
        .error-message {
            background: #ffe3e3;
            color: #b71c1c;
            border-radius: 6px;
            padding: 0.7rem 1rem;
            margin-top: 1rem;
            text-align: center;
        }
        a {
            color: #7afcff;
            text-decoration: underline;
            font-size: 1rem;
        }
        /* Estilos para o popup de cadastro */
        #cadastroPopup {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(30, 30, 63, 0.85);
            align-items: center;
            justify-content: center;
        }
        #cadastroPopup .popup-content {
            background: #23234a;
            border-radius: 12px;
            padding: 2rem 1.5rem;
            min-width: 320px;
            max-width: 90vw;
            position: relative;
            color: #fff;
            text-align: center;
        }
        #cadastroPopup h2 {
            margin-top: 0;
        }
        #cadastroPopup input {
            width: 100%;
            margin-bottom: 1rem;
            padding: 0.7rem;
            border-radius: 6px;
            border: 1px solid #d1d1e0;
        }
        #cadastroPopup button[type="submit"] {
            width: 100%;
            background: linear-gradient(90deg, #7afcff, #ff87e0);
            color: #fff;
            border: none;
            font-weight: bold;
            border-radius: 50px;
            cursor: pointer;
            padding: 0.8rem 0;
            font-size: 1.1rem;
        }
        #cadastroPopup button#fecharCadastro {
            position: absolute;
            top: 10px;
            right: 18px;
            font-size: 1.5rem;
            color: #7afcff;
            background: none;
            border: none;
            cursor: pointer;
        }
        #cadastroMsg {
            margin-bottom: 1rem;
            font-size: 1rem;
        }
    </style>
</head>
<body>
    <div class="login-box">
        <div class="logo">Almagine</div>
        <form method="POST" action="">
            <h2 style="color:#fff;margin-bottom:1.5rem;">Login</h2>
            <div class="input-group">
                <label for="login">E-mail ou Usuário:</label>
                <input type="text" id="login" name="login" required>
            </div>
            <div class="input-group">
                <label for="senha">Senha:</label>
                <input type="password" id="senha" name="senha" required>
            </div>
            <button class="login-btn" type="submit">Entrar</button>
        </form>
        <!-- Botão para abrir o popup -->
        <div style="margin-top: 1rem;">
            <button id="abrirCadastro" style="background:none;border:none;color:#7afcff;text-decoration:underline;cursor:pointer;font-size:1rem;">Criar nova conta</button>
        </div>

        <!-- Popup de cadastro -->
        <div id="cadastroPopup">
            <div class="popup-content">
                <button id="fecharCadastro" title="Fechar">&times;</button>
                <h2>Cadastro</h2>
                <div id="cadastroMsg"></div>
                <form id="formCadastro" autocomplete="off">
                    <input type="text" name="nome" placeholder="Usuário" required>
                    <input type="email" name="email" placeholder="E-mail" required>
                    <input type="password" name="senha" placeholder="Senha" required>
                    <button type="submit">Cadastrar</button>
                </form>
            </div>
        </div>

        <?php if ($erro): ?>
            <div class="error-message"><?php echo $erro; ?></div>
        <?php endif; ?>
    </div>

    <script>
        // Abrir e fechar popup
        const abrirCadastro = document.getElementById('abrirCadastro');
        const fecharCadastro = document.getElementById('fecharCadastro');
        const cadastroPopup = document.getElementById('cadastroPopup');
        abrirCadastro.onclick = function() {
            cadastroPopup.style.display = 'flex';
        };
        fecharCadastro.onclick = function() {
            cadastroPopup.style.display = 'none';
            document.getElementById('cadastroMsg').innerHTML = '';
            document.getElementById('formCadastro').reset();
        };
        window.onclick = function(event) {
            if (event.target === cadastroPopup) {
                cadastroPopup.style.display = 'none';
                document.getElementById('cadastroMsg').innerHTML = '';
                document.getElementById('formCadastro').reset();
            }
        };

        // AJAX para cadastro
        document.getElementById('formCadastro').onsubmit = function(e) {
            e.preventDefault();
            const form = e.target;
            const dados = new FormData(form);

            fetch('cadastro.php', {
                method: 'POST',
                body: dados
            })
            .then(response => response.json())
            .then(data => {
                const msg = document.getElementById('cadastroMsg');
                if (data.sucesso) {
                    msg.style.color = '#4caf50';
                    msg.innerHTML = data.sucesso;
                    form.reset();
                } else {
                    msg.style.color = '#ff5252';
                    msg.innerHTML = data.erro;
                }
            })
            .catch(() => {
                const msg = document.getElementById('cadastroMsg');
                msg.style.color = '#ff5252';
                msg.innerHTML = 'Erro ao conectar ao servidor.';
            });
        };
    </script>
</body>
</html>