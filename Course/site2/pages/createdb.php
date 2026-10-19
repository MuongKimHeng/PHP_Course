<?php
declare(strict_types=1);

require_once __DIR__ . '/functions.php';

$tables = [
    'countries' => 'CREATE TABLE IF NOT EXISTS countries (
        id      INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
        country VARCHAR(64) UNIQUE
    ) DEFAULT CHARSET = utf8mb4',

    'cities' => 'CREATE TABLE IF NOT EXISTS cities (
        id        INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
        city      VARCHAR(64),
        countryid INT,
        FOREIGN KEY (countryid) REFERENCES countries (id) ON DELETE CASCADE,
        UNIQUE INDEX ucity (city, countryid)
    ) DEFAULT CHARSET = utf8mb4',

    'hotels' => 'CREATE TABLE IF NOT EXISTS hotels (
        id        INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
        hotel     VARCHAR(64),
        cityid    INT,
        FOREIGN KEY (cityid) REFERENCES cities (id) ON DELETE CASCADE,
        countryid INT,
        FOREIGN KEY (countryid) REFERENCES countries (id) ON DELETE CASCADE,
        stars     INT,
        cost      INT,
        info      VARCHAR(1024)
    ) DEFAULT CHARSET = utf8mb4',

    'images' => 'CREATE TABLE IF NOT EXISTS images (
        id        INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
        imagepath VARCHAR(255),
        hotelid   INT,
        FOREIGN KEY (hotelid) REFERENCES hotels (id) ON DELETE CASCADE
    ) DEFAULT CHARSET = utf8mb4',

    'roles' => 'CREATE TABLE IF NOT EXISTS roles (
        id   INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
        role VARCHAR(32)
    ) DEFAULT CHARSET = utf8mb4',

    'users' => 'CREATE TABLE IF NOT EXISTS users (
        id       INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
        login    VARCHAR(32) UNIQUE,
        pass     VARCHAR(255),
        email    VARCHAR(128),
        discount INT,
        roleid   INT,
        FOREIGN KEY (roleid) REFERENCES roles (id) ON DELETE CASCADE,
        avatar   MEDIUMBLOB
    ) DEFAULT CHARSET = utf8mb4',
];

try {
    $pdo = connect();
    foreach ($tables as $name => $sql) {
        $pdo->exec($sql);
        echo "Table {$name} created<br>";
    }
} catch (PDOException $e) {
    exit('Error: ' . htmlspecialchars($e->getMessage()));
}