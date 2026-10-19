<?php
declare(strict_types=1);

class Customer
{
    public const ROLE_ADMIN = 1;
    public const ROLE_CUSTOMER = 2;

    public function __construct(
        public string $login,
        public string $pass,
        public string $imagepath = '',
        public int    $id        = 0,   // 0 until the DBMS assigns one
        public int    $roleid    = self::ROLE_CUSTOMER,   // role "Customer" must exist with id 2
        public int    $discount  = 0,
        public int    $total     = 0,
    ) {}

    /** @return true|int|string true on success, 1062 for duplicate login, or an error text */
    public function intoDb(): true|int|string
    {
        try {
            $pdo = Tools::connect();
            $ps = $pdo->prepare(
                'INSERT INTO customers (login, pass, roleid, discount, total, imagepath)
                 VALUES (:login, :pass, :roleid, :discount, :total, :imagepath)'
            );
            $ps->execute([
                'login'     => $this->login,
                'pass'      => password_hash($this->pass, PASSWORD_DEFAULT),
                'roleid'    => $this->roleid,
                'discount'  => $this->discount,
                'total'     => $this->total,
                'imagepath' => $this->imagepath,
            ]);
            $this->id = (int) $pdo->lastInsertId();
            return true;
        } catch (PDOException $e) {
            // 1062 = MySQL duplicate-key error (login already taken)
            return ($e->errorInfo[1] ?? 0) === 1062 ? 1062 : $e->getMessage();
        }
    }

    public static function fromDb(int $id): ?static
    {
        $ps = Tools::connect()->prepare('SELECT * FROM customers WHERE id = ?');
        $ps->execute([$id]);
        $row = $ps->fetch();

        return $row === false
            ? null
            : new static(
                $row['login'],
                $row['pass'],
                $row['imagepath'] ?? '',
                (int) $row['id'],
                (int) $row['roleid'],
                (int) $row['discount'],
                (int) $row['total'],
            );
    }

    /**
     * Checks the credentials with password_verify() and, on success,
     * stores the name in $_SESSION['reg'].
     */
    public static function login(string $login, string $pass): bool
    {
        $ps = Tools::connect()->prepare('SELECT login, pass FROM customers WHERE login = ?');
        $ps->execute([$login]);
        $row = $ps->fetch();

        // Always run password_verify(), even for unknown logins, so response time
        // does not reveal which logins exist.
        $hash = $row === false ? password_hash('no-such-user', PASSWORD_DEFAULT) : (string) $row['pass'];
        $ok = password_verify($pass, $hash);

        if ($row === false || !$ok) {
            return false;
        }

        session_regenerate_id(true);          // prevents session fixation
        $_SESSION['reg'] = (string) $row['login'];
        Tools::resetAdminCache();
        return true;
    }

    public static function logout(): void
    {
        $_SESSION = [];
        session_regenerate_id(true);
        Tools::resetAdminCache();
    }

    public static function isAdmin(string $login): bool
    {
        $ps = Tools::connect()->prepare('SELECT roleid FROM customers WHERE login = ?');
        $ps->execute([$login]);
        return (int) $ps->fetchColumn() === self::ROLE_ADMIN;
    }
}
