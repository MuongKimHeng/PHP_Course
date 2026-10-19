<?php declare(strict_types=1); ?>
<?php if(!isset($_POST['purchaseOrder'])):?>
<form method="post" action="index.php?page=2">
<?php
// the user name is the cookie prefix; guests get "cart"
$ruser = $_SESSION['reg'] ?? '';
if ($ruser === '') {
    $ruser = 'cart';
}

$total = 0;

foreach ($_COOKIE as $k => $v) {
    if (!str_starts_with($k, $ruser . '_')) {
        continue;
    }
    $id = (int) substr($k, strlen($ruser) + 1);  // item id after the "_"
    $item = Item::fromDb($id);

    if ($item !== null) {
        $total += $item->pricesale;
        $item->drawForCart();
    }
}

echo "<hr>Total cost is: {$total}";
?>
<button name="purchaseOrder">Purchase order</button>
</form>
<?php else: ?>
    //Sale
<?php endif ?>