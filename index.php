<?php
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Almagine</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        @import url('https://fonts.googleapis.com/css?family=Roboto:400,700&display=swap');
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }
        body {
            min-height: 100vh;
            font-family: 'Roboto', Arial, sans-serif;
            background: linear-gradient(135deg, #1e1e3f 0%, #7033a5 100%);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .hero {
            width: 100%;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
        }
        .logo {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.7rem;
            font-size: 3rem;
            font-weight: 700;
            letter-spacing: 2px;
            margin-bottom: 1rem;
            color: #fff;
        }
        .logo-star {
            display: inline-block;
            width: 2.5rem;
            height: 2.5rem;
            vertical-align: middle;
        }
        .subtitle {
            font-size: 1.5rem;
            font-weight: 400;
            margin-bottom: 1.5rem;
        }
        .subtitle .gradient {
            background: linear-gradient(90deg, #ff87e0, #7afcff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            color: transparent;
        }
        .desc {
            font-size: 1.1rem;
            color: #fff;
            margin-bottom: 2.5rem;
            max-width: 420px;
            margin-left: auto;
            margin-right: auto;
        }
        .cta-btn {
            padding: 1rem 2.5rem;
            font-size: 1.1rem;
            font-weight: 700;
            color: #fff;
            background: linear-gradient(90deg, #ff87e0, #7afcff);
            border: none;
            border-radius: 50px;
            cursor: pointer;
            box-shadow: 0 4px 16px 0 rgba(122,252,255,0.10);
            transition: opacity 0.2s;
            text-decoration: none;
            display: inline-block;
        }
        .cta-btn:hover {
            opacity: 0.8;
        }
        @media (max-width: 600px) {
            .logo { font-size: 2.2rem; }
            .logo-star { width: 1.5rem; height: 1.5rem; }
            .subtitle { font-size: 1.1rem; }
            .desc { font-size: 1rem; }
            .cta-btn { font-size: 1rem; padding: 0.8rem 1.5rem; }
        }
    </style>
</head>
<body>
    <div class="hero">
        <div class="logo">
            <svg class="logo-star" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
                <polygon points="18,3 22,14 34,14 24,21 28,33 18,26 8,33 12,21 2,14 14,14" fill="#fff"/>
            </svg>
            Almagine
        </div>
        <div class="subtitle">
            Transforme palavras em <span class="gradient">imagens incríveis</span>
        </div>
        <div class="desc">
            Crie imagens profissionais para suas redes sociais, projetos e campanhas usando IA.<br>
            Simples, rápido e gratuito.
        </div>
        <a href="deepseek_form.php" class="cta-btn">Comece a Criar Agora</a>
    </div>
</body>
</html>