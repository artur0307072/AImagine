<?php
<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/generator.php'; // já traz a função generate_image_deepai

use Dotenv\Dotenv;

// Carrega variáveis do .env
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$html_dir = __DIR__ . '/html';
$images_dir = __DIR__ . '/images';
$api_key = $_ENV['DEEPAI_API_KEY']; // Carrega do .env

if (!is_dir($images_dir)) {
    mkdir($images_dir, 0777, true);
}

function extract_article($content) {
    $start = '<!-- ARTICOL START -->';
    $end = '<!-- ARTICOL FINAL -->';
    $ini = strpos($content, $start);
    $fim = strpos($content, $end);
    if ($ini === false || $fim === false) return null;
    $ini += strlen($start);
    return trim(substr($content, $ini, $fim - $ini));
}

foreach (glob($html_dir . '/*.html') as $file) {
    $content = file_get_contents($file);
    $article = extract_article($content);
    if (!$article) continue;

    $filename = pathinfo($file, PATHINFO_FILENAME) . '_image.jpg';
    $local_image_path = $images_dir . '/' . $filename;
    $img_tag = '<img src="images/' . $filename . '">';

    // Só gera se não existe ainda
    if (!file_exists($local_image_path)) {
        $result = generate_image_deepai($article, $api_key);
        if (isset($result['images'][0])) {
            $img_url = $result['images'][0];
            $img_data = file_get_contents($img_url);
            file_put_contents($local_image_path, $img_data);
        }
    }

    // Substitui YYY pela tag da imagem
    $new_content = preg_replace('/YYY/', $img_tag, $content, 1);
    file_put_contents($file, $new_content);
}

header('Location: index.php?done=1');
exit;