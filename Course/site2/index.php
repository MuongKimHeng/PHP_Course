<?php
declare(strict_types=1);
session_start();
ob_start(); // buffer output so included pages may still send headers
require_once __DIR__ . '/pages/functions.php';
$page = (int) ($_GET['page'] ?? 1);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initialscale=
1">
    <title>Travel Agency</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style1.css" rel="stylesheet">
</head>

<body>
    <div class="container">
        <header><?php require_once __DIR__ . '/pages/login.php'; ?>
        </header>
        <nav><?php require_once __DIR__ . '/pages/menu.php'; ?></nav>
        <section>
            <?php
            $file = match ($page) {
                1 => 'tours.php',
                2 => 'comments.php',
                3 => 'registration.php',
                4 => 'admin.php',
                default => null,
            };
            if ($file !== null) {
                require_once __DIR__ . "/pages/{$file}";
            }
            ?>
        </section>
        <footer>Step Academy &copy;</footer>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>