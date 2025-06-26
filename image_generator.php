<?php
require_once 'config.php';

function generate_image($prompt, $n = 1) {
    if (!$prompt || !is_string($prompt) || strlen($prompt) < 3) {
        return ['error' => 'Prompt inválido.'];
    }

    $data = [
        'prompt' => $prompt,
        'n' => $n
    ];

    $ch = curl_init(API_URL);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer " . API_KEY,
        "Content-Type: application/json"
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        $error = curl_error($ch);
        curl_close($ch);
        return ['error' => "Erro de rede: $error"];
    }

    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $json = json_decode($response, true);

    if ($http_code !== 200 || !isset($json['data'][0]['url'])) {
        return ['error' => $json['error']['message'] ?? 'Erro desconhecido na API', 'raw' => $json];
    }

    // Suporte a múltiplas imagens
    $urls = array_map(fn($img) => $img['url'], $json['data']);

    return ['images' => $urls];
}