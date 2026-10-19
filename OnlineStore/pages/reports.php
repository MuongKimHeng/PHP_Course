<?php declare(strict_types=1);

if (!Tools::isAdmin()) {
    http_response_code(Tools::currentUser() === null ? 401 : 403);
    echo '<div class="alert alert-danger">Access denied. <a href="index.php?page=5">Log in</a> as an administrator.</div>';
    return;
}
?>
<h3>Sales reports</h3>

<form id="filter" class="row g-2 align-items-end mb-3">
    <div class="col-auto"><label class="form-label mb-0" for="from">From</label><input class="form-control" type="date" id="from"></div>
    <div class="col-auto"><label class="form-label mb-0" for="to">To</label><input class="form-control" type="date" id="to"></div>
    <div class="col-auto"><button class="btn btn-primary" type="submit">Show</button>
        <button class="btn btn-outline-secondary" type="button" id="reset">All time</button></div>
</form>

<div id="status" class="text-muted">Loading…</div>
<div id="report" hidden>
    <div class="row g-3 mb-4" id="cards"></div>
    <div class="row g-4">
        <div class="col-lg-6"><h5>By day</h5><table class="table table-sm" id="byDay"></table></div>
        <div class="col-lg-6"><h5>Top items</h5><table class="table table-sm" id="byItem"></table></div>
        <div class="col-12"><h5>Customers</h5><table class="table table-sm" id="byCustomer"></table></div>
    </div>
</div>

<script>
(() => {
    const money = (n) => '$ ' + Number(n).toLocaleString();

    // Report data contains user-controlled text (logins, item names), so everything is
    // inserted with textContent / createElement, never innerHTML.
    const el = (tag, text = '', cls = '') => {
        const e = document.createElement(tag);
        e.textContent = text;
        if (cls) e.className = cls;
        return e;
    };

    function card(label, value) {
        const col = el('div', '', 'col-6 col-md');
        const box = el('div', '', 'border rounded p-3 h-100');
        box.append(el('div', label, 'text-muted small'), el('div', value, 'fs-4'));
        col.append(box);
        return col;
    }

    function fillTable(table, headers, rows, barColumn = -1) {
        table.replaceChildren();
        const thead = table.createTHead().insertRow();
        headers.forEach((h) => thead.append(el('th', h)));

        const body = table.createTBody();
        if (rows.length === 0) {
            const td = el('td', 'No sales for this period.', 'text-muted');
            td.colSpan = headers.length;
            body.insertRow().append(td);
            return;
        }
        const max = barColumn >= 0 ? Math.max(...rows.map((r) => r[barColumn].raw), 1) : 1;
        for (const r of rows) {
            const tr = body.insertRow();
            r.forEach((cell, i) => {
                const td = el('td', cell.text);
                if (i === barColumn) {
                    const bar = el('div', '', 'bar mt-1');
                    bar.style.width = (cell.raw / max * 100) + '%';
                    td.append(bar);
                }
                tr.append(td);
            });
        }
    }
    const cell = (text, raw = 0) => ({ text, raw });

    function render(d) {
        const s = d.summary;
        document.getElementById('cards').replaceChildren(
            card('Items sold', String(s.sold)),
            card('Revenue', money(s.revenue)),
            card('Cost', money(s.cost)),
            card('Profit', money(s.profit)),
            card('Margin', s.margin + ' %'),
        );
        fillTable(document.getElementById('byDay'), ['Date', 'Sold', 'Revenue', 'Profit'],
            d.byDay.map((r) => [cell(r.date), cell(String(r.sold)), cell(money(r.revenue), r.revenue), cell(money(r.profit))]), 2);
        fillTable(document.getElementById('byItem'), ['Item', 'Sold', 'Revenue', 'Profit'],
            d.byItem.map((r) => [cell(r.item), cell(String(r.sold)), cell(money(r.revenue), r.revenue), cell(money(r.profit))]), 2);
        fillTable(document.getElementById('byCustomer'), ['Customer', 'Items', 'Spent', 'Last purchase'],
            d.byCustomer.map((r) => [cell(r.customer), cell(String(r.sold)), cell(money(r.revenue), r.revenue), cell(r.last)]), 2);
    }

    async function load() {
        const status = document.getElementById('status');
        const report = document.getElementById('report');
        const params = new URLSearchParams();
        for (const id of ['from', 'to']) {
            const v = document.getElementById(id).value;
            if (v) params.set(id, v);
        }
        status.textContent = 'Loading…';
        status.className = 'text-muted';
        try {
            const res = await fetch('pages/reports_data.php?' + params, { headers: { Accept: 'application/json' } });
            const data = await res.json();
            if (!res.ok) throw new Error(data.error ?? `HTTP ${res.status}`);
            render(data);
            status.textContent = '';
            report.hidden = false;
        } catch (err) {
            report.hidden = true;
            status.textContent = 'Could not load the report: ' + err.message;
            status.className = 'text-danger';
        }
    }

    document.getElementById('filter').addEventListener('submit', (e) => { e.preventDefault(); load(); });
    document.getElementById('reset').addEventListener('click', () => {
        document.getElementById('from').value = '';
        document.getElementById('to').value = '';
        load();
    });
    load();
})();
</script>
