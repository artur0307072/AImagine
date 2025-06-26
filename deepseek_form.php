<?php
session_start();
if (!isset($_SESSION['logado'])) {
    header('Location: login.php');
    exit;
}

$error = '';
$result = '';
$prompt = trim($_POST['prompt'] ?? '');

// Chave fixa para exemplo, pode ser removida se quiser campo para o usuário
$api_key = '6a625395-a30d-483c-95d9-070c713a6ad4';

require_once 'image_generator.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = generate_image($prompt);
    if (isset($result['error'])) {
        $error = $result['error'];
    } else {
        $image_urls = $result['images'];
        // Exiba as imagens no HTML
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Resultado - Gerador de Imagens IA</title>
    <link rel="stylesheet" href="assets/style.css">
    <style>
        body {
            background: linear-gradient(135deg, #1e1e3f 0%, #7033a5 100%);
            font-family: 'Roboto', Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .header {
            width: 100%;
            text-align: center;
            margin-bottom: 2rem;
        }
        .header-title {
            font-size: 2.5rem;
            font-weight: bold;
            color: #fff;
            letter-spacing: 1px;
        }
        .header-sub {
            font-size: 1.25rem;
            color: #fff;
            opacity: 0.85;
            margin-top: 15px;
            margin-bottom: 0;
        }
        .main-container {
            max-width: 900px;
            margin: 2rem auto;
            padding: 2rem 1rem;
            background: rgba(30,30,63,0.85);
            border-radius: 18px;
            box-shadow: 0 8px 32px 0 rgba(30,30,63,0.18);
            display: flex;
            gap: 2rem;
            flex-wrap: wrap;
        }
        .result-section {
            flex: 1 1 350px;
            min-width: 320px;
            margin: auto;
        }
        .result-card {
            background: rgba(255,255,255,0.07);
            border-radius: 12px;
            padding: 2rem 1.5rem;
            min-height: 340px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
        }
        .error-msg {
            color: #e53935;
            background: #fff0f0;
            border: 1px solid #ffcdd2;
            border-radius: 6px;
            padding: 0.7rem 1rem;
            margin-bottom: 1rem;
            font-size: 1rem;
            text-align: center;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            justify-content: center;
        }
        .error-msg::before {
            content: "⚠️";
            font-size: 1.2rem;
        }
        .img-preview {
            max-width: 100%;
            border-radius: 10px;
            box-shadow: 0 4px 24px 0 rgba(122,252,255,0.10);
            animation: fadeIn 1s;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @media (max-width: 1200px) {
            .main-container {
                flex-direction: column;
                gap: 1.5rem;
            }
        }
        @media (max-width: 600px) {
            .header-title { font-size: 1.5rem; }
            .header-sub { font-size: 1rem; }
            .main-container { padding: 1rem 0.2rem; }
            .result-card { padding: 1rem 0.5rem; }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-title">Resultado da Geração</div>
    </div>
    <div class="main-container">
        <div class="result-section" style="margin:auto;">
            <div class="result-card">
                <?php if ($error): ?>
                    <div class="error-msg"><?= htmlspecialchars($error) ?></div>
                <?php elseif (isset($image_urls)): ?>
                    <?php foreach ($image_urls as $image_url): ?>
                        <img src="<?= htmlspecialchars($image_url) ?>" alt="Imagem gerada" class="img-preview">
                        <div style="text-align:center;margin-top:1rem;">
                            <a href="<?= htmlspecialchars($image_url) ?>" target="_blank" style="color:#7afcff;text-decoration:underline;">Ver imagem em tamanho real</a>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
                <div style="margin-top:2rem;">
                    <a href="deepseek_form.html" class="cta-btn" style="padding:0.7rem 2rem;font-size:1rem;">Nova Imagem</a>
                    <a href="login.php" class="cta-btn" style="padding:0.7rem 2rem;font-size:1rem;">Sair</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>