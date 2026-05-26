<?php

$app = require __DIR__ . '/../src/Bootstrap.php';


// index para testar autenticação
$app->route('GET /', function () {
    echo json_encode(['success' => true]);
});


$app->route('POST /', function () {
    $data = json_decode(file_get_contents('php://input'), true);

    // Método para listar bancos de dados existentes
    if (isset($data['action']) and $data['action']=='listar_databases') {
        echo App\Database::listar_databases();
    }

    // Método para listar usuários
    if (isset($data['action']) and $data['action']=='listar_usuarios') {
        echo App\Database::listar_usuarios();
    }

    // Método para verificar se banco de dados existe (retornar true/false)
    if (isset($data['action']) && $data['action'] == 'database_existe') {
        $nome = $data['nome'] ?? '';
        echo json_encode(['existe' => App\Database::database_existe($nome)]);
    }

    // Método para verificar se usuário existe (retornar true/false)
    if (isset($data['action']) && $data['action'] == 'usuario_existe') {
        $nome = $data['nome'] ?? '';
        echo json_encode(['existe' => App\Database::usuario_existe($nome)]);
    }

    // Método para criar banco de dados
    if (isset($data['action']) && $data['action'] == 'criar_database') {
        $nome = $data['nome'] ?? '';
        echo App\Database::criar_database($nome);
    }

    // Método para criar usuário (com mesmo nome do banco de dados e senha gerada alfanumerica retornada)
    if (isset($data['action']) && $data['action'] == 'criar_usuario') {
        $nome = $data['nome'] ?? '';
        echo App\Database::criar_usuario($nome);
    }

    // Método para criar banco com usuário
    if (isset($data['action']) && $data['action'] == 'criar_database_usuario') {
        $nome = $data['nome'] ?? '';
        echo App\Database::criar_database_usuario($nome);
    }

    // Método para conceder privilégios do usuário ao banco de dados
    if (isset($data['action']) && $data['action'] == 'conceder_privilegios') {
        $nome = $data['nome'] ?? '';
        echo App\Database::conceder_privilegios($nome);
    }

    // Método para criar banco com usuário já com privilégios
    if (isset($data['action']) && $data['action'] == 'criar_database_usuario_privilegio') {
        $nome = $data['nome'] ?? '';
        echo App\Database::criar_database_usuario_privilegio($nome);
    }
        
    // Método para trocar senha (retornar a senha gerada alfanumerica)
    if (isset($data['action']) && $data['action'] == 'trocar_senha') {
        $nome = $data['nome'] ?? '';
        echo App\Database::trocar_senha($nome);
    }
});

$app->start();