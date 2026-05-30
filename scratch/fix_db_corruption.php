<?php

$host = '127.0.0.1';
$db = 'iieedu_db';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    echo "Connected to MySQL.\n";

    echo "Dropping database $db...\n";
    $pdo->exec("DROP DATABASE IF EXISTS `$db` ");
    echo "Database dropped.\n";

    echo "Creating database $db...\n";
    $pdo->exec("CREATE DATABASE `$db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "Database created successfully.\n";

} catch (PDOException $e) {
    echo 'Error: '.$e->getMessage()."\n";
}
