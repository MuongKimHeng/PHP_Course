<h1>GALLARY</h1>
<?php
$path = 'images/';
$extensions = [];
foreach (scandir($path) as $file) {
    if ($file == '.' || $file == '..')
        continue;
    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    if (!in_array($ext, $extensions)) {
        $extensions[] = $ext;
    }
    // echo "$file<br>";
}
?>
<form action="index.php?page=3" method="post">
    <select name="ext" id="ext" class="form-control">
        <option>-- All --</option>
        <?php foreach ($extensions as $ext): ?>
            <option value="<?= $ext ?>"><?= $ext ?></option>
        <?php endforeach ?>
    </select>
    <button type="submit" name="submit" class="btn btn-primary">Show Pictures</button>
</form>
<?php
// if (isset($_POST['submit'])) {
    $ext = preg_replace('/[^a-z0-9]/', '', strtolower($_POST['ext'] ?? ''));
    if($ext=='') $ext = '*';
    $files = glob("images/*.*");

    echo '<div class="card">';
    echo '<div class="card-header">Gallery content</div>';
    echo '<div class="card-body">';
    foreach ($files as $file) {
        $safe = htmlspecialchars($file);
        echo "<a href='{$safe}' target='_blank'> 
              <img src='{$safe}' style='height:100px' alt='picture'
              class='img-thumbnail'></a>";
    }
    echo '</div></div>';
// }