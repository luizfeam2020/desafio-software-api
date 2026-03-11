<?php declare(strict_types=1);

namespace App\Database;

use PDO;

final class Connection
{
    public static function pdo(): PDO
    {
        $dbFile = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'database.sqlite';

        $dir = dirname($dbFile);
        if (!is_dir($dir)) mkdir($dir, 0777, true);

        $pdo = new PDO('sqlite:' . $dbFile, null, null, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);

        $pdo->exec('PRAGMA foreign_keys = ON;');
        $pdo->exec('PRAGMA journal_mode = WAL;');

        return $pdo;
    }
}