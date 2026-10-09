// dashboard - extracted from views/admin/dashboard.php

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
