<?php 
if (!isset($_SESSION['radmin'])) {
    echo "<h3><span style='color:red;'>For Administrators Only!</span></h3>";
    return;   // stop this fragment, keep rendering the rest of index.php
}
// section A handlers — top of admin.php, before any output
if (isset($_POST['addcountry'])) {
    $country = trim($_POST['country'] ?? '');
    if ($country !== '') {
        connect()->prepare('INSERT INTO countries (country) VALUES (?)')
            ->execute([$country]);
    }
    header('Location: index.php?page=4');
    exit;
}

if (isset($_POST['delcountry'])) {
    $stmt = connect()->prepare('DELETE FROM countries WHERE id = ?');
    foreach ($_POST['del'] ?? [] as $id) {
        $stmt->execute([(int) $id]);
    }
    header('Location: index.php?page=4');
    exit;
}

if (isset($_POST['addcity'])) {
    $city = trim($_POST['city'] ?? '');
    $countryId = (int) ($_POST['countryname'] ?? 0);
    if ($city !== '' && $countryId > 0) {
        connect()->prepare('INSERT INTO cities (city, countryid) VALUES (?, ?)')
            ->execute([$city, $countryId]);
    }
    header('Location: index.php?page=4');
    exit;
}

if (isset($_POST['delcity'])) {
    $stmt = connect()->prepare('DELETE FROM cities WHERE id = ?');
    foreach ($_POST['delci'] ?? [] as $id) {
        $stmt->execute([(int) $id]);
    }
    header('Location: index.php?page=4');
    exit;
}

