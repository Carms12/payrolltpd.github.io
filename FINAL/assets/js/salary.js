// salary - extracted from views/admin/salary.php
// Page data is injected by the view as window.SALARY_PAGE

function togglePrintDropdown(event) {
    event.stopPropagation();
    document.getElementById('printDropdownMenu').classList.toggle('show');
    document.getElementById('printArrow').classList.toggle('open');
}

document.addEventListener('click', function(e) {
    const dropdown = document.querySelector('.print-dropdown');
    if (!dropdown.contains(e.target)) {
        document.getElementById('printDropdownMenu').classList.remove('show');
        document.getElementById('printArrow').classList.remove('open');
    }
});

function exportDocument(type, mode) {
    document.getElementById('printDropdownMenu').classList.remove('show');
    document.getElementById('printArrow').classList.remove('open');

    const loading = document.getElementById('exportLoading');
    loading.classList.add('show');

    const params = new URLSearchParams(window.location.search);
    const custom_start = params.get('custom_start') || window.SALARY_PAGE.customStart;
    const custom_end = params.get('custom_end') || window.SALARY_PAGE.customEnd;
    const preset = params.get('preset') || '';

    let url = '?action=print&format=' + type + '&custom_start=' + custom_start + '&custom_end=' + custom_end;
    if (preset) url += '&preset=' + preset;
    if (mode) url += '&mode=' + mode;
    if (type === 'pdf' || type === 'docx') url += '&auto_print=1';

    if (type === 'excel') {
        const excelUrl = '?action=print&format=excel&custom_start=' + custom_start + '&custom_end=' + custom_end + (preset ? '&preset=' + preset : '');
        const win = window.open(excelUrl, '_blank');
        if (!win) window.location.href = excelUrl;
        setTimeout(function() { loading.classList.remove('show'); }, 2000);
    } else {
        const win = window.open(url, '_blank');
        if (!win) window.location.href = url;
        setTimeout(function() { loading.classList.remove('show'); }, 1500);
    }
}

function toggleSidebar() {
    document.getElementById('sidebar').classList.toggle('open');
    document.getElementById('overlay').classList.toggle('active');
}

function toggleDropdown() {
    const dropdown = document.getElementById('payrollDropdown');
    const arrow = document.getElementById('dropdownArrow');
    dropdown.classList.toggle('open');
    arrow.classList.toggle('open');
}

document.addEventListener('click', function(e) {
    const dropdown = document.getElementById('payrollDropdown');
    const arrow = document.getElementById('dropdownArrow');
    const navItem = e.target.closest('.nav-item');
    if (navItem && navItem.querySelector('#dropdownArrow')) return;
    if (!dropdown.contains(e.target) && !e.target.closest('#dropdownArrow')) {
        dropdown.classList.remove('open');
        arrow.classList.remove('open');
    }
});
