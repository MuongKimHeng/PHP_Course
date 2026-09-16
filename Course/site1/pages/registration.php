<h3>Registration Form</h3>
<?php
if (!isset($_POST['regbtn'])) {
?>
<form action="index.php?page=4" method="post">
<div class="form-floating mb-3">
    <input type="text" class="form-control" name="login" id="login" placeholder="e.g. KimHeng">
    <label class="form-label" for="login">Login</label>
</div>
<div class="form-floating mb-3">
    <input type="password" class="form-control" name="pass1" id="pass1" placeholder="password">
    <label class="form-label" for="pass1">Password</label>
</div>
<div class="form-floating mb-3">
    <input type="password" class="form-control" name="pass2" id="pass2" placeholder="cf password">
    <label class="form-label" for="pass2">Confirm Password</label>
</div>
<div class="form-floating mb-3">
    <input type="email" class="form-control" name="email" id="email" placeholder="email">
    <label class="form-label" for="email">Email address</label>
</div>
<button type="submit" class="btn btn-primary" name="regbtn">Register</button>
</form>
<?php
} else {
    $name = $_POST['login'];
    $pass1 = $_POST['pass1'];
    $pass2 = $_POST['pass2'];
    $email = $_POST['email'];
    if(register($name, $pass1, $email)==true){
          echo "<h3 style='color':green;'>New User Added!</h3>";
    }
}
?>
