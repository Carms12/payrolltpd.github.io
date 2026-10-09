// incentives - extracted from views/admin/incentives.php
// Page data is injected by the view as window.INCENTIVES_DATA

const incentivesData = window.INCENTIVES_DATA.incentives;
let isSubmitting = false;

console.log('Incentives page loaded successfully');

function openIncentiveModal() {
    console.log('Opening add incentive modal');
    document.getElementById('incentiveModal').classList.add('active');
    document.getElementById('modalTitle').textContent = 'Add New Incentive';
    document.getElementById('formAction').value = 'create';
    document.getElementById('incentiveId').value = '';
    document.getElementById('employeeSelect').value = '';
    document.getElementById('incentiveName').value = '';
    document.getElementById('reason').value = '';
    document.getElementById('amount').value = '';
    document.getElementById('submitBtn').innerHTML = '<i class="fas fa-plus" style="font-size: 0.8rem;"></i> Add Incentive';
    document.getElementById('submitBtn').disabled = false;
    isSubmitting = false;
    document.body.style.overflow = 'hidden';
}

function editIncentive(id) {
    console.log('Editing incentive with ID:', id);
    const incentive = incentivesData.find(i => i.incentive_id == id);

    if (incentive) {
        document.getElementById('incentiveModal').classList.add('active');
        document.getElementById('modalTitle').textContent = 'Edit Incentive';
        document.getElementById('formAction').value = 'update';
        document.getElementById('incentiveId').value = incentive.incentive_id;
        document.getElementById('employeeSelect').value = incentive.employee_id;
        document.getElementById('incentiveName').value = incentive.incentive_name;
        document.getElementById('reason').value = incentive.reason;
        document.getElementById('amount').value = incentive.amount;
        document.getElementById('submitBtn').innerHTML = '<i class="fas fa-save" style="font-size: 0.8rem;"></i> Update Incentive';
        document.getElementById('submitBtn').disabled = false;
        isSubmitting = false;
        document.body.style.overflow = 'hidden';
    } else {
        alert('Incentive not found. ID: ' + id);
    }
}

function closeIncentiveModal() {
    document.getElementById('incentiveModal').classList.remove('active');
    document.body.style.overflow = '';
    isSubmitting = false;
    document.getElementById('submitBtn').disabled = false;
}

function deleteIncentive(id, name, employeeName) {
    console.log('Deleting incentive:', id, name, employeeName);
    document.getElementById('deleteIncentiveId').value = id;
    document.getElementById('deleteIncentiveName').textContent = name;
    document.getElementById('deleteEmployeeName').textContent = 'Assigned to: ' + employeeName;
    document.getElementById('deleteModal').classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.remove('active');
    document.body.style.overflow = '';
}

function handleSubmit() {
    if (isSubmitting) {
        return false;
    }

    isSubmitting = true;
    document.getElementById('submitBtn').disabled = true;
    document.getElementById('submitBtn').innerHTML = 'Processing...';
    return true;
}

document.getElementById('incentiveModal').addEventListener('click', function(e) {
    if (e.target === this) closeIncentiveModal();
});

document.getElementById('deleteModal').addEventListener('click', function(e) {
    if (e.target === this) closeDeleteModal();
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeIncentiveModal();
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
