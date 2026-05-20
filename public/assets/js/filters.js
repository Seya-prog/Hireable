/**
 * Shared Search, Filter & Tab Logic
 * Works on any page with these conventions:
 * 
 * TAB FILTERS:   .emp-filter-tab[data-filter] → filters items by [data-status]
 * SEARCH:        .emp-search-input → text search on [data-searchable] content
 * DROPDOWN:      .emp-filter-select → filters items by [data-position]
 * 
 * Container:     [data-filter-container] → the grid/list holding filterable items
 * Items:         [data-filter-item] → each filterable card/row
 */
(function () {
    'use strict';

    const container = document.querySelector('[data-filter-container]');
    if (!container) return;

    const getItems = () => container.querySelectorAll('[data-filter-item]');

    // ─── State ──────────────────────────────────
    let activeFilter = 'all';
    let searchQuery = '';
    let positionFilter = '';

    // ─── Tab Filters ────────────────────────────
    const tabs = document.querySelectorAll('.emp-filter-tab[data-filter]');
    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            tabs.forEach(t => t.classList.remove('emp-filter-tab--active'));
            tab.classList.add('emp-filter-tab--active');
            activeFilter = tab.dataset.filter.toLowerCase();
            applyFilters();
        });
    });

    // ─── Search ─────────────────────────────────
    const searchInput = document.querySelector('.emp-search-input');
    if (searchInput) {
        searchInput.addEventListener('input', () => {
            searchQuery = searchInput.value.trim().toLowerCase();
            applyFilters();
        });
    }

    // ─── Dropdown Filter ────────────────────────
    const selectFilter = document.querySelector('.emp-filter-select');
    if (selectFilter) {
        selectFilter.addEventListener('change', () => {
            const val = selectFilter.value;
            positionFilter = (val === 'All Positions' || val === '') ? '' : val.toLowerCase();
            applyFilters();
        });
    }

    // ─── Apply All Filters ──────────────────────
    function applyFilters() {
        const items = getItems();
        let visibleCount = 0;

        items.forEach(item => {
            const status = (item.dataset.status || '').toLowerCase();
            const searchText = (item.dataset.searchable || item.textContent).toLowerCase();
            const position = (item.dataset.position || '').toLowerCase();

            const matchesTab = activeFilter === 'all' || status === activeFilter;
            const matchesSearch = !searchQuery || searchText.includes(searchQuery);
            const matchesPosition = !positionFilter || position.includes(positionFilter);

            const visible = matchesTab && matchesSearch && matchesPosition;
            item.style.display = visible ? '' : 'none';
            if (visible) visibleCount++;
        });

        // Show/hide empty state
        let emptyMsg = container.querySelector('.filter-empty-state');
        if (visibleCount === 0) {
            if (!emptyMsg) {
                emptyMsg = document.createElement('p');
                emptyMsg.className = 'filter-empty-state';
                emptyMsg.style.cssText = 'text-align:center; color:#7a6b5a; padding:2.5rem 1rem; grid-column:1/-1; font-size:0.9rem;';
                emptyMsg.innerHTML = '<span class="material-symbols-outlined" style="display:block;font-size:2rem;opacity:0.4;margin-bottom:0.5rem;">search_off</span>No results match your filters.';
                container.appendChild(emptyMsg);
            }
            emptyMsg.style.display = 'block';
        } else if (emptyMsg) {
            emptyMsg.style.display = 'none';
        }
    }
})();
