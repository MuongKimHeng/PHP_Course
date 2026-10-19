<h3>Registration Form</h3>
<?php if (!isset($_POST['regbtn'])): ?>
<form action="index.php?page=3" method="post">
  <div class="mb-3">
    <label class="form-label" for="login">Login:</label>
    <input type="text" class="form-control" id="login" name="login">
  </div>
  <div class="mb-3">
    <label class="form-label" for="pass1">Password:</label>
    <input type="password" class="form-control" id="pass1" name="pass1">
  </div>
  <div class="mb-3">
    <label class="form-label" for="pass2">Confirm Password:</label>
    <input type="password" class="form-control" id="pass2" name="pass2">
  </div>
  <div class="mb-3">
    <label class="form-label" for="email">Email address:</label>
    <input type="email" class="form-control" id="email" name="email">
  </div>
  <button type="submit" class="btn btn-primary" name="regbtn">Register</button>
</form>
<?php elseif (register($_POST['login'] ?? '', $_POST['pass1'] ?? '',
                       $_POST['email'] ?? '')): ?>
  <h3><span style="color:green;">New User Added!</span></h3>
<?php endif; ?>