<?php
require_once __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

define('API_KEY', $_ENV['API_KEY']);
define('API_URL', $_ENV['API_URL']);