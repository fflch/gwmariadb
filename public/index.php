<?php

require __DIR__ . '/../vendor/autoload.php';  // subir um nível

use Dotenv\Dotenv;
use flight\Engine;

// carrega .env
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$app = new Engine();

// config


// conexão PDO
function db() {
    global $DB_HOST, $DB_USER, $DB_PASS;

    return new PDO(
        "mysql:host=$DB_HOST",
        $DB_USER,
        $DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
}

// helper
function json() {
    return json_decode(file_get_contents('php://input'), true);
}

function sanitize($value) {
    return preg_replace('/[^a-zA-Z0-9_]/', '', $value);
}

// middleware simples
$app->before('start', function () use ($TOKEN) {
    $headers = getallheaders();

    if (($headers['X-Token'] ?? null) !== $TOKEN) {
        // definir status HTTP 401
        http_response_code(401);

        echo json_encode(['error' => 'unauthorized']);
        exit;
    }
});

// --------------------
// ROTAS
// --------------------

// index
$app->route('GET /', function () {
    echo json_encode(['success' => true]);
});


$app->route('POST /', function () {
    $data = json_decode(file_get_contents('php://input'), true);

    // Método para listar bancos de dados existentes
    if (isset($data['action']) and $data['action']=='list_databases') {
        $stmt = db()->query("SHOW DATABASES");
        echo json_encode($stmt->fetchAll(PDO::FETCH_COLUMN));
    }

});


/*
// listar bancos
$app->route('GET /db/list', function () {
    echo "aqui";
    $stmt = db()->query("SHOW DATABASES");
    echo json_encode($stmt->fetchAll(PDO::FETCH_COLUMN));
});


// criar banco
$app->route('POST /db/create', function () {
    $data = json();
    $name = sanitize($data['name']);

    db()->exec("CREATE DATABASE `$name`");

    echo json_encode(['success' => true]);
});




// listar usuários
$app->route('GET /db/users', function () {
    try {
        $pdo = db();

        // seleciona usuários e host
        $stmt = $pdo->query("SELECT User, Host FROM mysql.user");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(['success' => true, 'users' => $users]);
    } catch (\PDOException $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
});

// criar usuário


// grant


// trocar senha


*/

$app->start();