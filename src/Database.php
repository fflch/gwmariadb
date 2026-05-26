<?php

namespace App;

class Database {
    private static function db() {
        $DB_HOST = $_ENV['DB_HOST'] ?? '127.0.0.1';
        $DB_USER = $_ENV['DB_USER'] ?? 'root';
        $DB_PASS = $_ENV['DB_PASS'] ?? '';
        $DB_PORT = $_ENV['DB_PORT'] ?? '8306';

        return new \PDO(
            "mysql:host=$DB_HOST;port=$DB_PORT",
            $DB_USER,
            $DB_PASS,
            [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]
        );
    }

    public static function listar_databases() {
        $stmt = self::db()->query("
            SHOW DATABASES
            WHERE `Database` NOT IN (
                'information_schema',
                'mysql',
                'performance_schema',
                'sys',
                'm_ysql',
                '_m_ysql'
            )
        ");

        return json_encode($stmt->fetchAll(\PDO::FETCH_COLUMN));
    }

    public static function listar_usuarios() {
        $stmt = self::db()->query("
            SELECT User
            FROM mysql.user
            WHERE User NOT IN (
                'root',
                'mysql',
                'mariadb.sys',
                'debian-sys-maint',
                'mysql.session',
                'mysql.sys',
                'healthcheck',
                'admin'
            )
            AND User <> ''
            GROUP BY User
            ORDER BY User
        ");

        return json_encode($stmt->fetchAll(\PDO::FETCH_COLUMN));
    }

    public static function database_existe(string $nome): bool
    {
        $lista = json_decode(self::listar_databases(), true);
        return in_array($nome, $lista ?? []);
    }
    
    public static function usuario_existe(string $nome): bool
    {
        $lista = json_decode(self::listar_usuarios(), true);
        return in_array($nome, $lista ?? []);
    }

    public static function criar_database(string $nome): string
    {
        if (!preg_match('/^[a-zA-Z][a-zA-Z0-9_]*$/', $nome)) {
            return json_encode(['sucesso' => false, 'mensagem' => 'Nome invalido']);
        } else if (strlen($nome) > 64) {
            return json_encode(['sucesso' => false, 'mensagem' => 'Nome muito grande']);
        }

        if (self::database_existe($nome)) {
            return json_encode(['sucesso' => false, 'mensagem' => 'Database ja existe']);
        }

        if (self::usuario_existe($nome)) {
            return json_encode(['sucesso' => false, 'mensagem' => 'Usuario com este nome ja existe']);
        }

        try {
            self::db()->exec("CREATE DATABASE `$nome`");
            return json_encode(['sucesso' => true, 'mensagem' => 'Database criada com sucesso']);
        } catch (\PDOException $e) {
            return json_encode(['sucesso' => false, 'mensagem' => $e->getMessage()]);
        }
    }

    public static function criar_usuario(string $nome): string
    {
        if (!preg_match('/^[a-zA-Z][a-zA-Z0-9_]*$/', $nome)) {
            return json_encode(['sucesso' => false, 'mensagem' => 'Nome invalido']);
        } else if (strlen($nome) > 32) {
            return json_encode(['sucesso' => false, 'mensagem' => 'Nome muito grande']);
        }

        if (self::usuario_existe($nome)) {
            return json_encode(['sucesso' => false, 'mensagem' => 'Usuario ja existe']);
        }

        $senha = self::gerar_senha();

        try {
            self::db()->exec("CREATE USER `$nome`@'%' IDENTIFIED BY '$senha'");
            return json_encode(['sucesso' => true, 'mensagem' => 'Usuario criado com sucesso', 'senha' => $senha]);
        } catch (\PDOException $e) {
            return json_encode(['sucesso' => false, 'mensagem' => $e->getMessage()]);
        }
    }

    private static function gerar_senha(int $tamanho = 24): string
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $senha = '';
        $max = strlen($chars) - 1;
        for ($i = 0; $i < $tamanho; $i++) {
            $senha .= $chars[random_int(0, $max)];
        }
        return $senha;
    }

    public static function criar_database_usuario(string $nome): string
    {
        $criar_database = json_decode(self::criar_database($nome), true);
        if (!$criar_database['sucesso']) {
            return json_encode(['sucesso' => false, 'mensagem' => $criar_database['mensagem']]);
        }

        $criar_usuario = json_decode(self::criar_usuario($nome), true);
        if (!$criar_usuario['sucesso']) {
            return json_encode(['sucesso' => false, 'mensagem' => $criar_usuario['mensagem']]);
        }

        return json_encode(['sucesso' => true, 'senha' => $criar_usuario['senha']]);
    }
            
    public static function conceder_privilegios(string $nome): string
    {
        if (!self::database_existe($nome)) {
            return json_encode(['sucesso' => false, 'mensagem' => 'Database nao existe']);
        }

        if (!self::usuario_existe($nome)) {
            return json_encode(['sucesso' => false, 'mensagem' => 'Usuario nao existe']);
        }

        try {
            self::db()->exec("GRANT ALL PRIVILEGES ON `$nome`.* TO `$nome`@'%'");
            self::db()->exec("FLUSH PRIVILEGES");
            return json_encode(['sucesso' => true, 'mensagem' => 'Privilegios concedidos com sucesso']);
        } catch (\PDOException $e) {
            return json_encode(['sucesso' => false, 'mensagem' => $e->getMessage()]);
        }
    }

    public static function criar_database_usuario_privilegio(string $nome): string
    {
        $criar_database_usuario = json_decode(self::criar_database_usuario($nome), true);
        if (!$criar_database_usuario['sucesso']) {
            return json_encode(['sucesso' => false, 'mensagem' => $criar_database_usuario['mensagem']]);
        }

        $conceder_privilegios = json_decode(self::conceder_privilegios($nome), true);
        if (!$conceder_privilegios['sucesso']) {
            return json_encode(['sucesso' => false, 'mensagem' => $conceder_privilegios['mensagem']]);
        }

        return json_encode(['sucesso' => true, 'senha' => $criar_database_usuario['senha']]);
    }

    public static function trocar_senha(string $nome): string
    {
        if (!self::usuario_existe($nome)) {
            return json_encode(['sucesso' => false, 'mensagem' => 'Usuario nao existe']);
        }

        $senha = self::gerar_senha();

        try {
            self::db()->exec("ALTER USER `$nome`@'%' IDENTIFIED BY '$senha'");
            return json_encode(['sucesso' => true, 'senha' => $senha]);
        } catch (\PDOException $e) {
            return json_encode(['sucesso' => false, 'mensagem' => $e->getMessage()]);
        }
    }
}
