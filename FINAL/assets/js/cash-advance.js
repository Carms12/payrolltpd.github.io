// cash-advance - extracted from views/admin/cash_advance.php
// Page data is injected by the view as window.CASH_ADVANCE_DATA

const cashAdvancesData = window.CASH_ADVANCE_DATA.cashAdvances;
let isSubmitting = false;

function openModal() {
    const modal = document.getElementById('cashAdvanceModal');
    modal.classList.add('active');
    document.getElementById('modalTitle').textContent = 'New Cash Advance';
    document.getElementById('formAction').value = 'create';
    document.getElementById('cashAdvanceId').value = '';
    document.getElementById('employeeSelect').value = '';
    document.getElementById('amountInput').value = '';
    document.getElementById('dateRequested').value = '';
    document.getElementById('statusSelect').value = 'Pending';
    document.getElementById('purposeInput').value = '';
    document.getElementById('remarksInput').value = '';
    document.getElementById('submitBtn').innerHTML = '<i class="fas fa-plus"></i> Create Cash Advance';
    document.getElementById('submitBtn').disabled = false;
    isSubmitting = false;
    document.body.style.overflow = 'hidden';

    const today = new Date().toISOString().split('T')[0];
    document.getElementById('dateRequested').value = today;

    setTimeout(() => document.getElementById('employeeSelect').focus(), 100);
}

function openEditModal(id) {
    const ca = cashAdvancesData.find(c => c.cash_advance_id === id);

    if (ca) {
        const modal = document.getElementById('cashAdvanceModal');
        modal.classList.add('active');
        document.getElementById('modalTitle').textContent = 'Edit Cash Advance';
        document.getElementById('formAction').value = 'update';
        document.getElementById('cashAdvanceId').value = ca.cash_advance_id;
        document.getElementById('employeeSelect').value = ca.employee_id;
        document.getElementById('amountInput').value = ca.amount;
        document.getElementById('dateRequested').value = ca.date_requested;
        document.getElementById('statusSelect').value = ca.status;
        document.getElementById('purposeInput').value = ca.purpose || '';
        document.getElementById('remarksInput').value = ca.remarks || '';
        document.getElementById('submitBtn').innerHTML = '<i class="fas fa-pen"></i> Update Cash Advance';
        document.getElementById('submitBtn').disabled = false;
        isSubmitting = false;
        document.body.style.overflow = 'hidden';

        setTimeout(() => document.getElementById('employeeSelect').focus(), 100);
    } else {
        alert('Failed to load cash advance data. Please refresh the page and try again.');
    }
}

function closeModal() {
    document.getElementById('cashAdvanceModal').classList.remove('active');
    document.body.style.overflow = '';
    isSubmitting = false;
    document.getElementById('submitBtn').disabled = false;
}

function openDeleteModal(id, name) {
    document.getElementById('deleteCashAdvanceId').value = id;
    document.getElementById('deleteItemName').textContent = name;
    document.getElementById('deleteModal').classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.remove('active');
    document.body.style.overflow = '';
}

function preventDuplicateSubmit() {
    if (isSubmitting) {
        return false;
    }
    isSubmitting = true;
    const btn = document.getElementById('submitBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
    return true;
}

document.getElementById('cashAdvanceModal').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});

document.getElementById('deleteModal').addEventListener('click', function(e) {
    if (e.target === this) closeDeleteModal();
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeModal();
        closeDeleteModal();
    }
});

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
