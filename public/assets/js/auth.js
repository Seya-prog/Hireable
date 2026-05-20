/**
 * Auth page interactions
 * - Password visibility toggle
 * - Signup role toggle (employee/employer dynamic copy)
 */
document.addEventListener('DOMContentLoaded', function() {

    // Password toggle
    var toggleBtn = document.querySelector('.auth-toggle-pw');
    if (toggleBtn) {
        toggleBtn.addEventListener('click', function() {
            var pw = document.getElementById('password');
            var icon = document.getElementById('pw-toggle-icon');
            if (pw.type === 'password') {
                pw.type = 'text';
                icon.textContent = 'visibility_off';
            } else {
                pw.type = 'password';
                icon.textContent = 'visibility';
            }
        });
    }

    // Role toggle (signup page)
    var radios = document.querySelectorAll('.auth-role-radio');
    radios.forEach(function(r) {
        r.addEventListener('change', function() {
            var isEmployer = document.getElementById('role-employer').checked;
            document.querySelectorAll('.employee-copy').forEach(function(el) {
                el.style.display = isEmployer ? 'none' : 'inline';
            });
            document.querySelectorAll('.employer-copy').forEach(function(el) {
                el.style.display = isEmployer ? 'inline' : 'none';
            });
        });
    });

});
