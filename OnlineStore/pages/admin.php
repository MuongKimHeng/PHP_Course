<?php declare(strict_types=1);

if (!Tools::isAdmin()) {
    http_response_code(Tools::currentUser() === null ? 401 : 403);
    echo '<div class="alert alert-danger">Access denied. <a href="index.php?page=5">Log in</a> as an administrator.</div>';
    return;
}

// ------------------------------------------------------------------ handlers
// Every action ends with Post/Redirect/Get, so a refresh never re-submits a form.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Tools::checkCsrf()) {
        Tools::flash('danger', 'Your session expired. Please try again.');
        Tools::redirect('index.php?page=4');
    }

    $action = (string) ($_POST['action'] ?? '');
    try {
        switch ($action) {
            case 'add_category':
                $name = trim((string) ($_POST['category'] ?? ''));
                if ($name === '' || mb_strlen($name) > 64) {
                    Tools::flash('danger', 'Category name must be 1–64 characters.');
                    break;
                }
                $r = (new Category($name))->intoDb();
                $r === true
                    ? Tools::flash('success', "Category “{$name}” added.")
                    : Tools::flash('danger', $r === 1062 ? 'This category already exists.' : 'Could not add the category.');
                break;

            case 'rename_category':
                $name = trim((string) ($_POST['category'] ?? ''));
                if ($name === '' || mb_strlen($name) > 64) {
                    Tools::flash('danger', 'Category name must be 1–64 characters.');
                    break;
                }
                $r = Category::rename((int) ($_POST['id'] ?? 0), $name);
                $r === true
                    ? Tools::flash('success', 'Category renamed.')
                    : Tools::flash('danger', $r === 1062 ? 'A category with this name already exists.' : 'Could not rename the category.');
                break;

            case 'delete_category':
                Category::delete((int) ($_POST['id'] ?? 0))
                    ? Tools::flash('success', 'Category deleted.')
                    : Tools::flash('danger', 'Only empty categories can be deleted. Move or sell its items first.');
                break;

            case 'add_item':
                $name = trim((string) ($_POST['itemname'] ?? ''));
                $catid = (int) ($_POST['catid'] ?? 0);
                $pricein = filter_var($_POST['pricein'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]);
                $pricesale = filter_var($_POST['pricesale'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]);
                $info = trim((string) ($_POST['info'] ?? ''));

                if ($name === '' || mb_strlen($name) > 64 || mb_strlen($info) > 1024) {
                    Tools::flash('danger', 'Name must be 1–64 characters, description at most 1024.');
                } elseif (Category::nameOf($catid) === null) {
                    Tools::flash('danger', 'Choose a category.');
                } elseif ($pricein === false || $pricesale === false) {
                    Tools::flash('danger', 'Prices must be whole numbers, 0 or more.');
                } else {
                    $cover = Tools::saveImage($_FILES['cover'] ?? []);
                    $r = (new Item($name, $catid, $pricein, $pricesale, $info, $cover))->intoDb();
                    $r === true
                        ? Tools::flash('success', "Item “{$name}” added.")
                        : Tools::flash('danger', 'Could not add the item.');
                }
                break;

            case 'add_images':
                $itemid = (int) ($_POST['itemid'] ?? 0);
                $item = Item::fromDb($itemid);
                $files = array_filter(
                    Tools::normalizeFiles($_FILES['images'] ?? null),
                    static fn(array $f): bool => $f['error'] !== UPLOAD_ERR_NO_FILE
                );
                if ($item === null) {
                    Tools::flash('danger', 'Choose an item.');
                } elseif ($files === []) {
                    Tools::flash('danger', 'Choose at least one image.');
                } else {
                    $saved = 0;
                    foreach ($files as $f) {
                        Item::addImage($itemid, Tools::saveImage($f));
                        $saved++;
                    }
                    Tools::flash('success', "{$saved} image(s) added to “{$item->itemname}”.");
                }
                break;

            default:
                Tools::flash('danger', 'Unknown action.');
        }
    } catch (RuntimeException $e) {       // upload validation problems: safe to show
        Tools::flash('danger', $e->getMessage());
    } catch (PDOException $e) {
        error_log($e->getMessage());
        Tools::flash('danger', 'Database error. Nothing was saved.');
    }
    Tools::redirect('index.php?page=4');
}

