<?php
require_once __DIR__ . '/pages/classes.php';
session_start();
ob_start();
Tools::connect();
$page = (int)$_GET['page']??1;
$pages = [1=>'Catalog',2=>'Cart',3=>'Registration',4=>'Admin'];
?><!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <div class="container">
        <header class="py-3">
            <nav class="nav nav-pills">
                <?php foreach($pages as $key=>$label):?>
                <a class="nav-link <?= $page==$key?'active':'' ?>" href="index.php?page=<?= $key ?>" aria-current="page"><?= $label ?></a>
                <?php endforeach ?>
            </nav>
        </header>
        <main>
            <?php
            include __DIR__ . match($page){
                1=>'/pages/catalog.php',
                2=>'/pages/cart.php',
                3=>'/pages/registration.php',
                4=>'/pages/admin.php',
                default=>'/pages/notfound.php'
            }
            ?>
        </main>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>
    <script src="js/cart.js"></script>
</body>

</html>