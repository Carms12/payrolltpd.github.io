// attendance - extracted from views/admin/attendance.php
// Page data is injected by the view as window.ATTENDANCE_DATA

const data = window.ATTENDANCE_DATA.records;
let submitting = false;
let existingAttendance = window.ATTENDANCE_DATA.existing;

function changePerPage(perPage) {
    const url = new URL(window.location.href);
    url.searchParams.set('per_page', perPage);
    url.searchParams.set('page', 1);
    window.location.href = url.toString();
}

function toggleTimeFields() {
    const status = document.getElementById('statusSelect').value;
    const timeFields = document.getElementById('timeFields');
    const timeIn = document.getElementById('timeIn');
    const timeOut = document.getElementById('timeOut');
    const timeInRequired = document.getElementById('timeInRequired');
    const timeOutRequired = document.getElementById('timeOutRequired');

    if (status === 'Absent') {
        timeFields.style.display = 'none';
        timeIn.required = false;
        timeOut.required = false;
        timeInRequired.style.display = 'none';
        timeOutRequired.style.display = 'none';
        timeIn.value = '';
        timeOut.value = '';
    } else {
        timeFields.style.display = 'grid';
        timeIn.required = true;
        timeOut.required = true;
        timeInRequired.style.display = 'inline';
        timeOutRequired.style.display = 'inline';
    }
}

// Employees already recorded for the page date are disabled in the dropdown (to block duplicates on ADD).
// When EDITING, that employee's own option must be selectable, otherwise the form can't be saved.
function setEmployeeOptionsForEdit(employeeId) {
    const sel = document.getElementById('empSelect');
    Array.from(sel.options).forEach(function (opt) {
        if (opt.value === '') return;
        if (opt.dataset.recorded === undefined) opt.dataset.recorded = opt.disabled ? '1' : '0';
        opt.disabled = (employeeId === null) ? opt.dataset.recorded === '1' : false;
    });
}

function openModal() {
    setEmployeeOptionsForEdit(null);
    document.getElementById('modal').classList.add('active');
    document.getElementById('modalTitle').textContent = 'Add Attendance';
    document.getElementById('formAction').value = 'create';
    document.getElementById('attendanceId').value = '';
    document.getElementById('empSelect').value = '';
    document.getElementById('attDate').value = window.ATTENDANCE_DATA.filterDate;
    document.getElementById('statusSelect').value = 'Present';
    document.getElementById('timeIn').value = '';
    document.getElementById('timeOut').value = '';
    document.getElementById('remarks').value = '';
    document.getElementById('submitBtn').innerHTML = '<i class="fas fa-save" style="font-size: 0.8rem;"></i> Save';
    submitting = false;
    toggleTimeFields();
    document.body.style.overflow = 'hidden';
}

function editRecord(id) {
    const r = data.find(a => a.attendance_id == id);
    if (r) {
        setEmployeeOptionsForEdit(r.employee_id);
        document.getElementById('modal').classList.add('active');
        document.getElementById('modalTitle').textContent = 'Edit Attendance';
        document.getElementById('formAction').value = 'update';
        document.getElementById('attendanceId').value = r.attendance_id;
        document.getElementById('empSelect').value = r.employee_id;
        document.getElementById('attDate').value = r.attendance_date;
        document.getElementById('statusSelect').value = r.status;
        document.getElementById('timeIn').value = r.time_in ? r.time_in.substring(0, 5) : '';
        document.getElementById('timeOut').value = r.time_out ? r.time_out.substring(0, 5) : '';
        document.getElementById('remarks').value = r.remarks || '';
        document.getElementById('submitBtn').innerHTML = '<i class="fas fa-save" style="font-size: 0.8rem;"></i> Update';
        submitting = false;
        toggleTimeFields();
        document.body.style.overflow = 'hidden';
    }
}

function closeModal() { document.getElementById('modal').classList.remove('active'); document.body.style.overflow = ''; }
function openBulkModal() { document.getElementById('bulkModal').classList.add('active'); document.body.style.overflow = 'hidden'; }
function closeBulkModal() { document.getElementById('bulkModal').classList.remove('active'); document.body.style.overflow = ''; }

function openImportModal() { document.getElementById('importModal').classList.add('active'); document.body.style.overflow = 'hidden'; }
function closeImportModal() { document.getElementById('importModal').classList.remove('active'); document.body.style.overflow = ''; }

function deleteRecord(id, name) {
    if (confirm('Delete attendance record for ' + name + '?')) {
        const f = document.createElement('form');
        f.method = 'POST';
        f.innerHTML = '<input type="hidden" name="action" value="delete"><input type="hidden" name="attendance_id" value="'+id+'"><input type="hidden" name="submission_token" value="' + window.ATTENDANCE_DATA.submissionToken + '">';
        document.body.appendChild(f); f.submit();
    }
}

function handleSubmit() { 
    if (submitting) return false; 
    submitting = true; 
    document.getElementById('submitBtn').disabled = true; 
    document.getElementById('submitBtn').innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
    return true; 
}

function validateCSV() {
    const fileInput = document.getElementById('csvFile');
    const file = fileInput.files[0];
    if (!file) {
        alert('Please select a file to import.');
        return false;
    }

    const fileSize = file.size / 1024 / 1024;
    if (fileSize > 10) {
        alert('File size exceeds 10MB limit. Please compress or split your file.');
        return false;
    }

    const extension = file.name.split('.').pop().toLowerCase();
    if (!['csv', 'txt', 'xls'].includes(extension)) {
        alert('Please upload a CSV, TXT or XLS file.');
        return false;
    }

    document.getElementById('importBtn').innerHTML = '<i class="fas fa-spinner fa-spin"></i> Importing...';
    document.getElementById('importBtn').disabled = true;
    return true;
}

document.getElementById('modal').addEventListener('click', function(e) { if (e.target === this) closeModal(); });
document.getElementById('bulkModal').addEventListener('click', function(e) { if (e.target === this) closeBulkModal(); });
document.getElementById('importModal').addEventListener('click', function(e) { if (e.target === this) closeImportModal(); });
document.addEventListener('keydown', function(e) { if (e.key === 'Escape') { closeModal(); closeBulkModal(); closeImportModal(); } });

function toggleSidebar() { document.getElementById('sidebar').classList.toggle('open'); document.getElementById('overlay').classList.toggle('active'); }
function toggleDropdown() { document.getElementById('payrollDropdown').classList.toggle('open'); document.getElementById('dropdownArrow').classList.toggle('open'); }
