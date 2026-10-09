// position - extracted from views/admin/position.php
// Page data is injected by the view as window.POSITIONS_DATA

const positionsData = window.POSITIONS_DATA.positions;
let isSubmitting = false;

function preventDuplicateSubmit() {
    if (isSubmitting) {
        return false;
    }
    isSubmitting = true;
    document.getElementById('submitBtn').disabled = true;
    document.getElementById('submitBtn').innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="fa-spin" style="margin-right: 4px;"><line x1="12" y1="2" x2="12" y2="6"></line><line x1="12" y1="18" x2="12" y2="22"></line><line x1="4.93" y1="4.93" x2="7.76" y2="7.76"></line><line x1="16.24" y1="16.24" x2="19.07" y2="19.07"></line><line x1="2" y1="12" x2="6" y2="12"></line><line x1="18" y1="12" x2="22" y2="12"></line><line x1="4.93" y1="19.07" x2="7.76" y2="16.24"></line><line x1="16.24" y1="7.76" x2="19.07" y2="4.93"></line></svg> Processing...';
    return true;
}

function openModal() {
    document.getElementById('positionModal').classList.add('active');
    document.getElementById('modalTitle').textContent = 'Add New Position';
    document.getElementById('formAction').value = 'create';
    document.getElementById('positionId').value = '';
    document.getElementById('positionName').value = '';
    document.getElementById('hourlyRate').value = '';
    document.getElementById('submitBtn').innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 4px;"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg> Add Position';
    document.getElementById('submitBtn').disabled = false;
    isSubmitting = false;
    document.body.style.overflow = 'hidden';
}

function openEditModal(id) {
    const position = positionsData.find(p => p.position_id === id);

    if (position) {
        document.getElementById('positionModal').classList.add('active');
        document.getElementById('modalTitle').textContent = 'Edit Position';
        document.getElementById('formAction').value = 'update';
        document.getElementById('positionId').value = position.position_id;
        document.getElementById('positionName').value = position.position_name;
        document.getElementById('hourlyRate').value = position.hourly_rate;
        document.getElementById('submitBtn').innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 4px;"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg> Update Position';
        document.getElementById('submitBtn').disabled = false;
        isSubmitting = false;
        document.body.style.overflow = 'hidden';
    } else {
        alert('Failed to load position data.');
    }
}

function closeModal() {
    document.getElementById('positionModal').classList.remove('active');
    document.body.style.overflow = '';
    isSubmitting = false;
    document.getElementById('submitBtn').disabled = false;
}

function openDeleteModal(id, name) {
    document.getElementById('deletePositionId').value = id;
    document.getElementById('deletePositionName').textContent = name;
    document.getElementById('deleteModal').classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.remove('active');
    document.body.style.overflow = '';
}

document.getElementById('positionModal').addEventListener('click', function(e) {
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
