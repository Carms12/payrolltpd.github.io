// deduction - extracted from views/admin/deduction.php

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

document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('payrollDropdown').classList.add('open');
    document.getElementById('dropdownArrow').classList.add('open');
});

function openAddModal(type) {
    document.getElementById('add_type').value = type;
    document.getElementById('add_type_display').value = type;
    const title = document.getElementById('addModalTitle');
    const colors = { 'SSS': '#fb923c', 'Pag-IBIG': '#34d399', 'PhilHealth': '#a78bfa' };
    title.innerHTML = '<i class="fas fa-plus-circle" style="color:' + (colors[type] || '#00ffff') + ';"></i> Add ' + type + ' Deduction';
    document.getElementById('addModal').classList.add('show');
}

function openEditModal(data) {
    document.getElementById('edit_id').value = data.id;
    document.getElementById('edit_type_hidden').value = data.deduction_type;
    document.getElementById('edit_type_display').value = data.deduction_type;
    document.getElementById('edit_month').value = data.deduction_month;
    document.getElementById('edit_amount').value = data.amount;
    document.getElementById('edit_notes').value = data.notes ?? '';
    document.getElementById('editModal').classList.add('show');
}

// --- Open the group edit modal (all 3 types at once) ---
function openEditGroup(data) {
    document.getElementById('editGroupTitle').innerHTML = '<i class="fas fa-pen" style="color:#00ffff;"></i> Edit ' + data.employee;
    document.getElementById('editGroupSubtitle').textContent = 'Update the three deduction amounts for ' + data.month + '.';

    // Fetch the employee_id from the row (data attribute set on form is cleaner but we only have name here,
    // so instead we look up the hidden selector by name in the current table)
    // We'll embed the employee_id in the data passed from PHP.
    const container = document.getElementById('editGroupFields');
    container.innerHTML = '';

    const types = [
        { key: 'SSS', color: '#fb923c', data: data.sss },
        { key: 'Pag-IBIG', color: '#34d399', data: data.pagibig },
        { key: 'PhilHealth', color: '#a78bfa', data: data.philhealth }
    ];

    types.forEach(function(t) {
        const group = document.createElement('div');
        group.className = 'form-group';
        group.innerHTML =
            '<label>' + t.key + ' Amount (₱)</label>' +
            '<input type="number" step="0.01" min="0" class="form-input" ' +
            'name="group_' + t.key.replace(/-/g, '_').toLowerCase() + '" ' +
            'value="' + (t.data.amount > 0 ? Number(t.data.amount).toFixed(2) : '0.00') + '" ' +
            'style="border-left: 3px solid ' + t.color + ';">';
        container.appendChild(group);
    });

    // Store metadata in hidden fields
    const existingMeta = document.getElementById('editGroupMeta');
    if (existingMeta) existingMeta.remove();

    const meta = document.createElement('div');
    meta.id = 'editGroupMeta';
    meta.style.display = 'none';
    meta.innerHTML =
        '<input type="hidden" name="group_employee_id" value="' + data.employee_id + '">' +
        '<input type="hidden" name="group_month" value="' + data.month + '">' +
        '<input type="hidden" name="group_sss_id" value="' + (data.sss.id ?? '') + '">' +
        '<input type="hidden" name="group_pagibig_id" value="' + (data.pagibig.id ?? '') + '">' +
        '<input type="hidden" name="group_philhealth_id" value="' + (data.philhealth.id ?? '') + '">';
    container.appendChild(meta);

    document.getElementById('editGroupModal').classList.add('show');
}

function openSettingsModal() {
    document.getElementById('settingsModal').classList.add('show');
}

function closeModal(id) {
    document.getElementById(id).classList.remove('show');
}

document.querySelectorAll('.modal-overlay').forEach(function(overlay) {
    overlay.addEventListener('click', function(e) {
        if (e.target === overlay) {
            overlay.classList.remove('show');
        }
    });
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        document.querySelectorAll('.modal-overlay.show').forEach(function(m) {
            m.classList.remove('show');
        });
    }
});
