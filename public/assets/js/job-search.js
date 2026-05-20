/**
 * Job Search page — AJAX filtering and interactions
 */
document.addEventListener('DOMContentLoaded', function() {

    // Bookmark buttons
    document.querySelectorAll('.js-opp-bookmark').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var icon = this.querySelector('.material-symbols-outlined');
            var isBookmarked = icon.style.fontVariationSettings === "'FILL' 1";
            if (isBookmarked) {
                icon.style.fontVariationSettings = '';
                this.style.color = '#ccc';
            } else {
                icon.style.fontVariationSettings = "'FILL' 1";
                this.style.color = '#695d46';
            }
        });
    });

    // Filter pills (placeholder for dropdown logic)
    document.querySelectorAll('.js-pill').forEach(function(pill) {
        pill.addEventListener('click', function() {
            this.classList.toggle('js-pill--active');
        });
    });
});
