<?php declare(strict_types=1);

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Tools::checkCsrf()) {
        $error = 'Your session expired. Please try again.';
    } elseif (isset($_POST['logout'])) {
        Customer::logout();
        Tools::redirect('index.php?page=1');
    } elseif (isset($_POST['loginbtn'])) {
        $login = trim((string) ($_POST['login'] ?? ''));
        $pass = (string) ($_POST['pass'] ?? '');

        if ($login === '' || $pass === '') {
            $error = 'Enter both login and password.';
        } elseif (Customer::login($login, $pass)) {
            Tools::redirect('index.php?page=1');
        } else {
            $error = 'Wrong login or password.';   // same text for both cases on purpose
        }
    }
}
?>
<h3>Login</h3>
<?php if ($error !== ''): ?>
    <div class="alert alert-danger"><?= Tools::e($error) ?></div>
<?php endif ?>
<?php if (Tools::currentUser() !== null): ?>
    <p>You are signed in as <strong><?= Tools::e(Tools::currentUser()) ?></strong>.</p>
<?php else: ?>
    <form action="index.php?page=5" method="post" class="col-md-4">
        <?= Tools::csrfField() ?>
        <div class="mb-3">
            <label class="form-label" for="login">Login</label>
            <input class="form-control" type="text" id="login" name="login" required autofocus>
        </div>
        <div class="mb-3">
            <label class="form-label" for="pass">Password</label>
            <input class="form-control" type="password" id="pass" name="pass" required>
        </div>
        <button type="submit" name="loginbtn" class="btn btn-primary">Log in</button>
    </form>
<?php endif ?>
