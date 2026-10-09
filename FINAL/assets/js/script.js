// ===== PASSWORD TOGGLE =====
function togglePassword() {
    const pwd = document.getElementById('password');
    const icon = document.getElementById('toggleIcon');
    if (pwd.type === 'password') {
        pwd.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        pwd.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}

// ===== AUTO-HIDE ERROR MESSAGES =====
document.addEventListener('DOMContentLoaded', function() {
    // Auto-hide error messages after 5 seconds
    const errorDiv = document.querySelector('.bg-red-50');
    if (errorDiv) {
        setTimeout(function() {
            errorDiv.style.transition = 'all 0.5s ease';
            errorDiv.style.opacity = '0';
            errorDiv.style.transform = 'translateY(-10px)';
            setTimeout(function() {
                errorDiv.style.display = 'none';
            }, 500);
        }, 5000);
    }

    // Auto-hide success messages after 5 seconds
    const successDiv = document.querySelector('.bg-green-50');
    if (successDiv) {
        setTimeout(function() {
            successDiv.style.transition = 'all 0.5s ease';
            successDiv.style.opacity = '0';
            successDiv.style.transform = 'translateY(-10px)';
            setTimeout(function() {
                successDiv.style.display = 'none';
            }, 500);
        }, 5000);
    }

    // Smooth form focus effects
    const inputs = document.querySelectorAll('input');
    inputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.parentElement.classList.add('ring-2', 'ring-blue-200');
        });
        input.addEventListener('blur', function() {
            this.parentElement.classList.remove('ring-2', 'ring-blue-200');
        });
    });
});

// ===== PREVENT FORM DOUBLE SUBMISSION =====
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Signing In...';
                setTimeout(function() {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fa-solid fa-sign-in-alt"></i> Sign In to Dashboard';
                }, 3000);
            }
        });
    }
});

// ===== LOGOUT CONFIRMATION =====
function confirmLogout() {
    return confirm('Are you sure you want to logout?');
}