if (isset($_POST['addhotel'])) {
    $hotel = trim($_POST['hotel'] ?? '');
    $cost = (int) ($_POST['cost'] ?? 0);
    $stars = (int) ($_POST['stars'] ?? 0);
    $info = trim($_POST['info'] ?? '');
    $cityId = (int) ($_POST['hcity'] ?? 0);

    if ($hotel !== '' && $cost > 0 && $stars > 0 && $cityId > 0) {
        $pdo = connect();
        $stmt = $pdo->prepare('SELECT countryid FROM cities WHERE id = ?');
        $stmt->execute([$cityId]);
        $countryId = (int) $stmt->fetchColumn();

        $pdo->prepare('INSERT INTO hotels (hotel, cityid, countryid, stars, cost, info)
                       VALUES (?, ?, ?, ?, ?, ?)')
            ->execute([$hotel, $cityId, $countryId, $stars, $cost, $info]);
    }
    header('Location: index.php?page=4');
    exit;
}

if (isset($_POST['delhotel'])) {
    $stmt = connect()->prepare('DELETE FROM hotels WHERE id = ?');
    foreach ($_POST['delho'] ?? [] as $id) {
        $stmt->execute([(int) $id]);
    }
    header('Location: index.php?page=4');
    exit;
}

if (isset($_POST['addimage'])) {
    $hotelId = (int) ($_POST['hotelid'] ?? 0);
    $stmt    = connect()->prepare(
        'INSERT INTO images (hotelid, imagepath) VALUES (?, ?)'
    );

    foreach ($_FILES['file']['name'] as $k => $origName) {
        if ($_FILES['file']['error'][$k] !== UPLOAD_ERR_OK) {
            continue;  // skip failed uploads
        }
        // never keep the user's file name — generate a random one
        $ext  = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
        $safe = bin2hex(random_bytes(8)) . ".{$ext}";

        if (move_uploaded_file($_FILES['file']['tmp_name'][$k],
                              __DIR__ . "/../images/{$safe}")) {
            $stmt->execute([$hotelId, "images/{$safe}"]);
        }
    }
    header('Location: index.php?page=4');
    exit;
}
?>
<div class="row">
    <div class="col-md-6 left">
        <!-- section A: Countries form -->
        <?php
        $pdo = connect();
        echo '<form action="index.php?page=4" method="post" id="formcountry">';
        echo '<table class="table table-striped">';
        foreach ($pdo->query('SELECT * FROM countries') as $row) {
            $name = htmlspecialchars($row['country']);
            echo '<tr>';
            echo "<td>{$row['id']}</td><td>{$name}</td>";
            echo "<td><input type='checkbox' name='del[]' value='{$row['id']}'></td>";
            echo '</tr>';
        }
        echo '</table>';
        echo '<input type="text" name="country" placeholder="Country">';
        echo '<input type="submit" name="addcountry" value="Add"
      class="btn btn-sm btn-info">';
        echo '<input type="submit" name="delcountry" value="Delete"
      class="btn btn-sm btn-warning">';
        echo '</form>';
        ?>
    </div>
    <div class="col-md-6 right">
        <!-- section B: Cities form    -->
        <?php
        echo '<form action="index.php?page=4" method="post" id="formcity">';
        $sql = 'SELECT ci.id, ci.city, co.country
        FROM cities ci
        JOIN countries co ON ci.countryid = co.id';
        echo '<table class="table table-striped">';
        foreach ($pdo->query($sql) as $row) {
            $city = htmlspecialchars($row['city']);
            $country = htmlspecialchars($row['country']);
            echo "<tr><td>{$row['id']}</td><td>{$city}</td><td>{$country}</td>"
                . "<td><input type='checkbox' name='delci[]' value='{$row['id']}'></td></tr>";
        }
        echo '</table>';

        // drop-down list of countries: value = id, text = name
        echo '<select name="countryname" class="form-select">';
        foreach ($pdo->query('SELECT * FROM countries') as $row) {
            $name = htmlspecialchars($row['country']);
            echo "<option value='{$row['id']}'>{$name}</option>";
        }
        echo '</select>';
        echo '<input type="text" name="city" placeholder="City">';
        echo '<input type="submit" name="addcity" value="Add" class="btn btn-sm btn-info">';
        echo '<input type="submit" name="delcity" value="Delete" class="btn btn-sm btn-warning">';
        echo '</form>';
        ?>
    </div>
</div>
<hr>
<div class="row">
    <div class="col-md-6 left">
        <!-- section C: Hotels form    -->
        <?php
        echo '<form action="index.php?page=4" method="post" id="formhotel">';
        $sql = 'SELECT ho.id, ho.hotel, ho.stars, ci.city, co.country
        FROM hotels ho
        JOIN cities ci    ON ho.cityid    = ci.id
        JOIN countries co ON ho.countryid = co.id';
        echo '<table class="table">';
        foreach ($pdo->query($sql) as $row) {
            $place = htmlspecialchars("{$row['city']}-{$row['country']}");
            $hotel = htmlspecialchars($row['hotel']);
            echo "<tr><td>{$row['id']}</td><td>{$place}</td><td>{$hotel}</td>"
                . "<td>{$row['stars']}</td>"
                . "<td><input type='checkbox' name='delho[]' value='{$row['id']}'></td></tr>";
        }
        echo '</table>';

        // "city : country" drop-down; value = city id
        $sql = 'SELECT ci.id, ci.city, co.country
        FROM cities ci
        JOIN countries co ON ci.countryid = co.id';
        echo '<select name="hcity" class="form-select">';
        foreach ($pdo->query($sql) as $row) {
            $label = htmlspecialchars("{$row['city']} : {$row['country']}");
            echo "<option value='{$row['id']}'>{$label}</option>";
        }
        echo '</select>';
        echo '<input type="text" name="hotel" placeholder="Hotel">';
        echo '<input type="text" name="cost" placeholder="Cost">';
        echo '&nbsp;Stars: <input type="number" name="stars" min="1" max="5">';
        echo '<br><textarea name="info" placeholder="Description"></textarea><br>';
        echo '<input type="submit" name="addhotel" value="Add" class="btn btn-sm btn-info">';
        echo '<input type="submit" name="delhotel" value="Delete" class="btn btn-sm btn-warning">';
        echo '</form>';
        ?>
    </div>
    <div class="col-md-6 right">
        <!-- section D: Images form    -->
        <?php
        echo '<form action="index.php?page=4" method="post"
       enctype="multipart/form-data">';
        $sql = 'SELECT ho.id, co.country, ci.city, ho.hotel
        FROM hotels ho
        JOIN cities ci    ON ci.id = ho.cityid
        JOIN countries co ON co.id = ho.countryid
        ORDER BY co.country';
        echo '<select name="hotelid" class="form-select">';
        foreach ($pdo->query($sql) as $row) {
            $label = htmlspecialchars("{$row['country']} {$row['city']} {$row['hotel']}");
            echo "<option value='{$row['id']}'>{$label}</option>";
        }
        echo '</select>';
        echo '<input type="file" name="file[]" multiple accept="image/*">';
        echo '<input type="submit" name="addimage" value="Add" class="btn btn-sm btn-info">';
        echo '</form>';
        ?>
    </div>
</div>