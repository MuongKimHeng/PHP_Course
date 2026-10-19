<?php declare(strict_types=1);

// opened from the catalog as index.php?page=6&id=<item id>
$id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
$item = ($id !== false && $id !== null && $id > 0) ? Item::fromDb($id) : null;

if ($item === null) {
    http_response_code(404);
    echo '<h1>Item not found</h1><p><a href="index.php?page=1">Back to the catalog</a></p>';
    return;
}

$name = Tools::e($item->itemname);
$category = Category::nameOf($item->catid);
$gallery = $item->getGallery();
?>
<p><a href="index.php?page=1">&larr; Back to the catalog</a></p>

<div class="row g-4">
    <div class="col-md-7">
        <?php if ($gallery === []): ?>
            <div class="text-muted">No images.</div>
        <?php else: ?>
            <div id="gallery" class="carousel slide gallery" data-bs-ride="false">
                <div class="carousel-inner">
                    <?php foreach ($gallery as $i => $path): ?>
                        <div class="carousel-item <?= $i === 0 ? 'active' : '' ?>">
                            <img src="<?= Tools::e($path) ?>" class="d-block w-100" alt="<?= $name ?> – image <?= $i + 1 ?>">
                        </div>
                    <?php endforeach ?>
                </div>
                <?php if (count($gallery) > 1): ?>
                    <button class="carousel-control-prev" type="button" data-bs-target="#gallery" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon bg-dark rounded" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#gallery" data-bs-slide="next">
                        <span class="carousel-control-next-icon bg-dark rounded" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                <?php endif ?>
            </div>
            <?php if (count($gallery) > 1): ?>
                <div class="gallery-thumbs d-flex flex-wrap gap-2 mt-2">
                    <?php foreach ($gallery as $i => $path): ?>
                        <img src="<?= Tools::e($path) ?>" class="img-thumbnail" alt="Show image <?= $i + 1 ?>"
                             data-bs-target="#gallery" data-bs-slide-to="<?= $i ?>">
                    <?php endforeach ?>
                </div>
            <?php endif ?>
        <?php endif ?>
    </div>

    <div class="col-md-5">
        <h2><?= $name ?></h2>
        <p class="text-muted mb-1">Category: <?= Tools::e($category ?? 'Uncategorized') ?></p>
        <p class="mb-1">Rating: <?= Tools::e($item->rate) ?></p>
        <?php if ($item->action > 0): ?><span class="badge text-bg-danger">On sale</span><?php endif ?>
        <p class="fs-3 mt-2">$&nbsp;<?= Tools::e($item->pricesale) ?></p>
        <p><?= nl2br(Tools::e($item->info)) ?></p>
        <button onclick="addToCart(<?= $item->id ?>, this)" class="btn btn-primary">Add To My Cart</button>
    </div>
</div>
