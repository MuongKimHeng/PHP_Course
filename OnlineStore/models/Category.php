<?php
declare(strict_types=1);

final class Category
{
    public function __construct(
        public string $category,
        public int $id = 0,
        public int $itemcount = 0,   // filled by all()
    ) {}

    /** @return true|int|string true on success, 1062 for duplicate name, or an error text */
    public function intoDb(): true|int|string
    {
        try {
            $pdo = Tools::connect();
            $pdo->prepare('INSERT INTO categories (category) VALUES (?)')->execute([$this->category]);
            $this->id = (int) $pdo->lastInsertId();
            return true;
        } catch (PDOException $e) {
            return ($e->errorInfo[1] ?? 0) === 1062 ? 1062 : $e->getMessage();
        }
    }

    /** @return true|int|string same convention as intoDb() */
    public static function rename(int $id, string $name): true|int|string
    {
        try {
            Tools::connect()->prepare('UPDATE categories SET category = ? WHERE id = ?')->execute([$name, $id]);
            return true;
        } catch (PDOException $e) {
            return ($e->errorInfo[1] ?? 0) === 1062 ? 1062 : $e->getMessage();
        }
    }

    /**
     * Deletes an empty category. items.catid has ON DELETE CASCADE, so deleting a
     * category that still has items would silently delete the items too; refuse instead.
     */
    public static function delete(int $id): bool
    {
        $pdo = Tools::connect();
        $ps = $pdo->prepare('SELECT COUNT(*) FROM items WHERE catid = ?');
        $ps->execute([$id]);
        if ((int) $ps->fetchColumn() > 0) {
            return false;
        }
        $pdo->prepare('DELETE FROM categories WHERE id = ?')->execute([$id]);
        return true;
    }

    public static function nameOf(int $id): ?string
    {
        $ps = Tools::connect()->prepare('SELECT category FROM categories WHERE id = ?');
        $ps->execute([$id]);
        $name = $ps->fetchColumn();
        return $name === false ? null : (string) $name;
    }

    /** @return list<Category> */
    public static function all(): array
    {
        $ps = Tools::connect()->prepare(
            'SELECT c.id, c.category, COUNT(i.id) AS cnt
               FROM categories c LEFT JOIN items i ON i.catid = c.id
              GROUP BY c.id, c.category
              ORDER BY c.category'
        );
        $ps->execute();

        $list = [];
        while ($row = $ps->fetch()) {
            $list[] = new Category((string) $row['category'], (int) $row['id'], (int) $row['cnt']);
        }
        return $list;
    }
}
