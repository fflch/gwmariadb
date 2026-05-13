<?php 

require __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use flight\Engine;

$dotenv = Dotenv::createUnsafeImmutable(__DIR__ . '/../');
$dotenv->load();

// carrega database
require __DIR__ . '/Database.php';

// cria app
$app = new Engine();

// middleware simples
$TOKEN   = $_ENV['TOKEN']   ?? 'default_token';
$app->before('start', function () use ($TOKEN) {
    $headers = getallheaders();

    if (($headers['X-Token'] ?? null) !== $TOKEN) {
        // definir status HTTP 401
        http_response_code(401);

        echo json_encode(['error' => 'unauthorized']);
        exit;
    }
});

return $app;