<?php // pages/menu.php — $page is set by index.php
$items = [1 => 'Tours', 2 => 'Comments', 3 => 'Registration', 4 => 'Admin Forms',];
if (isset($_SESSION['radmin'])) {
    $items[6] = 'Private';
}
?>
<ul class="nav nav-pills nav-fill">
<?php foreach ($items as $n => $title): ?>
  <li class="nav-item">
    <a class="nav-link<?= $page === $n ? ' active' : '' ?>"
       href="index.php?page=<?= $n ?>"><?= $title ?></a>
  </li>
<?php endforeach; ?>
</ul>