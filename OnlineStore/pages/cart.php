<?php declare(strict_types=1);

const CART_COOKIE_PREFIX = 'cart_';   // must match CART_PREFIX in js/cart.js

/** @return list<Item> items from the cart cookies that still exist in the shop */
function cartItems(): array
{
    $items = [];
    foreach ($_COOKIE as $k => $v) {
        $k = (string) $k;
        if (!str_starts_with($k, CART_COOKIE_PREFIX)) {
            continue;
        }
        $id = (int) substr($k, strlen(CART_COOKIE_PREFIX));   // item id after the "_"
        $item = $id > 0 ? Item::fromDb($id) : null;
        if ($item !== null) {
            $items[] = $item;
        }
    }
    return $items;
}

function clearCartCookies(): void
{
    foreach (array_keys($_COOKIE) as $k) {
        if (str_starts_with((string) $k, CART_COOKIE_PREFIX)) {
            setcookie((string) $k, '', ['expires' => time() - 3600, 'path' => '/', 'samesite' => 'Lax']);
            unset($_COOKIE[$k]);
        }
    }
}
?>
<?php if (!isset($_POST['purchaseOrder'])): ?>
<form method="post" action="index.php?page=2">
<?= Tools::csrfField() ?>
<?php
$total = 0;
$items = cartItems();

foreach ($items as $item) {
    $total += $item->pricesale;
    $item->drawForCart();
}

if ($items === []) {
    echo '<p class="text-muted">Your cart is empty.</p>';
}
echo '<hr>Total cost is: ' . Tools::e($total);
?>
<?php if ($items !== []): ?>
<button name="purchaseOrder" class="btn btn-success">Purchase order</button>
<?php endif ?>
</form>
<?php else: ?>
<?php
if (!Tools::checkCsrf()) {
    echo '<div class="alert alert-danger">Your session expired. Please go back to the cart and try again.</div>';
} else {
    $items = cartItems();
    $pdo = Tools::connect();
    $sum = 0;
    try {
        $pdo->beginTransaction();   // all items are sold, or none
        foreach ($items as $item) {
            $item->sale();
            $sum += $item->pricesale;
        }
        $pdo->commit();
        clearCartCookies();
        echo '<div class="alert alert-success">Thank you for your purchase! ' . count($items)
            . ' item(s), total ' . Tools::e($sum) . '.</div>';
    } catch (PDOException $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        error_log($e->getMessage());
        echo '<div class="alert alert-danger">The order could not be completed. Nothing was charged.</div>';
    }
}
?>
<?php endif ?>
