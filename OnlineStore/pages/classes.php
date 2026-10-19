<?php
declare(strict_types=1);

final class Tools
{
    private static ?PDO $pdo = null;
    private static ?bool $admin = null;

    private const ALLOWED_IMAGES = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/gif'  => 'gif',
        'image/webp' => 'webp',
    ];
    private const MAX_IMAGE_BYTES = 5 * 1024 * 1024;

    /** One shared connection per request (needed for transactions in the cart). */
    public static function connect(
        string $host = 'localhost',
        string $user = 'root',
        string $pass = 'Step@2025',
        string $dbname = 'shop',
        int $port = 3306,   // change if your bundle's MySQL uses another port
    ): PDO {
        if (self::$pdo !== null) {
            return self::$pdo;
        }
        $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4";

        return self::$pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }

    public static function register(string $name, string $pass, string $imagepath): bool
    {
        $name = trim($name);
        $pass = trim($pass);

        if ($name === '' || $pass === '') {
            echo "<h3 style='color:red;'>Fill All Required Fields!</h3>";
            return false;
        }
        if (
            strlen($name) < 3 || strlen($name) > 30 ||
            strlen($pass) < 3 || strlen($pass) > 30
        ) {
            echo "<h3 style='color:red;'>Values Length Must Be Between 3 And 30!</h3>";
            return false;
        }

        $err = (new Customer($name, $pass, $imagepath))->intoDb();

        if ($err !== true) {
            echo $err === 1062
                ? "<h3 style='color:red;'>This Login Is Already Taken!</h3>"
                : "<h3 style='color:red;'>Error: " . self::e((string) $err) . "</h3>";
            return false;
        }
        return true;
    }

    // ---------------------------------------------------------------- output

    /** Shorthand for htmlspecialchars() with safe defaults. */
    public static function e(string|int|float|null $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    // ------------------------------------------------------- auth / sessions

    public static function currentUser(): ?string
    {
        $user = $_SESSION['reg'] ?? null;
        return is_string($user) && $user !== '' ? $user : null;
    }

    /** Authorization is always checked against the DB role, never trusted from the session. */
    public static function isAdmin(): bool
    {
        if (self::$admin !== null) {
            return self::$admin;
        }
        $user = self::currentUser();
        return self::$admin = $user !== null && Customer::isAdmin($user);
    }

    public static function resetAdminCache(): void
    {
        self::$admin = null;
    }

    // ------------------------------------------------------------------ CSRF

    public static function csrfToken(): string
    {
        if (!isset($_SESSION['csrf']) || !is_string($_SESSION['csrf'])) {
            $_SESSION['csrf'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf'];
    }

    public static function csrfField(): string
    {
        return '<input type="hidden" name="csrf" value="' . self::e(self::csrfToken()) . '">';
    }

    public static function checkCsrf(): bool
    {
        $sent = $_POST['csrf'] ?? '';
        return is_string($sent) && hash_equals(self::csrfToken(), $sent);
    }

    // -------------------------------------------------- flash + redirection

    public static function flash(string $type, string $text): void
    {
        $_SESSION['flash'] = ['type' => $type, 'text' => $text];
    }

    /** Prints (and clears) the pending flash message, if any. */
    public static function showFlash(): void
    {
        $f = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);
        if (is_array($f) && isset($f['type'], $f['text'])) {
            echo '<div class="alert alert-' . self::e($f['type']) . '">' . self::e($f['text']) . '</div>';
        }
    }

    /** Post/Redirect/Get. Drops the buffered page so only the redirect is sent. */
    public static function redirect(string $url): never
    {
        while (ob_get_level() > 0) {
            ob_end_clean();
        }
        header('Location: ' . $url);
        exit;
    }

    // --------------------------------------------------------------- uploads

    /**
     * Validates one uploaded image and moves it into /images with a random name.
     * @param array{name:string,tmp_name:string,error:int,size:int} $file
     * @return string path relative to the site root, e.g. "images/ab12cd.jpg"
     * @throws RuntimeException on any validation problem
     */
    public static function saveImage(array $file): string
    {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            throw new RuntimeException('Upload failed (code ' . (int) ($file['error'] ?? -1) . ').');
        }
        $tmp = (string) $file['tmp_name'];
        if (!is_uploaded_file($tmp)) {
            throw new RuntimeException('Not a valid upload.');
        }
        if ((int) $file['size'] > self::MAX_IMAGE_BYTES) {
            throw new RuntimeException('Image is larger than 5 MB.');
        }
        $mime = (new finfo(FILEINFO_MIME_TYPE))->file($tmp);
        $ext = self::ALLOWED_IMAGES[$mime] ?? null;
        if ($ext === null) {
            throw new RuntimeException('Only JPG, PNG, GIF or WEBP images are allowed.');
        }

        $dir = __DIR__ . '/../images';
        if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
            throw new RuntimeException('Cannot create the images folder.');
        }
        $name = bin2hex(random_bytes(8)) . '.' . $ext;
        if (!move_uploaded_file($tmp, $dir . '/' . $name)) {
            throw new RuntimeException('Cannot store the uploaded file.');
        }
        return 'images/' . $name;
    }

    /**
     * Turns PHP's awkward $_FILES['x'] layout for <input multiple> into a list of single-file arrays.
     * @return list<array{name:string,tmp_name:string,error:int,size:int}>
     */
    public static function normalizeFiles(mixed $files): array
    {
        if (!is_array($files) || !isset($files['name'])) {
            return [];
        }
        if (!is_array($files['name'])) {
            return [$files];
        }
        $out = [];
        foreach (array_keys($files['name']) as $i) {
            $out[] = [
                'name'     => (string) $files['name'][$i],
                'tmp_name' => (string) $files['tmp_name'][$i],
                'error'    => (int) $files['error'][$i],
                'size'     => (int) $files['size'][$i],
            ];
        }
        return $out;
    }
}

require_once __DIR__ . '/../models/Customer.php';
require_once __DIR__ . '/../models/Category.php';
require_once __DIR__ . '/../models/Item.php';
require_once __DIR__ . '/../models/Report.php';
