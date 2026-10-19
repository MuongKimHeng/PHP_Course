<?php declare(strict_types=1); ?>
<select id="catid" class="form-select w-auto mb-3">
    <option value="0">Select category...</option>
    <?php foreach (Category::all() as $c): ?>
        <option value="<?= $c->id ?>"><?= Tools::e($c->category) ?></option>
    <?php endforeach; ?>
</select>

<div id="result">
    <?php foreach (Item::getItems() as $item) {
        $item->draw();
    } ?>
</div>

<script>
    document.getElementById('catid').addEventListener('change', async (e) => {
        const result = document.getElementById('result');
        try {
            const res = await fetch('pages/lists.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({ cat: e.target.value }),
            });
            if (!res.ok) throw new Error(`HTTP ${res.status}`);
            result.innerHTML = await res.text();   // server-side escaped HTML
        } catch (err) {
            result.textContent = 'Could not load the items. Please try again.';
        }
    });
</script>
