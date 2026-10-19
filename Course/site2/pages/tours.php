<h2>Select Tours</h2>
<hr>
<?php
$pdo = connect();
echo '<form action="index.php?page=1" method="post">';
echo '<select name="countryid" class="form-select d-inline w-auto" onchange="fetchCities(this)">';
echo '<option value="0">Select country...</option>';
foreach ($pdo->query('SELECT * FROM countries ORDER BY country') as $row) {
    $name = htmlspecialchars($row['country']);
    echo "<option value='{$row['id']}'>{$name}</option>";
}
echo '</select>';
echo '<input type="submit" name="selcountry" value="Select Country"
      class="btn btn-sm btn-primary">';

echo '<div><select id="cityList" class="form-select d-inline w-auto" ></select></div>';
echo '</form>';

if (isset($_POST['selcity'])) {
    $cityId = (int) ($_POST['cityid'] ?? 0);
    $stmt = $pdo->prepare(
        'SELECT co.country, ci.city, ho.hotel, ho.cost, ho.stars, ho.id
         FROM hotels ho
         JOIN cities ci    ON ho.cityid    = ci.id
         JOIN countries co ON ho.countryid = co.id
         WHERE ho.cityid = ?'
    );
    $stmt->execute([$cityId]);

    echo '<table class="table table-striped text-center">';
    echo '<thead class="fw-bold"><tr><td>Hotel</td><td>Country</td>
          <td>City</td><td>Price</td><td>Stars</td><td>Link</td></tr></thead>';
    foreach ($stmt as $row) {
        $hotel = htmlspecialchars($row['hotel']);
        $country = htmlspecialchars($row['country']);
        $city = htmlspecialchars($row['city']);
        echo "<tr><td>{$hotel}</td><td>{$country}</td><td>{$city}</td>"
            . "<td>\${$row['cost']}</td><td>{$row['stars']}</td>"
            . "<td><a href='pages/hotelinfo.php?hotel={$row['id']}'
                  target='_blank'>more info</a></td></tr>";
    }
    echo '</table><br>';
}
?>
<script>
    async function fetchCities(countryList) {
        const res = await fetch(`pages/ajax1.php?cid=${countryList.value}`);
        const cities = await res.json();

        cityList.innerHTML = '<option value="0">select city</option>' +
            cities.map(c => `<option value="${c.id}">${c.city}</option>`).join('');
    }
</script>