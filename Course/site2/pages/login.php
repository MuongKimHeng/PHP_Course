<?php // pages/login.php
$qs = isset($_GET['page']) ? '?page=' . (int) $_GET['page'] : '';

if (isset($_POST['ex'])) {              // logout pressed
    unset($_SESSION['ruser'], $_SESSION['radmin']);
    header("Location: index.php{$qs}");
    exit;
}
if (isset($_POST['press'])              // login pressed
    && login($_POST['login'] ?? '', $_POST['pass'] ?? '')) {
    header("Location: index.php{$qs}");
    exit;
}

if (isset($_SESSION['ruser'])): $user = htmlspecialchars($_SESSION['ruser']); ?>
  <form action="index.php<?= $qs ?>" method="post" class="text-end">
    <h4>Hello, <span><?= $user ?></span>
    <input type="submit" value="Logout" name="ex" class="btn btn-sm"></h4>
  </form>
<?php else: ?>
  <form action="index.php<?= $qs ?>" method="post" class="text-end">
    <input type="text"     name="login" size="10" placeholder="login">
    <input type="password" name="pass"  size="10" placeholder="password">
    <input type="submit"   name="press" value="Login" class="btn btn-sm">
  </form>
<?php endif; ?>