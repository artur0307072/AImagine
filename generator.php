<?php
$error = '';
$result = '';
$api_key = '';
$prompt = '';
$examples = [
    "Paisagem futurista com prédios de cristal",
    "Um gato astronauta flutuando no espaço com estrelas coloridas",
    "Cidade submersa iluminada por águas-vivas",
    "Robô pintando um quadro em Paris"
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $api_key = trim($_POST['api_key'] ?? '');
    $prompt = trim($_POST['prompt'] ?? '');

    if (!$api_key) {
        $error = "Por favor, insira sua chave da API DeepAI.";
    } elseif (!$prompt) {
        $error = "Por favor, descreva a imagem que deseja gerar.";
    } else {
        $ch = curl_init('https://api.deepseek.com/v1/text2img');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer $api_key",
            "Content-Type: application/json"
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
            "prompt" => $prompt
        ]));
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            $error = "Erro ao conectar à API: " . curl_error($ch);
        } else {
            $data = json_decode($response, true);
            if (isset($data['data'][0]['url'])) {
                $result = $data['data'][0]['url'];
            } else {
                $error = "Erro ao gerar imagem. Resposta: " . htmlspecialchars($response);
            }
        }
        curl_close($ch);
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Gerador de Imagens IA</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/style.css">
    <style>
        body {
            background: linear-gradient(135deg, #1e1e3f 0%, #7033a5 100%);
            font-family: 'Roboto', Arial, sans-serif;
            margin: 0;
            padding: 0;
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
        .form-section, .result-section {
            flex: 1 1 350px;
            min-width: 320px;
        }
        .form-card {
            background: rgba(255,255,255,0.07);
            border-radius: 12px;
            padding: 2rem 1.5rem;
            box-shadow: 0 4px 16px 0 rgba(30,30,63,0.10);
        }
        .form-label {
            font-weight: 500;
            display: block;
            margin-bottom: 0.3rem;
            margin-top: 1rem;
            color: #fff;
        }
        .input, textarea {
            width: 100%;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #d1d1e0;
            font-size: 1rem;
            margin-bottom: 10px;
            background: #F9F9F9;
            font-family: inherit;
            transition: border-color 0.2s;
            box-sizing: border-box;
        }
        .input:focus, textarea:focus {
            border-color: #7afcff;
            outline: none;
        }
        textarea {
            min-height: 100px;
            resize: vertical;
        }
        .examples-list {
            margin: 1rem 0 1.5rem 0;
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }
        .example-btn {
            background: #f9f9f9;
            color: #7033a5;
            border: 1px solid #e0e0e0;
            border-radius: 20px;
            padding: 0.4rem 1rem;
            font-size: 0.95rem;
            cursor: pointer;
            transition: background 0.2s, color 0.2s;
        }
        .example-btn:hover {
            background: #ff87e0;
            color: #fff;
        }
        .gen-btn {
            width: 100%;
            padding: 1rem 0;
            font-size: 1rem;
            font-weight: bold;
            color: #fff;
            background: linear-gradient(90deg, #ff87e0, #7afcff);
            border: none;
            border-radius: 50px;
            cursor: pointer;
            box-shadow: 0 4px 16px 0 rgba(122,252,255,0.10);
            transition: opacity 0.2s;
            margin-top: 1rem;
        }
        .gen-btn:hover {
            opacity: 0.8;
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
        .placeholder {
            width: 100%;
            height: 300px;
            border: 2px dashed #7afcff;
            border-radius: 10px;
            background: rgba(255,255,255,0.05);
            color: #7afcff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            text-align: center;
            transition: background 0.2s;
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
            .form-card, .result-card { padding: 1rem 0.5rem; }
        }
    </style>
    <script>
        function fillPrompt(text) {
            document.getElementById('prompt').value = text;
        }
    </script>
</head>
<body>
    <div class="header">
        <div class="header-title">Gerador de Imagens IA</div>
        <div class="header-sub">Descreva o que você imagina e nossa IA criará para você</div>
    </div>
    <div class="main-container">
        <div class="form-section">
            <div class="form-card">
                <form method="post" autocomplete="off">
                    <label class="form-label" for="api_key">Chave da API DeepAI:</label>
                    <input class="input" type="text" id="api_key" name="api_key" value="<?= htmlspecialchars($api_key ?: '6a625395-a30d-483c-95d9-070c713a6ad4') ?>" placeholder="Cole sua chave da API DeepAI aqui..." required>
                    
                    <label class="form-label" for="prompt">Descrição da Imagem:</label>
                    <textarea id="prompt" name="prompt" maxlength="200" placeholder="Ex: Um gato astronauta flutuando no espaço com estrelas coloridas…" required><?= htmlspecialchars($prompt) ?></textarea>
                    
                    <div class="examples-list">
                        <?php foreach ($examples as $ex): ?>
                            <button type="button" class="example-btn" onclick="fillPrompt('<?= htmlspecialchars($ex, ENT_QUOTES) ?>')"><?= htmlspecialchars($ex) ?></button>
                        <?php endforeach; ?>
                    </div>
                    <?php if ($error): ?>
                        <div class="error-msg"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>
                    <button class="gen-btn" type="submit">Gerar Imagem</button>
                </form>
            </div>
        </div>
        <div class="result-section">
            <div class="result-card">
                <?php if ($result): ?>
                    <img src="<?= htmlspecialchars($result) ?>" alt="Imagem gerada" class="img-preview">
                    <div style="text-align:center;margin-top:1rem;">
                        <a href="<?= htmlspecialchars($result) ?>" target="_blank" style="color:#7afcff;text-decoration:none;font-size:0.9rem;">Ver imagem em tamanho real</a>
                    </div>
                <?php else: ?>
                    <div class="placeholder">
                        Sua imagem gerada aparecerá aqui.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div style="text-align:center;margin-top:2rem;">
        <a href="deepseek_form.php" class="cta-btn">Comece a Criar Agora</a>
    </div>
</body>
</html>