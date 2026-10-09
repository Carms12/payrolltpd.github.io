// bonus - extracted from views/admin/bonus.php
// Page data is injected by the view as window.BONUS_DATA

// ================================================================
// DATA & STATE
// ================================================================

const bonusesData = window.BONUS_DATA.bonuses;
let isSubmitting = false;

console.log('Bonus page loaded successfully');
console.log('Bonuses data:', bonusesData);

// ================================================================
// BONUS MODAL FUNCTIONS
// ================================================================

function openBonusModal() {
    console.log('Opening add bonus modal');
    document.getElementById('bonusModal').classList.add('active');
    document.getElementById('modalTitle').textContent = 'Add New Bonus';
    document.getElementById('formAction').value = 'create';
    document.getElementById('bonusId').value = '';
    document.getElementById('employeeSelect').value = '';
    document.getElementById('bonusName').value = '';
    document.getElementById('reason').value = '';
    document.getElementById('amount').value = '';
    document.getElementById('submitBtn').innerHTML = 
        '<i class="fas fa-plus" style="font-size: 0.8rem;"></i> Add Bonus';
    document.getElementById('submitBtn').disabled = false;
    isSubmitting = false;
    document.body.style.overflow = 'hidden';
}

function editBonus(id) {
    console.log('Editing bonus with ID:', id);
    const bonus = bonusesData.find(b => b.bonus_id == id);

    if (bonus) {
        document.getElementById('bonusModal').classList.add('active');
        document.getElementById('modalTitle').textContent = 'Edit Bonus';
        document.getElementById('formAction').value = 'update';
        document.getElementById('bonusId').value = bonus.bonus_id;
        document.getElementById('employeeSelect').value = bonus.employee_id;
        document.getElementById('bonusName').value = bonus.bonus_name;
        document.getElementById('reason').value = bonus.reason;
        document.getElementById('amount').value = bonus.amount;
        document.getElementById('submitBtn').innerHTML = 
            '<i class="fas fa-save" style="font-size: 0.8rem;"></i> Update Bonus';
        document.getElementById('submitBtn').disabled = false;
        isSubmitting = false;
        document.body.style.overflow = 'hidden';
    } else {
        alert('Bonus not found. ID: ' + id);
    }
}

function closeBonusModal() {
    document.getElementById('bonusModal').classList.remove('active');
    document.body.style.overflow = '';
    isSubmitting = false;
    document.getElementById('submitBtn').disabled = false;
}

// ================================================================
// DELETE MODAL FUNCTIONS
// ================================================================

function deleteBonus(id, name, employeeName) {
    console.log('Deleting bonus:', id, name, employeeName);
    document.getElementById('deleteBonusId').value = id;
    document.getElementById('deleteBonusName').textContent = name;
    document.getElementById('deleteEmployeeName').textContent = 'Assigned to: ' + employeeName;
    document.getElementById('deleteModal').classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.remove('active');
    document.body.style.overflow = '';
}

// ================================================================
// FORM SUBMISSION HANDLING
// ================================================================

function handleSubmit() {
    if (isSubmitting) {
        return false;
    }

    isSubmitting = true;
    document.getElementById('submitBtn').disabled = true;
    document.getElementById('submitBtn').innerHTML = 'Processing...';
    return true;
}

// ================================================================
// MODAL CLOSE ON OUTSIDE CLICK
// ================================================================

document.getElementById('bonusModal').addEventListener('click', function(e) {
    if (e.target === this) closeBonusModal();
});

document.getElementById('deleteModal').addEventListener('click', function(e) {
    if (e.target === this) closeDeleteModal();
});

// ================================================================
// KEYBOARD SHORTCUTS
// ================================================================

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeBonusModal();
        closeDeleteModal();
    }
});

// ================================================================
// SIDEBAR FUNCTIONS
// ================================================================

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
