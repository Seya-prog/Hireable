/**
 * Assessment Create page interactions
 * - Toggle between Manual and AI modes
 */
document.addEventListener('DOMContentLoaded', function() {

    var modeManual = document.getElementById('mode-manual');
    var modeAi = document.getElementById('mode-ai');
    var panelManual = document.getElementById('panel-manual');
    var panelAi = document.getElementById('panel-ai');

    if (modeManual && modeAi && panelManual && panelAi) {
        modeManual.addEventListener('click', function() {
            this.classList.add('assess-mode-btn--active');
            modeAi.classList.remove('assess-mode-btn--active');
            panelManual.classList.remove('assess-create-panel--hidden');
            panelAi.classList.add('assess-create-panel--hidden');
        });

        modeAi.addEventListener('click', function() {
            this.classList.add('assess-mode-btn--active');
            modeManual.classList.remove('assess-mode-btn--active');
            panelAi.classList.remove('assess-create-panel--hidden');
            panelManual.classList.add('assess-create-panel--hidden');
        });
    }
});