$categories = Category::all();
$items = Item::getItems();
?>
<h3>Administration</h3>
<?php Tools::showFlash(); ?>

<div class="row g-4">
    <!-- ============================================================ categories -->
    <section class="col-lg-6">
        <h5>Categories</h5>
        <form method="post" action="index.php?page=4" class="input-group mb-3">
            <?= Tools::csrfField() ?>
            <input type="hidden" name="action" value="add_category">
            <input class="form-control" type="text" name="category" maxlength="64" placeholder="New category" required>
            <button class="btn btn-primary" type="submit">Add</button>
        </form>

        <?php if ($categories === []): ?>
            <p class="text-muted">No categories yet.</p>
        <?php endif ?>
        <table class="table table-sm align-middle">
            <?php foreach ($categories as $c): ?>
            <tr>
                <td>
                    <form method="post" action="index.php?page=4" class="input-group input-group-sm">
                        <?= Tools::csrfField() ?>
                        <input type="hidden" name="action" value="rename_category">
                        <input type="hidden" name="id" value="<?= $c->id ?>">
                        <input class="form-control" type="text" name="category" maxlength="64"
                               value="<?= Tools::e($c->category) ?>" required>
                        <button class="btn btn-outline-secondary" type="submit">Rename</button>
                    </form>
                </td>
                <td class="text-muted text-nowrap"><?= $c->itemcount ?> item(s)</td>
                <td>
                    <form method="post" action="index.php?page=4"
                          onsubmit="return confirm('Delete this category?');">
                        <?= Tools::csrfField() ?>
                        <input type="hidden" name="action" value="delete_category">
                        <input type="hidden" name="id" value="<?= $c->id ?>">
                        <button class="btn btn-sm btn-outline-danger" type="submit"
                                <?= $c->itemcount > 0 ? 'disabled title="Only empty categories can be deleted"' : '' ?>>Delete</button>
                    </form>
                </td>
            </tr>
            <?php endforeach ?>
        </table>
    </section>

    <!-- ================================================================ images -->
    <section class="col-lg-6">
        <h5>Add extra images to an item</h5>
        <form method="post" action="index.php?page=4" enctype="multipart/form-data" class="mb-4">
            <?= Tools::csrfField() ?>
            <input type="hidden" name="action" value="add_images">
            <div class="mb-2">
                <select class="form-select" name="itemid" required>
                    <option value="">Select item...</option>
                    <?php foreach ($items as $it): ?>
                        <option value="<?= $it->id ?>"><?= Tools::e($it->itemname) ?></option>
                    <?php endforeach ?>
                </select>
            </div>
            <div class="mb-2">
                <input class="form-control" type="file" name="images[]" accept="image/jpeg,image/png,image/gif,image/webp" multiple required>
                <div class="form-text">JPG, PNG, GIF or WEBP, up to 5 MB each. The cover image stays in the item itself.</div>
            </div>
            <button class="btn btn-primary" type="submit">Upload</button>
        </form>
    </section>

    <!-- ================================================================== item -->
    <section class="col-12">
        <h5>Add an item</h5>
        <form method="post" action="index.php?page=4" enctype="multipart/form-data" class="row g-2">
            <?= Tools::csrfField() ?>
            <input type="hidden" name="action" value="add_item">
            <div class="col-md-4"><input class="form-control" type="text" name="itemname" maxlength="64" placeholder="Name" required></div>
            <div class="col-md-3">
                <select class="form-select" name="catid" required>
                    <option value="">Category...</option>
                    <?php foreach ($categories as $c): ?>
                        <option value="<?= $c->id ?>"><?= Tools::e($c->category) ?></option>
                    <?php endforeach ?>
                </select>
            </div>
            <div class="col-md-2"><input class="form-control" type="number" min="0" step="1" name="pricein" placeholder="Cost price" required></div>
            <div class="col-md-3"><input class="form-control" type="number" min="0" step="1" name="pricesale" placeholder="Selling price" required></div>
            <div class="col-12"><textarea class="form-control" name="info" rows="3" maxlength="1024" placeholder="Description"></textarea></div>
            <div class="col-md-6"><input class="form-control" type="file" name="cover" accept="image/jpeg,image/png,image/gif,image/webp" required></div>
            <div class="col-md-6"><button class="btn btn-primary" type="submit">Add item</button></div>
        </form>
    </section>
</div>
