/**
 * Custom Select — Replaces native <select> with styled dropdowns
 * Uses inline styles to guarantee rendering regardless of CSS loading
 */
document.addEventListener('DOMContentLoaded', function () {
    'use strict';

    const SELECTORS = '.emp-filter-select, select.assess-select, select.auth-select, select.profile-field-select';

    document.querySelectorAll(SELECTORS).forEach(function(nativeSelect) {
        if (nativeSelect.dataset.customized) return;
        nativeSelect.dataset.customized = 'true';

        // Preserve any required/name attributes for form submission
        var isFormSelect = nativeSelect.closest('form') !== null;

        // Build wrapper
        var wrapper = document.createElement('div');
        wrapper.style.cssText = 'position:relative;display:inline-block;font-family:Manrope,sans-serif;min-width:' + 
            (nativeSelect.style.maxWidth || '200px') + ';';
        if (nativeSelect.style.maxWidth) wrapper.style.maxWidth = nativeSelect.style.maxWidth;

        // Current value display button
        var display = document.createElement('button');
        display.type = 'button';
        display.style.cssText = 'display:flex;align-items:center;justify-content:space-between;gap:0.75rem;width:100%;' +
            'padding:0.6rem 0.9rem;background:#fff;border:1px solid rgba(122,107,90,0.25);border-radius:10px;' +
            'font-family:Manrope,sans-serif;font-size:0.85rem;font-weight:500;color:#3b2f1e;cursor:pointer;' +
            'transition:all 0.2s ease;outline:none;text-align:left;white-space:nowrap;';

        var selectedOpt = nativeSelect.options[nativeSelect.selectedIndex];
        var displayText = document.createElement('span');
        displayText.textContent = selectedOpt ? selectedOpt.textContent : '';
        displayText.style.cssText = 'overflow:hidden;text-overflow:ellipsis;';

        var arrow = document.createElement('span');
        arrow.style.cssText = 'width:0;height:0;border-left:4px solid transparent;border-right:4px solid transparent;' +
            'border-top:5px solid #7a6b5a;transition:transform 0.2s ease;flex-shrink:0;';

        display.appendChild(displayText);
        display.appendChild(arrow);

        // Dropdown list
        var dropdown = document.createElement('div');
        dropdown.style.cssText = 'position:absolute;top:calc(100% + 6px);left:0;right:0;min-width:100%;max-height:240px;' +
            'overflow-y:auto;background:#fff;border:1px solid rgba(122,107,90,0.2);border-radius:12px;' +
            'box-shadow:0 8px 24px rgba(59,47,30,0.12),0 2px 8px rgba(59,47,30,0.06);z-index:9999;' +
            'opacity:0;visibility:hidden;transform:translateY(-8px);transition:all 0.2s ease;padding:4px;';

        var isOpen = false;

        function buildOptions() {
            dropdown.innerHTML = '';
            Array.from(nativeSelect.options).forEach(function(opt, idx) {
                var item = document.createElement('div');
                var isActive = idx === nativeSelect.selectedIndex;
                item.textContent = opt.textContent;
                item.style.cssText = 'padding:0.55rem 0.85rem;font-size:0.85rem;color:#3b2f1e;cursor:pointer;' +
                    'border-radius:8px;transition:background 0.15s ease;white-space:nowrap;' +
                    (isActive ? 'background:rgba(138,91,28,0.1);color:#8a5b1c;font-weight:600;' : '');

                item.addEventListener('mouseenter', function() {
                    if (!isActive) item.style.background = 'rgba(138,91,28,0.06)';
                });
                item.addEventListener('mouseleave', function() {
                    if (!isActive) item.style.background = '';
                });

                item.addEventListener('click', function(e) {
                    e.stopPropagation();
                    nativeSelect.selectedIndex = idx;
                    nativeSelect.dispatchEvent(new Event('change', { bubbles: true }));
                    displayText.textContent = opt.textContent;
                    closeDropdown();
                    buildOptions(); // Refresh active states
                });
                dropdown.appendChild(item);
            });
        }

        function openDropdown() {
            isOpen = true;
            dropdown.style.opacity = '1';
            dropdown.style.visibility = 'visible';
            dropdown.style.transform = 'translateY(0)';
            arrow.style.transform = 'rotate(180deg)';
            display.style.borderColor = '#8a5b1c';
            display.style.boxShadow = '0 0 0 3px rgba(138,91,28,0.1)';
        }

        function closeDropdown() {
            isOpen = false;
            dropdown.style.opacity = '0';
            dropdown.style.visibility = 'hidden';
            dropdown.style.transform = 'translateY(-8px)';
            arrow.style.transform = 'rotate(0deg)';
            display.style.borderColor = 'rgba(122,107,90,0.25)';
            display.style.boxShadow = 'none';
        }

        display.addEventListener('click', function(e) {
            e.stopPropagation();
            if (isOpen) closeDropdown();
            else {
                // Close all other custom selects first
                document.querySelectorAll('[data-cs-open="true"]').forEach(function(w) {
                    w.querySelector('button').click();
                });
                openDropdown();
                wrapper.dataset.csOpen = 'true';
            }
        });

        display.addEventListener('mouseenter', function() {
            if (!isOpen) {
                display.style.borderColor = 'rgba(138,91,28,0.4)';
                display.style.boxShadow = '0 1px 4px rgba(138,91,28,0.08)';
            }
        });
        display.addEventListener('mouseleave', function() {
            if (!isOpen) {
                display.style.borderColor = 'rgba(122,107,90,0.25)';
                display.style.boxShadow = 'none';
            }
        });

        buildOptions();

        // Insert into DOM
        nativeSelect.style.display = 'none';
        nativeSelect.parentNode.insertBefore(wrapper, nativeSelect);
        wrapper.appendChild(display);
        wrapper.appendChild(dropdown);
        wrapper.appendChild(nativeSelect);

        // Close on outside click
        document.addEventListener('click', function() {
            if (isOpen) {
                closeDropdown();
                wrapper.dataset.csOpen = 'false';
            }
        });
    });

    // Close all on Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('[data-cs-open="true"]').forEach(function(w) {
                var btn = w.querySelector('button');
                if (btn) btn.click();
            });
        }
    });
});
