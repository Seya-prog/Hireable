/**
 * Toast auto-dismiss and close button
 */
document.addEventListener('DOMContentLoaded', function() {
    var toast = document.getElementById('toast-message');
    if (!toast) return;

    // Close button
    var closeBtn = toast.querySelector('.toast-close');
    if (closeBtn) {
        closeBtn.addEventListener('click', function() {
            toast.remove();
        });
    }

    // Auto-dismiss after 4 seconds
    setTimeout(function() {
        if (toast) {
            toast.classList.add('toast--hide');
            setTimeout(function() { toast.remove(); }, 300);
        }
    }, 4000);
});
