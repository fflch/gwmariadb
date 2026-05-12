<?php

namespace App;

class Methods {
    public static function db() {
        $TOKEN   = $_ENV['TOKEN']   ?? 'default_token';
        $DB_HOST = $_ENV['DB_HOST'] ?? '127.0.0.1';
        $DB_USER = $_ENV['DB_USER'] ?? 'root';
        $DB_PASS = $_ENV['DB_PASS'] ?? '';
        $DB_PORT = $_ENV['DB_PORT'] ?? '8306';

        return new PDO(
            "mysql:host=$DB_HOST;port=$DB_PORT",
            $DB_USER,
            $DB_PASS,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
    }


}
