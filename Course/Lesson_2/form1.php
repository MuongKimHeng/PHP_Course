<?php
if($_SERVER['REQUEST_METHOD']==='POST'):
// if (isset($_POST['submit'])) 
    echo 'Username: ' . $_POST['username'] . '<br>';
    echo 'Password: ' . $_POST['userpass'];
else: 
    // echo 'No data received.';

?><!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <!-- Method GET -->
    <!-- <form action="handler1.php" method="GET">
        <p>Enter your name: <input type="text" name="username"></p>
        <p>Enter your password: <input type="password" name="userpass"></p>
        <p><button name="submit" value="Login">Submit</button></p>
        <p><button name="submit" value="Search">Submit</button></p> -->

    <!-- Method Post     -->
    <form action="<?= $_SERVER['PHP_SELF'] ?>" method="POST">
        <p>Enter your name: <input type="text" name="username"></p>
        <p>Enter your password: <input type="password" name="userpass"></p>
        <p><button name="submit" value="Login">Submit</button></p>
        <p><button name="submit" value="Search">Submit</button></p>
    </form>
</body>

</html>
<?php endif; ?>