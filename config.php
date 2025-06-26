<?php
require_once __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

define('API_KEY', '813ecf14-dec8-4965-a83b-62f0a062833e');
define('API_URL', 'https://api.deepai.org/api/text2img');