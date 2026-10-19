<?php
declare(strict_types=1);

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

    /** @return true|int|string true on success, 1062 for a duplicate key, or an error text */
    public function intoDb(): true|int|string
    {
        try {
            $pdo = Tools::connect();
            $ps = $pdo->prepare(
                'INSERT INTO items (itemname, catid, pricein, pricesale, info, imagepath, rate, action)
                 VALUES (:itemname, :catid, :pricein, :pricesale, :info, :imagepath, :rate, :action)'
            );
            $ps->execute([
                'itemname' => $this->itemname,
                'catid' => $this->catid,
                'pricein' => $this->pricein,
                'pricesale' => $this->pricesale,
                'info' => $this->info,
                'imagepath' => $this->imagepath,
                'rate' => $this->rate,
                'action' => $this->action,
            ]);
            $this->id = (int) $pdo->lastInsertId();
            return true;
        } catch (PDOException $e) {
            return ($e->errorInfo[1] ?? 0) === 1062 ? 1062 : $e->getMessage();
        }
    }

    /** @param array<string,mixed> $row */
    private static function fromRow(array $row): static
    {
        return new static(
            (string) $row['itemname'],
            (int) $row['catid'],
            (int) $row['pricein'],
            (int) $row['pricesale'],
            (string) $row['info'],
            (string) $row['imagepath'],
            (float) $row['rate'],
            (int) $row['action'],
            (int) $row['id'],
        );
    }

    public static function fromDb(int $id): ?static
    {
        $ps = Tools::connect()->prepare('SELECT * FROM items WHERE id = ?');
        $ps->execute([$id]);
        $row = $ps->fetch();

        return $row === false ? null : static::fromRow($row);
    }

    /** @return list<Item> all items, or only one category when $catid > 0 */
    public static function getItems(int $catid = 0): array
    {
        $pdo = Tools::connect();

        if ($catid === 0) {
            $ps = $pdo->prepare('SELECT * FROM items ORDER BY id');
            $ps->execute();
        } else {
            $ps = $pdo->prepare('SELECT * FROM items WHERE catid = ? ORDER BY id');
            $ps->execute([$catid]);
        }

        $items = [];
        while ($row = $ps->fetch()) {
            $items[] = static::fromRow($row);
        }
        return $items;
    }

    // ------------------------------------------------------------- gallery

    /** Adds an extra image of an item to the `images` table. */
    public static function addImage(int $itemid, string $imagepath): void
    {
        Tools::connect()
            ->prepare('INSERT INTO images (itemid, imagepath) VALUES (?, ?)')
            ->execute([$itemid, $imagepath]);
    }

    /** @return list<string> only the extra images from the `images` table */
    public function getExtraImages(): array
    {
        $ps = Tools::connect()->prepare('SELECT imagepath FROM images WHERE itemid = ? ORDER BY id');
        $ps->execute([$this->id]);
        return array_map('strval', $ps->fetchAll(PDO::FETCH_COLUMN));
    }

    /** @return list<string> cover image first, then every extra image */
    public function getGallery(): array
    {
        $all = $this->getExtraImages();
        if ($this->imagepath !== '') {
            array_unshift($all, $this->imagepath);
        }
        return $all;
    }

    // ------------------------------------------------------------- drawing

    // A schematic item card — design your own version
    public function draw(): void
    {
        $name = Tools::e($this->itemname);
        $info = Tools::e($this->info);
        $img = Tools::e($this->imagepath);
        $rate = Tools::e($this->rate);
        $price = Tools::e($this->pricesale);

        echo <<<HTML
        <div class="item-card">
          <a href="index.php?page=6&amp;id={$this->id}">{$name}</a>
          <span class="rate">{$rate}&nbsp;rate</span>
          <img src="{$img}" alt="{$name}" height="100">
          <span class="price">\$&nbsp;{$price}</span>
          <p>{$info}</p>
          <button onclick="addToCart({$this->id}, this)" class="btn btn-primary">Add To My Cart</button>
        </div>
        HTML;
    }

    public function drawForCart(): void
    {
        $name = Tools::e($this->itemname);
        $img = Tools::e($this->imagepath);
        $price = Tools::e($this->pricesale);

        echo <<<HTML
        <div class="item-card row">
          <div class="col-2"><img src="{$img}" alt="{$name}" class="w-100"/></div>
          <div class="col">{$name}</div>
          <div class="col-auto">{$price}</div>
          <div class="col-auto ms-auto"><button type="button" onclick="removeFromCart({$this->id});window.location.reload()">x</button></div>
        </div>
        HTML;
    }

    /** Records the sale. Call inside a transaction when selling several items. */
    public function sale(): void
    {
        $pdo = Tools::connect();
        $ruser = Tools::currentUser() ?? 'cart';

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
                (new DateTimeImmutable())->format('Y-m-d'),
            ]);

        // …and remove the sold item
        $pdo->prepare('DELETE FROM items WHERE id = ?')->execute([$this->id]);
    }
}
