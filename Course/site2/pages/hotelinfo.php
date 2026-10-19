<?php
declare(strict_types=1);   // must be the very first statement — before any HTML!
require_once __DIR__ . '/functions.php';   // same folder — no "pages/" prefix!

$hotelId = (int) ($_GET['hotel'] ?? 0);
$pdo = connect();

$stmt = $pdo->prepare('SELECT * FROM hotels WHERE id = ?');
$stmt->execute([$hotelId]);
$hotel = $stmt->fetch();

if ($hotel === false) {
    http_response_code(404);
    exit('Hotel not found');
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Hotel Info</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/info.css">
</head>

<body>
    <?php
    echo '<h2 class="text-uppercase text-center">'
        . htmlspecialchars($hotel['hotel']) . '</h2>';
    echo '<div class="row"><div class="col-md-6 text-center">';

    // stars: repeat the symbol $hotel['stars'] times
    echo '<p class="fs-4">' . str_repeat('★', (int) $hotel['stars']) . '</p>';

    // gallery
    $stmt = $pdo->prepare('SELECT imagepath FROM images WHERE hotelid = ?');
    $stmt->execute([$hotelId]);
    echo '<span class="badge bg-info">Watch our pictures</span>';
    echo '<ul id="gallery">';
    foreach ($stmt as $row) {
        $path = htmlspecialchars($row['imagepath']);
        echo "<li><img src='../{$path}' alt='hotel photo'></li>";
    }
    echo '</ul>';

    echo '<p>' . nl2br(htmlspecialchars($hotel['info'])) . '</p>';
    echo '</div></div>';
    ?>
</body>

</html>