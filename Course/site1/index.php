<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <title>Site1</title>
</head>

<body>
    <div class="container">
        <header class="row">
            <div class="col-12">
                <h1>My First PHP Website</h1>
            </div>
        </header>
        <nav class="row">
            <div class="col-12">
                <?php
                include_once 'pages/menu.php';
                include_once 'pages/functions.php';
                ?>
            </div>
        </nav>
        <section class="row">
            <div class="col-12">
                <?php
                $page = $_GET['page'] ?? '1';
                include_once match ($page) {
                    '2' => 'pages/upload.php',
                    '3' => 'pages/gallery.php',
                    '4' => 'pages/registration.php',
                    default => 'pages/home.php',
                };
                ?>
            </div>
        </section>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous">
    </script>
    
</body>

</html>