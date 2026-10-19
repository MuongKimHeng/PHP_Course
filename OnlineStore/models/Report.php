<?php
declare(strict_types=1);

/** Aggregations over the `sales` table. Every method takes an optional inclusive date range (Y-m-d). */
final class Report
{
    /** @return array{0:string,1:list<string>} WHERE clause + positional params */
    private static function range(?string $from, ?string $to): array
    {
        $where = [];
        $params = [];
        if ($from !== null) {
            $where[] = 'datesale >= ?';
            $params[] = $from;
        }
        if ($to !== null) {
            $where[] = 'datesale <= ?';
            $params[] = $to;
        }
        return [$where ? ' WHERE ' . implode(' AND ', $where) : '', $params];
    }

    /** @return list<array<string,mixed>> */
    private static function rows(string $sql, array $params): array
    {
        $ps = Tools::connect()->prepare($sql);
        $ps->execute($params);
        return $ps->fetchAll();
    }

    /** @return array{sold:int,revenue:int,cost:int,profit:int,margin:float} */
    public static function summary(?string $from, ?string $to): array
    {
        [$w, $p] = self::range($from, $to);
        $r = self::rows(
            "SELECT COUNT(*) AS sold, COALESCE(SUM(pricesale),0) AS revenue, COALESCE(SUM(pricein),0) AS cost
               FROM sales{$w}",
            $p
        )[0];
        $revenue = (int) $r['revenue'];
        $cost = (int) $r['cost'];
        return [
            'sold'    => (int) $r['sold'],
            'revenue' => $revenue,
            'cost'    => $cost,
            'profit'  => $revenue - $cost,
            'margin'  => $revenue > 0 ? round(($revenue - $cost) / $revenue * 100, 1) : 0.0,
        ];
    }

    /** @return list<array{date:string,sold:int,revenue:int,profit:int}> */
    public static function byDay(?string $from, ?string $to): array
    {
        [$w, $p] = self::range($from, $to);
        return array_map(static fn(array $r): array => [
            'date'    => (string) $r['d'],
            'sold'    => (int) $r['sold'],
            'revenue' => (int) $r['revenue'],
            'profit'  => (int) $r['revenue'] - (int) $r['cost'],
        ], self::rows(
            "SELECT datesale AS d, COUNT(*) AS sold, SUM(pricesale) AS revenue, SUM(pricein) AS cost
               FROM sales{$w} GROUP BY datesale ORDER BY datesale",
            $p
        ));
    }

    /** @return list<array{customer:string,sold:int,revenue:int,last:string}> */
    public static function byCustomer(?string $from, ?string $to): array
    {
        [$w, $p] = self::range($from, $to);
        return array_map(static fn(array $r): array => [
            'customer' => (string) $r['customername'],
            'sold'     => (int) $r['sold'],
            'revenue'  => (int) $r['revenue'],
            'last'     => (string) $r['last'],
        ], self::rows(
            "SELECT customername, COUNT(*) AS sold, SUM(pricesale) AS revenue, MAX(datesale) AS last
               FROM sales{$w} GROUP BY customername ORDER BY revenue DESC",
            $p
        ));
    }

    /** @return list<array{item:string,sold:int,revenue:int,profit:int}> */
    public static function byItem(?string $from, ?string $to, int $limit = 10): array
    {
        [$w, $p] = self::range($from, $to);
        $limit = max(1, min($limit, 100));   // int-cast and clamped: safe to inline
        return array_map(static fn(array $r): array => [
            'item'    => (string) $r['itemname'],
            'sold'    => (int) $r['sold'],
            'revenue' => (int) $r['revenue'],
            'profit'  => (int) $r['revenue'] - (int) $r['cost'],
        ], self::rows(
            "SELECT itemname, COUNT(*) AS sold, SUM(pricesale) AS revenue, SUM(pricein) AS cost
               FROM sales{$w} GROUP BY itemname ORDER BY revenue DESC LIMIT {$limit}",
            $p
        ));
    }
}
