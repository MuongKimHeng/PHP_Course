<h3>Registration Form</h3>
<?php if (!isset($_POST['regbtn'])): ?>
    <form action="index.php?page=3" method="post" enctype="multipart/form-data">
        <label>Login: <input type="text" name="login"></label>
        <label>Password: <input type="password" name="pass1"></label>
        <label>Confirm: <input type="password" name="pass2"></label>
        <label>Select image: <input type="file" name="imagepath"></label>
        <button type="submit" name="regbtn">Register</button>
    </form>
<?php else:
    $path = '';
    if (is_uploaded_file($_FILES['imagepath']['tmp_name'] ?? '')) {
        $path = 'images/' . basename($_FILES['imagepath']['name']);
        move_uploaded_file($_FILES['imagepath']['tmp_name'], $path);
    }

    if (Tools::register($_POST['login'] ?? '', $_POST['pass1'] ?? '', $path)) {
        echo "<h3 style='color:green;'>New User Added!</h3>";
    }
endif; ?>