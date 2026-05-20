/**
 * Job Create page interactions
 * - Skills tag input (add/remove)
 * - Draft/Publish status toggle
 */
document.addEventListener('DOMContentLoaded', function() {

    var skillInput = document.getElementById('skill-input');
    var skillsList = document.getElementById('skills-list');
    var skillsHidden = document.getElementById('skills-hidden');
    var skills = [];

    if (skillInput) {
        skillInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                var val = this.value.trim();
                if (val && skills.indexOf(val) === -1) {
                    skills.push(val);
                    renderSkills();
                    this.value = '';
                }
            }
        });
    }

    function renderSkills() {
        if (!skillsList) return;
        skillsList.innerHTML = skills.map(function(s) {
            return '<span class="emp-cand-skill-tag">' + s +
                   ' <button type="button" class="emp-tag-remove" data-skill="' + s + '">&times;</button></span>';
        }).join('');
        if (skillsHidden) skillsHidden.value = skills.join(', ');

        // Attach remove listeners
        skillsList.querySelectorAll('.emp-tag-remove').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var name = this.getAttribute('data-skill');
                skills = skills.filter(function(s) { return s !== name; });
                renderSkills();
            });
        });
    }

    // Draft/Publish buttons
    var statusField = document.getElementById('job-status');
    var draftBtn = document.getElementById('btn-draft');
    var publishBtn = document.getElementById('btn-publish');

    if (draftBtn && statusField) {
        draftBtn.addEventListener('click', function() {
            statusField.value = 'draft';
        });
    }
    if (publishBtn && statusField) {
        publishBtn.addEventListener('click', function() {
            statusField.value = 'active';
        });
    }
});
