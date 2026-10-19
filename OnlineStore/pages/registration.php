<?php declare(strict_types=1); ?>
<h3>Registration Form</h3>
<?php if (!isset($_POST['regbtn'])): ?>
    <form action="index.php?page=3" method="post" enctype="multipart/form-data">
        <?= Tools::csrfField() ?>
        <label>Login: <input type="text" name="login"></label>
        <label>Password: <input type="password" name="pass1"></label>
        <label>Confirm: <input type="password" name="pass2"></label>
        <label>Select image: <input type="file" name="imagepath" accept="image/*"></label>
        <button type="submit" name="regbtn">Register</button>
    </form>
<?php else:
    $path = '';
    $ok = true;

    if (!Tools::checkCsrf()) {
        echo "<h3 style='color:red;'>Your session expired. Please try again.</h3>";
        $ok = false;
    } elseif (($_POST['pass1'] ?? '') !== ($_POST['pass2'] ?? '')) {
        echo "<h3 style='color:red;'>Passwords Do Not Match!</h3>";
        $ok = false;
    } elseif (($_FILES['imagepath']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
        try {
            $path = Tools::saveImage($_FILES['imagepath']);
        } catch (RuntimeException $e) {
            echo "<h3 style='color:red;'>" . Tools::e($e->getMessage()) . "</h3>";
            $ok = false;
        }
    }

    if ($ok && Tools::register((string) ($_POST['login'] ?? ''), (string) ($_POST['pass1'] ?? ''), $path)) {
        echo "<h3 style='color:green;'>New User Added! You can <a href='index.php?page=5'>log in</a> now.</h3>";
    }
endif; ?>
