<?php
declare(strict_types=1);
require_once 'classes.php';

$pdo = Tools::connect();

$pdo->exec('CREATE TABLE IF NOT EXISTS roles (
    id   INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    role VARCHAR(32) NOT NULL UNIQUE
) DEFAULT CHARSET = utf8mb4');

$pdo->exec('CREATE TABLE IF NOT EXISTS customers (
    id        INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    login     VARCHAR(32)  NOT NULL UNIQUE,
    pass      VARCHAR(255) NOT NULL,
    roleid    INT,          FOREIGN KEY (roleid) REFERENCES roles(id) ON UPDATE CASCADE,
    discount  INT,
    total     INT,
    imagepath VARCHAR(255)
) DEFAULT CHARSET = utf8mb4');

// …categories, items, images, sales — same pattern…
$pdo->exec('CREATE TABLE categories (
    id       INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    category VARCHAR(64) NOT NULL UNIQUE
) DEFAULT CHARSET = utf8mb4');
echo 'Table categories created<br>';

$pdo->exec('CREATE TABLE items (
    id        INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    itemname  VARCHAR(64) NOT NULL,
    catid     INT,          FOREIGN KEY (catid) REFERENCES categories(id) ON DELETE CASCADE,
    pricein   INT,
    pricesale INT,
    info      VARCHAR(1024),
    imagepath VARCHAR(255),
    rate      FLOAT DEFAULT 0,
    action    INT   DEFAULT 0
) DEFAULT CHARSET = utf8mb4');
echo 'Table items created<br>';

$pdo->exec('CREATE TABLE images (
    id        INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    itemid    INT,          FOREIGN KEY (itemid) REFERENCES items(id) ON DELETE CASCADE,
    imagepath VARCHAR(255)
) DEFAULT CHARSET = utf8mb4');
echo 'Table images created<br>';

$pdo->exec('CREATE TABLE sales (
    id           INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    customername VARCHAR(32),
    itemname     VARCHAR(64),
    pricein      INT,
    pricesale    INT,
    datesale     DATE
) DEFAULT CHARSET = utf8mb4');
echo 'Table sales created<br>';

// the Customer class relies on role ids 1 (Admin) and 2 (Customer):
$stmt = $pdo->prepare('SELECT COUNT(*) FROM roles');
$stmt->execute();
$count = $stmt->fetchColumn();
if($count==0){
    $pdo->exec("INSERT INTO roles (role) VALUES ('Admin'), ('Customer')");
}