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

    // Mônica: Método para verificar se banco de dados existe (retornar true/false)
    if (isset($data['action']) && $data['action'] == 'database_existe') {
        $nome = $data['nome'] ?? '';
        echo json_encode(['existe' => App\Database::database_existe($nome)]);
    }

    // Mônica: Método para verificar se usuário existe (retornar true/false)
    if (isset($data['action']) && $data['action'] == 'usuario_existe') {
        $nome = $data['nome'] ?? '';
        echo json_encode(['existe' => App\Database::usuario_existe($nome)]);
    }


    // Mônica: Método para criar banco de dados
    // Mônica: Método para criar usuário (com mesmo nome do banco de dados e senha gerada alfanumerica, retornar a senha gerada)
    // Mônica: Método para conceder privilégios do usuário ao banco de dados - grant all privileges on {NOME_APP}.* to {NOME_APP}@'%' identified by 'SENHA_INVENTADA';
    // Mônica: Método para trocar senha (retornar a senha gerada alfanumerica)
});

$app->start();