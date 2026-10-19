<?php
declare(strict_types=1);

function connect(
    string $host = 'localhost',
    string $user = 'root',
    string $pass = 'Step@2025',
    string $dbname = 'hotels_e19_v2',
    int $port = 3306,      // change if your bundle's MySQL uses another port
): PDO {
    static $pdo = null;
    return $pdo ??= new PDO(
        "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4",
        $user,
        $pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ],
    );
}
function register(string $name, string $pass, string $email): bool
{
    $name  = trim($name);
    $pass  = trim($pass);
    $email = trim($email);

    if ($name === '' || $pass === '' || $email === '') {
        echo "<h3><span style='color:red;'>Fill All Required Fields!</span></h3>";
        return false;
    }
    if (strlen($name) < 3 || strlen($name) > 30 ||
        strlen($pass) < 3 || strlen($pass) > 30) {
        echo "<h3><span style='color:red;'>Values Length Must Be Between 3 And 30!</span></h3>";
        return false;
    }

    try {
        $stmt = connect()->prepare(
            'INSERT INTO users (login, pass, email, roleid) VALUES (?, ?, ?, 2)'
        );
        $stmt->execute([$name, password_hash($pass, PASSWORD_DEFAULT), $email]);
    } catch (PDOException $e) {
        if (($e->errorInfo[1] ?? 0) === 1062) {
            echo "<h3><span style='color:red;'>This Login Is Already Taken!</span></h3>";
        } else {
            echo "<h3><span style='color:red;'>Registration error!</span></h3>";
        }
        return false;
    }
    return true;
}
function login(string $name, string $pass): bool
{
    $name = trim($name);
    $pass = trim($pass);
    if ($name === '' || $pass === '') {
        return false;
    }

    $stmt = connect()->prepare('SELECT * FROM users WHERE login = ?');
    $stmt->execute([$name]);
    $user = $stmt->fetch();

    if ($user !== false && password_verify($pass, $user['pass'])) {
        session_regenerate_id(true);          // defeat session fixation
        $_SESSION['ruser'] = $name;
        if ((int) $user['roleid'] === 1) {   // 1 = Admin
            $_SESSION['radmin'] = $name;
        }
        return true;
    }
    return false;                             // same answer for any failure
}
// try {
//     $pdo = connect();
// } catch (PDOException $e) {
//     echo '<div class="alert alert-danger" role="alert">'
//         . $e->getMessage() . '</div>';
// }
// $stmt = $pdo->query('SELECT * FROM `countries`');
// foreach ($stmt as $row) {
//     print_r($row);
//     echo '<br>';
// }
// $stmt = $pdo->prepare('INSERT INTO countries(country) VALUES(?)');
// $stmt->execute([$_POST['country']]);
// 'INSERT INTO countries(country) 
// VALUES("Laos");UPDATE users SET password="123456";'
// field input: 
//  Laos");INSERT INTO users (username,pwd) VALUES("superadmin","123456