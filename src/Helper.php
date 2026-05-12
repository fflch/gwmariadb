<?php

namespace App;

class Helper {

    public static function json() {
        return json_decode(file_get_contents('php://input'), true);
    }

    public static function sanitize($value) {
        return preg_replace('/[^a-zA-Z0-9_]/', '', $value);
    }
}
