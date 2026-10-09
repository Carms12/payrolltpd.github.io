// mobile.js - shared mobile helpers for all admin pages (loaded after each page's own script).
// 1) top bar with the page title   2) drawer: close on link / overlay / Esc, lock page scroll
// 3) turn data tables into cards on phones (labels come from the <thead>)
(function () {
    'use strict';
    var mq = window.matchMedia('(max-width: 1024px)');

    /* ---------- 1. top bar ---------- */
    function buildTopbar() {
        if (document.querySelector('.m-topbar')) return;
        var h1 = document.querySelector('.page-header h1');
        var bar = document.createElement('div');
        bar.className = 'm-topbar';
        var t = document.createElement('div');
        t.className = 'm-title';
        t.textContent = h1 ? h1.textContent.replace(/\s+/g, ' ').trim() : document.title;
        var b = document.createElement('div');
        b.className = 'm-brand';
        b.innerHTML = 'TPD<b>Solutions</b>';
        bar.appendChild(t); bar.appendChild(b);
        document.body.insertBefore(bar, document.body.firstChild);
    }

    /* ---------- 2. drawer behaviour ---------- */
    function setupDrawer() {
        var sidebar = document.getElementById('sidebar');
        var overlay = document.getElementById('overlay');
        if (!sidebar) return;
        function sync() { document.body.classList.toggle('m-lock', mq.matches && sidebar.classList.contains('open')); }
        new MutationObserver(sync).observe(sidebar, { attributes: true, attributeFilter: ['class'] });
        function close() {
            sidebar.classList.remove('open');
            if (overlay) overlay.classList.remove('active');
            sync();
        }
        // tapping a real link closes the drawer (dropdown headers have no href)
        sidebar.addEventListener('click', function (e) {
            var a = e.target.closest ? e.target.closest('a[href]') : null;
            if (a && mq.matches) close();
        });
        document.addEventListener('keydown', function (e) { if (e.key === 'Escape') close(); });
        // swipe left on the open drawer to close it
        var x0 = null;
        sidebar.addEventListener('touchstart', function (e) { x0 = e.touches[0].clientX; }, { passive: true });
        sidebar.addEventListener('touchend', function (e) {
            if (x0 !== null && x0 - e.changedTouches[0].clientX > 60) close();
            x0 = null;
        }, { passive: true });
        mq.addListener ? mq.addListener(function () { if (!mq.matches) close(); }) : null;
        // keep the active nav item visible inside the drawer
        var act = sidebar.querySelector('.nav-item.active');
        if (act && act.scrollIntoView) { try { act.scrollIntoView({ block: 'nearest' }); } catch (e) {} }
    }

    /* ---------- 3. tables -> cards ---------- */
    function cellText(el) { return (el.textContent || '').replace(/\s+/g, ' ').trim(); }

    function prepareTable(table) {
        if (table.dataset.mReady || table.closest('.modal')) return;   // tables inside modals keep the scroller
        var thead = table.tHead;
        if (!thead || !thead.rows.length) return;
        var headRow = thead.rows[thead.rows.length - 1];
        // only simple headers (one header row, no colspan/rowspan) become cards
        if (thead.rows.length > 1 || thead.querySelector('th[colspan], th[rowspan]')) return;
        var labels = Array.prototype.map.call(headRow.cells, cellText);
        var actionsIdx = -1, hideIdx = [];
        labels.forEach(function (l, i) {
            if (/^actions?$/i.test(l)) actionsIdx = i;
            if (l === '#') hideIdx.push(i);
        });

        Array.prototype.forEach.call(table.tBodies, function (tb) {
            Array.prototype.forEach.call(tb.rows, function (tr) {
                var cells = Array.prototype.slice.call(tr.cells);
                if (cells.some(function (c) { return c.colSpan > 1; })) {          // totals / empty state
                    tr.classList.add('m-full-row');
                    return;
                }
                var first = true;
                cells.forEach(function (td, i) {
                    var label = labels[i] || '';
                    if (hideIdx.indexOf(i) > -1) { td.classList.add('m-hide'); return; }
                    if (first) { td.classList.add('m-title-cell'); first = false; return; }
                    if (i === actionsIdx || /^(payslip|save|edit|view)$/i.test(label)) { td.classList.add('m-actions'); return; }
                    if (label) td.setAttribute('data-label', label);
                    if (/reason|purpose|remarks|note|description/i.test(label)) td.classList.add('m-wide');
                });
            });
        });
        // footer rows with colspan (grand totals) behave the same way
        if (table.tFoot) Array.prototype.forEach.call(table.tFoot.rows, function (tr) { tr.classList.add('m-full-row'); });

        table.classList.add('m-cards');
        var wrap = table.closest('.table-container');
        if (wrap) wrap.classList.add('m-cards-wrap');
        table.dataset.mReady = '1';
    }

    function init() {
        buildTopbar();
        setupDrawer();
        Array.prototype.forEach.call(document.querySelectorAll('.table-container table, .glass-card table'), prepareTable);
    }
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init); else init();
})();
