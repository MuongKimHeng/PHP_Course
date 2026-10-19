<?php // pages/private.php — handler first
if (isset($_POST['addadmin'])) {
    $userId = (int) ($_POST['userid'] ?? 0);
    if ($userId > 0 && ($_FILES['file']['error'] ?? 1) === UPLOAD_ERR_OK) {
        $img = file_get_contents($_FILES['file']['tmp_name']);
        $stmt = connect()->prepare(
            'UPDATE users SET avatar = ?, roleid = 1 WHERE id = ?'
        );
        $stmt->bindValue(1, $img, PDO::PARAM_LOB);
        $stmt->bindValue(2, $userId, PDO::PARAM_INT);
        $stmt->execute();
    }
    header('Location: index.php?page=6');
    exit;
}

$pdo = connect();
echo '<form action="index.php?page=6" method="post"
      enctype="multipart/form-data">';
echo '<select name="userid" class="form-select">';
$stmt = $pdo->query('SELECT id, login FROM users WHERE roleid = 2 ORDER BY login');
foreach ($stmt as $row) {
    $loginName = htmlspecialchars($row['login']);
    echo "<option value='{$row['id']}'>{$loginName}</option>";
}
echo '</select>';
echo '<input type="file" name="file" accept="image/*">';
echo '<input type="submit" name="addadmin" value="Add" class="btn btn-sm btn-info">';
echo '</form>';
?>

<?php
echo '<table class="table table-striped">';
$stmt = $pdo->query('SELECT id, login, email, avatar
                     FROM users WHERE roleid = 1 ORDER BY login');
foreach ($stmt as $row) {
    $loginName = htmlspecialchars($row['login']);
    $email     = htmlspecialchars($row['email'] ?? '');
    // the first admin was promoted via phpMyAdmin and has no avatar yet —
    // render the image only when the BLOB is not NULL
    $cell = '&mdash;';
    if ($row['avatar'] !== null) {
        $img  = base64_encode((string) $row['avatar']);
        $cell = "<img height='100' alt='avatar'
                  src='data:image/jpeg;base64,{$img}'>";
    }
    echo "<tr><td>{$row['id']}</td><td>{$loginName}</td><td>{$email}</td>"
       . "<td>{$cell}</td></tr>";
}
echo '</table>';
?>