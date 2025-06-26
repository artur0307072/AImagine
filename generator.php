<?php
session_start();
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    header('Location: login.php');
    exit;
}

// Função para gerar imagem via API DeepAI
function generate_image_deepai($prompt, $api_key = null) {
    if (!$prompt || !is_string($prompt) || strlen($prompt) < 3) {
        return ['error' => 'Prompt inválido.'];
    }
    $apiKeyToUse = $api_key ?: '6a625395-a30d-483c-95d9-070c713a6ad4';

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://api.deepai.org/api/text2img");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, ['text' => $prompt]);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Api-Key: $apiKeyToUse"]);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curl_error = curl_error($ch);
    curl_close($ch);

    if ($curl_error) {
        return ['error' => "Erro de rede: $curl_error"];
    }

    $json = json_decode($response, true);
    if ($http_code !== 200 || !isset($json['output_url'])) {
        return [
            'error' => $json['err'] ?? 'Erro desconhecido na API',
            'raw' => $json,
            'http_code' => $http_code,
            'response' => $response
        ];
    }
    return ['images' => [$json['output_url']]];
}

$error = '';
$image_url = '';
$prompt = '';
$user_api_key = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $prompt = trim($_POST['prompt'] ?? '');
    $user_api_key = trim($_POST['api_key'] ?? '');

    if (!$prompt) {
        $error = 'Por favor, preencha o prompt.';
    } else {
        $result = generate_image_deepai($prompt, $user_api_key);

        if (isset($result['error'])) {
            $error = $result['error'];
            // Debug opcional:
            // echo '<pre>'; print_r($result); echo '</pre>';
        } else {
            $image_url = $result['images'][0] ?? '';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Almagine - Geração de Imagens (DeepAI)</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/style.css">
    <style>
        body {
            background: #f7f8fa;
            font-family: 'Segoe UI', Arial, sans-serif;
            margin: 0;
            padding: 0;
            min-height: 100vh;
        }
        .main-content {
            display: flex;
            justify-content: center;
            align-items: flex-start;
            gap: 0;
            max-width: 1200px;
            margin: 3rem auto 0 auto;
            padding: 2rem 1rem;
        }
        .form-col, .result-col {
            flex: 1 1 350px;
            max-width: 420px;
            min-width: 300px;
            display: flex;
            flex-direction: column;
            align-items: stretch;
        }
        .form-card, .result-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 12px 0 rgba(30,30,63,0.10);
            padding: 2rem 2rem 1.5rem 2rem;
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: stretch;
        }
        .form-label {
            display: block;
            margin-bottom: 0.3rem;
            font-weight: 500;
            color: #7033a5;
        }
        .input, textarea {
            width: 100%;
            padding: 0.7rem;
            margin-bottom: 1.2rem;
            border-radius: 6px;
            border: 1px solid #d1d1e0;
            font-size: 1rem;
            background: #f7f8fa;
            resize: none;
        }
        textarea {
            min-height: 60px;
            max-height: 180px;
        }
        .gen-btn {
            background: linear-gradient(90deg, #7afcff, #ff87e0);
            color: #fff;
            border: none;
            border-radius: 50px;
            padding: 0.8rem 2.5rem;
            font-size: 1.1rem;
            font-weight: bold;
            cursor: pointer;
            transition: opacity 0.2s, transform 0.2s;
            margin-top: 0.5rem;
        }
        .gen-btn:hover {
            opacity: 0.85;
            transform: translateY(-2px) scale(1.03);
            background: linear-gradient(90deg, #ff87e0, #7afcff);
        }
        .error-msg {
            background: #ffe3e3;
            color: #b71c1c;
            border-radius: 6px;
            padding: 0.7rem 1rem;
            margin-bottom: 1rem;
            text-align: center;
            width: 100%;
        }
        .divider-col {
            width: 100px;
            min-width: 100px;
            max-width: 100px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .result-card {
            align-items: center;
            justify-content: center;
            min-height: 350px;
        }
        .result-img {
            max-width: 100%;
            max-height: 350px;
            border-radius: 10px;
            margin-bottom: 1rem;
            box-shadow: 0 2px 12px 0 rgba(30,30,63,0.10);
            display: block;
            object-fit: contain;
            background: #f7f8fa;
        }
        .placeholder {
            color: #aaa;
            font-size: 1.1rem;
            text-align: center;
            margin-top: 2rem;
        }
        @media (max-width: 1100px) {
            .main-content {
                flex-direction: column;
                align-items: stretch;
                gap: 2rem;
                padding: 1rem 0.5rem;
            }
            .form-col, .result-col {
                max-width: 100%;
            }
            .divider-col {
                display: none;
            }
        }
        @media (max-width: 700px) {
            .main-content {
                padding: 0.5rem 0.2rem;
            }
            .form-card, .result-card {
                padding: 1rem 0.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="main-content">
        <!-- Card da esquerda: Formulário -->
        <div class="form-col">
            <div class="form-card">
                <?php if ($error): ?>
                    <div class="error-msg"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                <form method="post" autocomplete="off">
                    <label class="form-label" for="api_key">Chave da API DeepAI <span style="color:#aaa">(opcional)</span>:</label>
                    <input class="input" type="text" id="api_key" name="api_key" value="<?= htmlspecialchars($user_api_key) ?>" placeholder="Informe sua chave ou deixe em branco para usar a padrão">

                    <label class="form-label" for="prompt">Prompt <span style="color:#b71c1c">*</span>:</label>
                    <textarea id="prompt" name="prompt" placeholder="Ex: Um gato astronauta flutuando no espaço…" required><?= htmlspecialchars($prompt) ?></textarea>
                    
                    <button class="gen-btn" type="submit">Gerar Imagem</button>
                </form>
            </div>
        </div>
        <!-- Coluna do meio: Espaço vazio do mesmo tamanho da linha roxa -->
        <div class="divider-col" style="background:transparent;">
            <!-- Espaço vazio, sem linha -->
        </div>
        <!-- Card da direita: Resultado da imagem -->
        <div class="result-col">
            <div class="result-card">
                <?php if ($image_url): ?>
                    <img src="<?= htmlspecialchars($image_url) ?>" alt="Imagem gerada" class="result-img">
                    <div><a href="<?= htmlspecialchars($image_url) ?>" target="_blank" style="color:#7033a5;text-decoration:underline;">Abrir imagem em nova aba</a></div>
                <?php else: ?>
                    <div class="placeholder">Sua imagem aparecerá aqui</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>