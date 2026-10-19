<?php
declare(strict_types=1);

require_once __DIR__ . '/pages/classes.php';
session_start();
ob_start();   // lets pages send redirects after output has started

$page = (int) ($_GET['page'] ?? 1);
$user = Tools::currentUser();
$isAdmin = Tools::isAdmin();

// menu: only what the current visitor may use (the pages themselves re-check access)
$pages = [1 => 'Catalog', 2 => 'Cart'];
if ($user === null) {
    $pages[3] = 'Registration';
    $pages[5] = 'Login';
}
if ($isAdmin) {
    $pages[4] = 'Admin';
    $pages[7] = 'Reports';
}
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
        <header class="py-3 d-flex align-items-center">
            <nav class="nav nav-pills">
                <?php foreach ($pages as $key => $label): ?>
                <a class="nav-link <?= $page === $key ? 'active' : '' ?>" href="index.php?page=<?= $key ?>"><?= Tools::e($label) ?></a>
                <?php endforeach ?>
            </nav>
            <?php if ($user !== null): ?>
            <form method="post" action="index.php?page=5" class="ms-auto d-flex align-items-center gap-2">
                <?= Tools::csrfField() ?>
                <span class="text-muted">Signed in as <strong><?= Tools::e($user) ?></strong><?= $isAdmin ? ' (admin)' : '' ?></span>
                <button type="submit" name="logout" class="btn btn-outline-secondary btn-sm">Log out</button>
            </form>
            <?php endif ?>
        </header>
        <main>
            <?php
            include __DIR__ . match ($page) {
                1 => '/pages/catalog.php',
                2 => '/pages/cart.php',
                3 => '/pages/registration.php',
                4 => '/pages/admin.php',
                5 => '/pages/login.php',
                6 => '/pages/iteminfo.php',
                7 => '/pages/reports.php',
                default => '/pages/notfound.php',
            };
            ?>
        </main>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>
    <script src="js/cart.js"></script>
</body>

</html>
