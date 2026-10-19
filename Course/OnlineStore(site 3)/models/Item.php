<?php
class Item
{
    public function __construct(
        public string $itemname,
        public int $catid,
        public int $pricein,
        public int $pricesale,
        public string $info,
        public string $imagepath,
        public float $rate = 0,
        public int $action = 0,
        public int $id = 0,
    ) {
    }

    // intoDb() and fromDb($id) — same pattern as in Customer
    public function intoDb(): true|int|string
    {
        try {
            $pdo = Tools::connect();
            $ps = $pdo->prepare(
                'INSERT INTO items (itemname, catid, pricein, pricesale, info, imagepath, rate)
                 VALUES (:login, :itemname, :catid, :pricein, :pricesale, :info, :imagepath, :rate)'
            );
            $ps->execute([
                'itemname' => $this->itemname,
                'catid' => $this->catid,
                'pricein' => $this->pricein,
                'pricesale' => $this->pricesale,
                'info' => $this->info,
                'imagepath' => $this->imagepath,
                'rate' => $this->rate ?? 0
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
        $ps = Tools::connect()->prepare('SELECT * FROM items WHERE id = ?');
        $ps->execute([$id]);
        $row = $ps->fetch();

        return $row === false
            ? null
            : new static(
                $row['itemname'],
                $row['catid'],
                (int) $row['pricein'],
                (int) $row['pricesale'],
                $row['info'],
                $row['imagepath'],
                $row['rate'],
                $row['action'],
                (int) $row['id']
            );
    }

    /** @return list<Item> all items, or only one category when $catid > 0 */
    public static function getItems(int $catid = 0): array
    {
        $pdo = Tools::connect();

        if ($catid === 0) {
            $ps = $pdo->prepare('SELECT * FROM items');
            $ps->execute();
        } else {
            $ps = $pdo->prepare('SELECT * FROM items WHERE catid = ?');
            $ps->execute([$catid]);
        }

        $items = [];
        while ($row = $ps->fetch()) {
            $items[] = new Item(
                $row['itemname'],
                (int) $row['catid'],
                (int) $row['pricein'],
                (int) $row['pricesale'],
                $row['info'],
                $row['imagepath'],
                (float) $row['rate'],
                (int) $row['action'],
                (int) $row['id'],
            );
        }
        return $items;
    }

    // A schematic item card — design your own version
    public function draw(): void
    {
        $name = htmlspecialchars($this->itemname);
        $info = htmlspecialchars($this->info);
        $img = htmlspecialchars($this->imagepath);

        echo <<<HTML
        <div class="item-card">
          <a href="pages/iteminfo.php?id={$this->id}" target="_blank">{$name}</a>
          <span class="rate">{$this->rate}&nbsp;rate</span>
          <img src="{$img}" alt="{$name}" height="100">
          <span class="price">\$&nbsp;{$this->pricesale}</span>
          <p>{$info}</p>
          <button onclick="addToCart({$this->id})" class="btn btn-primary">Add To My Cart</button>
        </div>
        HTML;
    }
    public function drawForCart(): void
    {
        $name = htmlspecialchars($this->itemname);
        // $info = htmlspecialchars($this->info);
        $img = htmlspecialchars($this->imagepath);

        echo <<<HTML
        <div class="item-card row">
          <div class="col-2"><img src="{$img}" class="w-100"/></div>
          <div class="col">{$this->itemname}</div>
          <div class="col-auto">{$this->pricesale}</div>
          <div class="col-auto ms-auto"><button type="button" onclick="eraseCookie({$this->id});window.location.reload()">x</button></div>
        </div>
        HTML;
    }

    public function sale(): void
    {
        $pdo = Tools::connect();
        $ruser = $_SESSION['reg'] ?? 'cart';

        // increase the customer's total…
        $pdo->prepare('UPDATE customers SET total = total + ? WHERE login = ?')
            ->execute([$this->pricesale, $ruser]);

        // …record the sale…
        $pdo->prepare('INSERT INTO sales (customername, itemname, pricein, pricesale, datesale)
                  VALUES (?, ?, ?, ?, ?)')
            ->execute([
                $ruser,
                $this->itemname,
                $this->pricein,
                $this->pricesale,
                new DateTimeImmutable()->format('Y-m-d')
            ]);

        // …and remove the sold item
        $pdo->prepare('DELETE FROM items WHERE id = ?')->execute([$this->id]);
    }
}