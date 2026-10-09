// employees - extracted from views/admin/employees.php
// Page data is injected by the view as window.EMPLOYEES_DATA

const employeesData = window.EMPLOYEES_DATA.employees;
let isSubmitting = false;

function updateHourlyRate() {
    const positionSelect = document.getElementById('positionSelect');
    const hourlyRateInput = document.getElementById('hourlyRate');
    const selectedOption = positionSelect.options[positionSelect.selectedIndex];

    if (selectedOption && selectedOption.value) {
        const rate = selectedOption.getAttribute('data-rate');
        if (rate && parseFloat(rate) > 0) {
            hourlyRateInput.value = parseFloat(rate).toFixed(2);
            hourlyRateInput.style.borderColor = 'rgba(52, 211, 153, 0.3)';
        } else {
            hourlyRateInput.value = '';
            hourlyRateInput.style.borderColor = 'rgba(255, 0, 0, 0.2)';
            hourlyRateInput.placeholder = 'No rate set for this position';
        }
    } else {
        hourlyRateInput.value = '';
        hourlyRateInput.style.borderColor = '';
        hourlyRateInput.placeholder = 'Select a position first';
    }
}

// ---- Employee photo (preview in the form) ----
const PHOTO_PLACEHOLDER = '<i class="fas fa-user"></i>';

function showPhotoPreview(src) {
    const box = document.getElementById('photoPreview');
    box.innerHTML = src ? '<img src="' + src + '" alt="Photo preview">' : PHOTO_PLACEHOLDER;
}

// currentPhoto = URL of the photo saved in the database for this employee ('' if none)
function resetPhotoField(currentPhoto) {
    document.getElementById('photoInput').value = '';
    document.getElementById('removePhoto').checked = false;
    document.getElementById('photoRemoveWrap').style.display = currentPhoto ? 'flex' : 'none';
    showPhotoPreview(currentPhoto || '');
    window._currentPhoto = currentPhoto || '';
}

function previewPhoto(input) {
    const file = input.files && input.files[0];
    if (!file) { resetPhotoField(window._currentPhoto); return; }
    if (!/^image\/(jpeg|png|webp)$/.test(file.type)) {
        alert('Please choose a JPG, PNG or WEBP image.');
        input.value = ''; return;
    }
    if (file.size > 2 * 1024 * 1024) {
        alert('Photo is too large (max 2 MB).');
        input.value = ''; return;
    }
    document.getElementById('removePhoto').checked = false;
    const reader = new FileReader();
    reader.onload = function (e) { showPhotoPreview(e.target.result); };
    reader.readAsDataURL(file);
}

function toggleRemovePhoto(cb) {
    if (cb.checked) {
        document.getElementById('photoInput').value = '';
        showPhotoPreview('');
    } else {
        showPhotoPreview(window._currentPhoto || '');
    }
}

function openModal() {
    resetPhotoField('');
    const modal = document.getElementById('employeeModal');
    modal.classList.add('active');
    document.getElementById('modalTitle').textContent = 'Add New Employee';
    document.getElementById('formAction').value = 'create';
    document.getElementById('employeeId').value = '';
    document.getElementById('employeeCode').value = '';
    document.getElementById('firstName').value = '';
    document.getElementById('lastName').value = '';
    document.getElementById('positionSelect').value = '';
    document.getElementById('hourlyRate').value = '';
    document.getElementById('hourlyRate').placeholder = 'Select a position first';
    document.getElementById('statusSelect').value = '';
    document.getElementById('dateHired').value = '';
    document.getElementById('submitBtn').innerHTML = '<i class="fas fa-user-plus"></i> Add Employee';
    document.getElementById('submitBtn').disabled = false;
    isSubmitting = false;
    document.body.style.overflow = 'hidden';

    const today = new Date().toISOString().split('T')[0];
    document.getElementById('dateHired').value = today;

    setTimeout(() => document.getElementById('employeeCode').focus(), 100);
}

function openEditModal(id) {
    const employee = employeesData.find(e => e.employee_id === id);

    if (employee) {
        const modal = document.getElementById('employeeModal');
        modal.classList.add('active');
        document.getElementById('modalTitle').textContent = 'Edit Employee';
        document.getElementById('formAction').value = 'update';
        document.getElementById('employeeId').value = employee.employee_id;
        document.getElementById('employeeCode').value = employee.employee_code || '';
        document.getElementById('firstName').value = employee.first_name || '';
        document.getElementById('lastName').value = employee.last_name || '';
        document.getElementById('positionSelect').value = employee.position || '';
        document.getElementById('statusSelect').value = employee.status || '';
        document.getElementById('dateHired').value = employee.date_hired || '';
        resetPhotoField(employee.photo_v ? 'employee_photo.php?id=' + employee.employee_id + '&v=' + employee.photo_v : '');
        document.getElementById('submitBtn').innerHTML = '<i class="fas fa-pen"></i> Update Employee';
        document.getElementById('submitBtn').disabled = false;
        isSubmitting = false;
        document.body.style.overflow = 'hidden';

        updateHourlyRate();

        setTimeout(() => document.getElementById('employeeCode').focus(), 100);
    } else {
        alert('Failed to load employee data. Please refresh the page and try again.');
    }
}

function closeModal() {
    document.getElementById('employeeModal').classList.remove('active');
    document.body.style.overflow = '';
    isSubmitting = false;
    document.getElementById('submitBtn').disabled = false;
}

function openDeleteModal(id, name) {
    document.getElementById('deleteEmployeeId').value = id;
    document.getElementById('deleteEmployeeName').textContent = name;
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

document.getElementById('employeeModal').addEventListener('click', function(e) {
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

document.addEventListener('keydown', function(e) {
    if (e.key === 'Tab') {
        const activeModal = document.querySelector('.modal-overlay.active');
        if (!activeModal) return;

        const focusable = activeModal.querySelectorAll(
            'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
        );
        if (focusable.length === 0) return;

        const first = focusable[0];
        const last = focusable[focusable.length - 1];

        if (e.shiftKey && document.activeElement === first) {
            e.preventDefault();
            last.focus();
        } else if (!e.shiftKey && document.activeElement === last) {
            e.preventDefault();
            first.focus();
        }
    }
});
