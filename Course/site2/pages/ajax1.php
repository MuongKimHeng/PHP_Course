<?php
declare(strict_types=1);
require_once __DIR__.'/functions.php';
header('Content-Type: application/json; charset=utf-8');
$countryId = (int) ($_GET['cid'] ?? 0);
$stmt = connect()->prepare(
    'SELECT id, city FROM cities WHERE countryid = ? ORDER BY city'
);
$stmt->execute([$countryId]);
echo json_encode($stmt->fetchAll(), JSON_THROW_ON_ERROR);