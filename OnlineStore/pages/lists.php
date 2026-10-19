<?php
declare(strict_types=1);
require_once __DIR__ . '/classes.php';

$cat = (int) ($_POST['cat'] ?? 0);

foreach (Item::getItems($cat) as $item) {
    $item->draw();
}
