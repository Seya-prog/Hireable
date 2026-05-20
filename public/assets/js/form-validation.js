/**
 * Client-side form validation for any form with novalidate attribute.
 * 
 * Usage:
 *   1. Add "novalidate" to your <form>
 *   2. Add "required" to fields that must be filled
 *   3. Add data-error="Your message" for custom error text
 *   4. Wrap inputs in a .field or .row container
 *   5. Include this script: <script src="../assets/js/form-validation.js"></script>
 */

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('form[novalidate]').forEach(form => {

        // Clear error when user types or changes a field
        form.querySelectorAll('input, select, textarea').forEach(field => {
            field.addEventListener('input', () => {
                const error = field.closest('.field, .row')?.querySelector('.field-error.client');
                if (error) error.remove();
            });
            // Also handle select change event
            field.addEventListener('change', () => {
                const error = field.closest('.field, .row')?.querySelector('.field-error.client');
                if (error) error.remove();
            });
        });

        // Validate on submit
        form.addEventListener('submit', (e) => {
            // Remove old client-side errors
            form.querySelectorAll('.field-error.client').forEach(el => el.remove());

            let firstInvalid = null;

            form.querySelectorAll('[required]').forEach(field => {
                if (!field.checkValidity()) {
                    e.preventDefault();
                    const parent = field.closest('.field') || field.closest('.row');
                    if (parent && !parent.querySelector('.field-error')) {
                        const msg = field.dataset.error || 'This field is required';
                        const p = document.createElement('p');
                        p.className = 'field-error client';
                        p.textContent = '\u26a0 ' + msg;
                        parent.appendChild(p);
                    }
                    if (!firstInvalid) firstInvalid = field;
                }
            });

            if (firstInvalid) firstInvalid.focus();
        });
    });
});
