<?php $page = $_GET['page'] ?? 1; ?>
<ul class="nav nav-pills">
    <li class="nav-item">
        <a href="index.php?page=1" class="nav-link <?= $page == 1 ? 'active' : '' ?>">Home</a>
    </li>
    <li class="nav-item">
        <a href="index.php?page=2" class="nav-link <?= $page == 2 ? 'active' : '' ?>">UPLOAD</a>
    </li>
    <li class="nav-item">
        <a href="index.php?page=3" class="nav-link <?= $page == 3 ? 'active' : '' ?>">GALLERY</a>
    </li>
    <li class="nav-item">
        <a href="index.php?page=4" class="nav-link <?= $page == 4 ? 'active' : '' ?>">REGISTRAION</a>
    </li>
</ul>


<!-- Using foreach
<?php 
$page = $_GET['page'] ?? 1;
$pages = ['HOME', 'UPLOAD', 'GALLERY', 'REGISTRATION'];
?>
<ul class="nav nav-pills">
    <?php foreach ($pages as $i => $pageName): ?>
        <li class="nav-item">
            <a href="index.php?page=<?= $i + 1 ?>" class="nav-link <?= $page == ($i + 1) ? 'active' : '' ?>">
                <?= $pageName ?>
            </a>
        </li>
    <?php endforeach ?>
</ul> -->