/**
 * Landing page interactions
 * - Header scroll effect
 */
window.addEventListener('scroll', function () {
    var header = document.querySelector('header');
    if (header) {
        if (window.scrollY > 0) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
    }
});
