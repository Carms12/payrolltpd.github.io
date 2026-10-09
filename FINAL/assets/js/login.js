// Login page: show/hide password, Caps Lock hint, block double submit.
(function () {
    var form = document.getElementById('loginForm');
    var pwd = document.getElementById('password');
    var toggle = document.getElementById('togglePassword');
    var hint = document.getElementById('capsHint');
    var submit = form ? form.querySelector('button[type="submit"]') : null;

    if (toggle && pwd) {
        toggle.addEventListener('click', function () {
            var show = pwd.type === 'password';
            pwd.type = show ? 'text' : 'password';
            toggle.textContent = show ? 'Hide' : 'Show';
            toggle.setAttribute('aria-pressed', show ? 'true' : 'false');
            toggle.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
            pwd.focus();
        });
    }

    if (pwd && hint) {
        var caps = function (e) {
            hint.textContent = (e.getModifierState && e.getModifierState('CapsLock')) ? 'Caps Lock is on.' : '';
        };
        pwd.addEventListener('keyup', caps);
        pwd.addEventListener('keydown', caps);
        pwd.addEventListener('blur', function () { hint.textContent = ''; });
    }

    if (form && submit) {
        form.addEventListener('submit', function () {
            submit.disabled = true;
            submit.textContent = 'Signing in\u2026';
        });
        // coming back with the Back button: make the button usable again
        window.addEventListener('pageshow', function (e) {
            if (e.persisted) { submit.disabled = false; submit.textContent = 'Sign in'; }
        });
    }
})();
