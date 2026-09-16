<?php
declare(strict_types=1);
const USERS_FILE = 'pages/users.txt'; // path as seen from index.php
function register(string $name, string $pass, string $email): bool
{
    // --- validation block ---
    $name = trim($name);
    $pass = trim($pass);
    $email = trim($email);
    if ($name === '' || $pass === '' || $email === '') {
        echo "<h3 style='color:red;'>Fill All Required Fields!</h3>";
        return false;
    }
    if (strlen($name) < 3 || strlen($name) > 30 ||
        strlen($pass) < 3 || strlen($pass) > 30) {
        echo "<h3 style='color:red;'>Values Length Must Be Between 3 And 30!</h3>";
        return false;
    }
    if (filter_var($email, FILTER_VALIDATE_EMAIL) === false ||
        str_contains($name, ':')) {
        echo "<h3 style='color:red;'>Invalid Email Or Login!</h3>";
        return false;
    }

    $lines = is_file(USERS_FILE)
        ? file(USERS_FILE, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES)
        : [];
    foreach ($lines as $line) {
        [$readName] = explode(':', $line, 2);
        if ($readName === $name) {
            echo "<h3 style='color:red;'>Such Login Name Is Already Used!</h3>";
            return false;
        }
    }
    $line = $name . ':' . password_hash($pass, PASSWORD_DEFAULT) . ':' . $email . PHP_EOL;
    file_put_contents(USERS_FILE, $line, FILE_APPEND | LOCK_EX);
    return true;
}
