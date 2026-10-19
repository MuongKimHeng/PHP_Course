<?php /* catalog.php */ ?>
<select id="catid">
    <option value="0">Select category...</option>
    <?php foreach (Tools::connect()->query('SELECT * FROM categories') as $row): ?>
        <option value="<?= (int) $row['id'] ?>"><?= htmlspecialchars($row['category']) ?></option>
    <?php endforeach; ?>
</select>

<div id="result">
    <?php foreach (Item::getItems() as $item) {
        $item->draw();
    } ?>
</div>

<script>
    document.getElementById('catid').addEventListener('change', async (e) => {
        const res = await fetch('pages/lists.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({ cat: e.target.value }),
        });
        document.getElementById('result').innerHTML = await res.text();
    });
    
</script>