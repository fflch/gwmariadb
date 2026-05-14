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
                'sys'
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


}
