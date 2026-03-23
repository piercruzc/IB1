<?php
/**
 * Database Connection Helper
 * Uses PDO with environment variables from docker-compose
 */

function getDB() {
    static $pdo = null;
    
    if ($pdo === null) {
        $host = getenv('DB_HOST') ?: 'mysql';
        $name = getenv('DB_NAME') ?: 'ibm_blog';
        $user = getenv('DB_USER') ?: 'ibm_blog';
        $pass = getenv('DB_PASS') ?: 'ibm_blog_pass';

        try {
            $pdo = new PDO(
                "mysql:host={$host};dbname={$name};charset=utf8mb4",
                $user,
                $pass,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
        } catch (PDOException $e) {
            error_log("Database connection failed: " . $e->getMessage());
            die("Error de conexión a la base de datos.");
        }
    }

    return $pdo;
}
