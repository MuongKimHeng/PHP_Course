<?php
class Customer
{
    public function __construct(
        public string $login,
        public string $pass,
        public string $imagepath = '',
        public int    $id        = 0,   // 0 until the DBMS assigns one
        public int    $roleid    = 2,   // role "Customer" must exist with id 2
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
            $this->id = $pdo->lastInsertId();
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
            : new static($row['login'], $row['pass'], $row['imagepath'] ?? '', (int)$row['id']);
    }
}