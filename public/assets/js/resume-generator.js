/**
 * Resume Generator — Interactive JS
 * Live preview, AI enhance, PDF export, template switching
 */

let currentTemplate = 'executive';
let previewZoom = 1;

// ─── Init ─────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    updatePreview();
});

// ─── Template Selection ───────────────────────────────
function selectTemplate(card) {
    document.querySelectorAll('.resume-template-card').forEach(c => {
        c.classList.remove('resume-template-card--active');
        c.querySelector('.resume-template-check')?.remove();
    });
    card.classList.add('resume-template-card--active');
    const check = document.createElement('span');
    check.className = 'resume-template-check material-symbols-outlined';
    check.style.fontVariationSettings = "'FILL' 1";
    check.textContent = 'check_circle';
    card.appendChild(check);
    currentTemplate = card.dataset.template;
    updatePreview();
}

// ─── Auto-fill from Profile ───────────────────────────
function autofillFromProfile() {
    document.getElementById('rv-name').value = profileData.name;
    document.getElementById('rv-title').value = profileData.title;
    document.getElementById('rv-email').value = profileData.email;
    document.getElementById('rv-phone').value = profileData.phone;
    document.getElementById('rv-location').value = profileData.location;
    document.getElementById('rv-link').value = profileData.link;
    document.getElementById('rv-summary').value = profileData.summary;
    updatePreview();
    showToast('Profile data loaded!', 'success');
}

// ─── Live Preview ─────────────────────────────────────
function updatePreview() {
    const name = document.getElementById('rv-name').value || 'Your Name';
    const title = document.getElementById('rv-title').value || 'Job Title';
    const email = document.getElementById('rv-email').value;
    const phone = document.getElementById('rv-phone').value;
    const location = document.getElementById('rv-location').value;
    const link = document.getElementById('rv-link').value;
    const summary = document.getElementById('rv-summary').value;

    const contactParts = [email, phone, location].filter(Boolean);
    const contactLine = contactParts.join(' • ');

    // Gather experience
    let expHtml = '';
    document.querySelectorAll('.resume-exp-card').forEach(card => {
        const t = card.querySelector('.exp-title')?.value || card.querySelector('.resume-exp-title')?.textContent || '';
        const c = card.querySelector('.exp-company')?.value || '';
        const p = card.querySelector('.exp-period')?.value || '';
        const d = card.querySelector('.resume-exp-desc')?.value || '';
        const bullets = d.split('\n').filter(Boolean).map(b => {
            b = b.replace(/^[•\-\*]\s*/, '');
            return `<div class="resume-pv-text">• ${escHtml(b)}</div>`;
        }).join('');
        expHtml += `<div class="resume-pv-exp-title">${escHtml(t)}</div>
                     <div class="resume-pv-exp-sub">${escHtml(c)}${p ? ' • ' + escHtml(p) : ''}</div>
                     ${bullets}`;
    });

    // Gather skills
    const skillTags = [];
    document.querySelectorAll('.resume-skill-tag').forEach(tag => {
        const text = tag.childNodes[0]?.textContent?.trim();
        if (text) skillTags.push(text);
    });
    const skillsLine = skillTags.join(' • ');

    // Gather education
    let eduHtml = '';
    document.querySelectorAll('.resume-edu-card').forEach(card => {
        const deg = card.querySelector('.edu-degree')?.value || card.querySelector('.resume-edu-degree')?.textContent || '';
        const sch = card.querySelector('.edu-school')?.value || card.querySelector('.resume-edu-school')?.textContent || '';
        const per = card.querySelector('.edu-period')?.value || '';
        eduHtml += `<div class="resume-pv-exp-title">${escHtml(deg)}</div>
                     <div class="resume-pv-exp-sub">${escHtml(sch)}${per ? ' • ' + escHtml(per) : ''}</div>`;
    });

    // Build preview based on template
    const page = document.getElementById('resume-preview-page');
    page.className = 'resume-preview-page resume-tpl-' + currentTemplate;

    let linkHtml = link ? `<div class="resume-pv-text" style="margin-top:0.125rem;">${escHtml(link)}</div>` : '';

    page.innerHTML = `
        <div class="resume-pv-name">${escHtml(name)}</div>
        <div class="resume-pv-jobtitle">${escHtml(title)}</div>
        <div class="resume-pv-contact">${escHtml(contactLine)}</div>
        ${linkHtml}
        <div class="resume-pv-divider"></div>
        ${summary ? `<div class="resume-pv-heading">Professional Summary</div><div class="resume-pv-text">${escHtml(summary)}</div>` : ''}
        ${expHtml ? `<div class="resume-pv-heading">Experience</div>${expHtml}` : ''}
        ${skillsLine ? `<div class="resume-pv-heading">Skills</div><div class="resume-pv-text">${escHtml(skillsLine)}</div>` : ''}
        ${eduHtml ? `<div class="resume-pv-heading">Education</div>${eduHtml}` : ''}
    `;
}

function escHtml(str) {
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
}

// ─── Zoom ─────────────────────────────────────────────
function zoomPreview(dir) {
    previewZoom = Math.max(0.6, Math.min(1.4, previewZoom + dir * 0.1));
    document.getElementById('resume-preview-page').style.transform = `scale(${previewZoom})`;
    document.getElementById('resume-preview-page').style.transformOrigin = 'top left';
}

// ─── Experience CRUD ──────────────────────────────────
function addExperience() {
    document.getElementById('rv-exp-empty')?.remove();
    const list = document.getElementById('rv-exp-list');
    const idx = list.querySelectorAll('.resume-exp-card').length;
    const card = document.createElement('div');
    card.className = 'resume-exp-card';
    card.dataset.expIdx = idx;
    card.innerHTML = `
        <div class="resume-exp-card-header">
            <div class="resume-exp-card-drag"><span class="material-symbols-outlined">drag_indicator</span></div>
            <div class="resume-exp-card-info">
                <input class="resume-input resume-exp-title-input" type="text" placeholder="Job Title" oninput="updateExpHeader(this); updatePreview()">
                <input class="resume-input" type="text" placeholder="Company • Period (e.g. Jan 2020 – Present)" style="font-size:0.8rem; margin-top:0.5rem;" oninput="updateExpCompany(this); updatePreview()">
            </div>
            <button class="resume-ai-inline-btn" onclick="aiEnhanceBullets(this)" title="Enhance with AI">
                <span class="material-symbols-outlined">auto_awesome</span>
            </button>
            <button class="resume-exp-remove" onclick="removeExperience(this)"><span class="material-symbols-outlined">delete</span></button>
        </div>
        <textarea class="resume-exp-desc" rows="3" oninput="updatePreview()" placeholder="Describe your key achievements..."></textarea>
        <input type="hidden" class="exp-title" value="">
        <input type="hidden" class="exp-company" value="">
        <input type="hidden" class="exp-period" value="">
    `;
    list.appendChild(card);
    card.querySelector('input').focus();
}

function updateExpHeader(input) {
    const card = input.closest('.resume-exp-card');
    card.querySelector('.exp-title').value = input.value;
}

function updateExpCompany(input) {
    const card = input.closest('.resume-exp-card');
    const parts = input.value.split('•').map(s => s.trim());
    card.querySelector('.exp-company').value = parts[0] || '';
    card.querySelector('.exp-period').value = parts[1] || '';
}

function removeExperience(btn) {
    btn.closest('.resume-exp-card').remove();
    updatePreview();
}

// ─── Education CRUD ───────────────────────────────────
function addEducation() {
    document.getElementById('rv-edu-empty')?.remove();
    const list = document.getElementById('rv-edu-list');
    const card = document.createElement('div');
    card.className = 'resume-edu-card';
    card.innerHTML = `
        <div class="resume-edu-icon"><span class="material-symbols-outlined">school</span></div>
        <div style="flex:1;">
            <input class="resume-input resume-edu-degree" type="text" placeholder="Degree (e.g. BSc in Computer Science)" style="font-weight:700; margin-bottom:0.4rem;" oninput="this.closest('.resume-edu-card').querySelector('.edu-degree').value=this.value; updatePreview()">
            <input class="resume-input" type="text" placeholder="School • Period (e.g. 2016 – 2020)" style="font-size:0.8rem;" oninput="updateEduMeta(this); updatePreview()">
        </div>
        <button class="resume-exp-remove" onclick="removeEducation(this)"><span class="material-symbols-outlined">delete</span></button>
        <input type="hidden" class="edu-degree" value="">
        <input type="hidden" class="edu-school" value="">
        <input type="hidden" class="edu-period" value="">
    `;
    list.appendChild(card);
    card.querySelector('input').focus();
}

function updateEduMeta(input) {
    const card = input.closest('.resume-edu-card');
    const parts = input.value.split('•').map(s => s.trim());
    card.querySelector('.edu-school').value = parts[0] || '';
    card.querySelector('.edu-period').value = parts[1] || '';
}

function removeEducation(btn) {
    btn.closest('.resume-edu-card').remove();
    updatePreview();
}

// ─── Skills CRUD ──────────────────────────────────────
function handleSkillKey(e) {
    if (e.key === 'Enter' || e.key === ',') {
        e.preventDefault();
        const input = document.getElementById('rv-skill-input');
        const val = input.value.replace(',', '').trim();
        if (!val) return;
        addSkillTag(val);
        input.value = '';
        updatePreview();
    }
}

function addSkillTag(name) {
    const wrap = document.getElementById('rv-skills-wrap');
    const input = document.getElementById('rv-skill-input');
    const tag = document.createElement('div');
    tag.className = 'resume-skill-tag';
    tag.innerHTML = `${escHtml(name)} <button class="resume-skill-remove" onclick="removeSkill(this)">&times;</button>`;
    wrap.insertBefore(tag, input);
}

function removeSkill(btn) {
    btn.closest('.resume-skill-tag').remove();
    updatePreview();
}

// ─── AI: Enhance Summary ──────────────────────────────
async function aiEnhanceSummary() {
    const btn = document.querySelector('#rv-summary').previousElementSibling?.querySelector('.resume-ai-inline-btn') ||
                document.querySelector('[onclick="aiEnhanceSummary()"]');
    const summaryEl = document.getElementById('rv-summary');
    const original = summaryEl.value;

    setLoading(btn, true);
    try {
        const skills = [];
        document.querySelectorAll('.resume-skill-tag').forEach(t => {
            const txt = t.childNodes[0]?.textContent?.trim();
            if (txt) skills.push(txt);
        });

        const expContext = [];
        document.querySelectorAll('.resume-exp-card').forEach(card => {
            expContext.push(card.querySelector('.exp-title')?.value || '');
        });

        const res = await fetch('/api/resume/enhance', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({
                action: 'enhance_summary',
                data: {
                    name: document.getElementById('rv-name').value,
                    title: document.getElementById('rv-title').value,
                    current_summary: original,
                    skills: skills.join(', '),
                    experience: expContext.join(', ')
                }
            })
        });
        const json = await res.json();
        if (json.success && json.result) {
            summaryEl.value = json.result;
            updatePreview();
            showToast('Summary enhanced!', 'success');
        }
    } catch (err) {
        showToast('Enhancement failed. Try again.', 'error');
    }
    setLoading(btn, false);
}

// ─── AI: Enhance Bullets ──────────────────────────────
async function aiEnhanceBullets(btn) {
    const card = btn.closest('.resume-exp-card');
    const descEl = card.querySelector('.resume-exp-desc');
    const original = descEl.value;

    setLoading(btn, true);
    try {
        const res = await fetch('/api/resume/enhance', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({
                action: 'enhance_bullets',
                data: {
                    job_title: card.querySelector('.exp-title')?.value || '',
                    company: card.querySelector('.exp-company')?.value || '',
                    description: original
                }
            })
        });
        const json = await res.json();
        if (json.success && json.result) {
            descEl.value = json.result;
            updatePreview();
            showToast('Bullets enhanced!', 'success');
        }
    } catch (err) {
        showToast('Enhancement failed.', 'error');
    }
    setLoading(btn, false);
}

// ─── AI: Suggest Skills ───────────────────────────────
async function aiSuggestSkills() {
    const btn = document.querySelector('[onclick="aiSuggestSkills()"]');
    setLoading(btn, true);
    try {
        const currentSkills = [];
        document.querySelectorAll('.resume-skill-tag').forEach(t => {
            const txt = t.childNodes[0]?.textContent?.trim();
            if (txt) currentSkills.push(txt);
        });

        const expText = [];
        document.querySelectorAll('.resume-exp-card .exp-title').forEach(i => { if (i.value) expText.push(i.value); });

        const res = await fetch('/api/resume/enhance', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({
                action: 'suggest_skills',
                data: {
                    target_role: document.getElementById('rv-title').value,
                    current_skills: currentSkills.join(', '),
                    experience: expText.join(', ')
                }
            })
        });
        const json = await res.json();
        if (json.success && json.result) {
            const suggested = json.result.split(',').map(s => s.trim()).filter(Boolean);
            let added = 0;
            suggested.forEach(sk => {
                if (!currentSkills.some(cs => cs.toLowerCase() === sk.toLowerCase())) {
                    addSkillTag(sk);
                    added++;
                }
            });
            updatePreview();
            showToast(`Added ${added} suggested skills!`, 'success');
        }
    } catch (err) {
        showToast('Skill suggestion failed.', 'error');
    }
    setLoading(btn, false);
}

// ─── AI: Full Resume Analysis ─────────────────────────
async function getAISuggestions() {
    const btn = document.querySelector('[onclick="getAISuggestions()"]');
    setLoading(btn, true);

    const skills = [];
    document.querySelectorAll('.resume-skill-tag').forEach(t => {
        const txt = t.childNodes[0]?.textContent?.trim();
        if (txt) skills.push(txt);
    });

    const expArr = [];
    document.querySelectorAll('.resume-exp-card').forEach(card => {
        expArr.push({
            title: card.querySelector('.exp-title')?.value || '',
            description: card.querySelector('.resume-exp-desc')?.value || ''
        });
    });

    const eduArr = [];
    document.querySelectorAll('.resume-edu-card').forEach(card => {
        eduArr.push({ degree: card.querySelector('.edu-degree')?.value || '' });
    });

    try {
        const res = await fetch('/api/resume/suggestions', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({
                resume: {
                    name: document.getElementById('rv-name').value,
                    title: document.getElementById('rv-title').value,
                    summary: document.getElementById('rv-summary').value,
                    skills: skills.join(', '),
                    experience: expArr,
                    education: eduArr
                }
            })
        });
        const json = await res.json();
        const data = json.suggestions || json;
        renderAITips(data);
        showToast('Resume analyzed!', 'success');
    } catch (err) {
        showToast('Analysis failed.', 'error');
    }
    setLoading(btn, false);
}

function renderAITips(data) {
    const container = document.getElementById('rv-ai-tips');
    const score = data.score || 70;
    const tips = data.tips || [];

    let html = `<div class="resume-ai-tip">
        <span class="material-symbols-outlined resume-ai-tip-icon">trending_up</span>
        <p>Your resume scores <strong>${score}/100</strong> for ATS compatibility.</p>
    </div>`;

    tips.forEach(tip => {
        html += `<div class="resume-ai-tip">
            <span class="material-symbols-outlined resume-ai-tip-icon">lightbulb</span>
            <p>${escHtml(tip)}</p>
        </div>`;
    });

    container.innerHTML = html;
}

// ─── Export: PDF via Print ─────────────────────────────
function exportPDF() {
    const page = document.getElementById('resume-preview-page');
    const printWin = window.open('', '_blank');

    printWin.document.write(`<!DOCTYPE html><html><head>
        <title>Resume - ${document.getElementById('rv-name').value}</title>
        <link href="https://fonts.googleapis.com/css2?family=Newsreader:wght@400;700&family=Manrope:wght@400;600;700&display=swap" rel="stylesheet">
        <style>
            * { margin: 0; padding: 0; box-sizing: border-box; }
            body { font-family: 'Manrope', sans-serif; padding: 2.5rem; color: #170f07; max-width: 800px; margin: 0 auto; }
            .rv-name { font-family: 'Newsreader', serif; font-size: 1.75rem; font-weight: 700; margin-bottom: 0.25rem; }
            .rv-title { font-size: 0.85rem; color: #695d46; text-transform: uppercase; letter-spacing: 0.12em; font-weight: 600; margin-bottom: 0.375rem; }
            .rv-contact { font-size: 0.8rem; color: #7e766e; margin-bottom: 1rem; }
            .rv-divider { height: 2px; background: #170f07; margin-bottom: 1rem; }
            .rv-heading { font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.12em; margin: 1.25rem 0 0.5rem; color: #170f07; border-bottom: 1px solid #d0c5bb; padding-bottom: 0.25rem; }
            .rv-exp-title { font-size: 0.9rem; font-weight: 700; }
            .rv-exp-sub { font-size: 0.8rem; color: #7e766e; margin-bottom: 0.25rem; }
            .rv-text { font-size: 0.85rem; color: #333; line-height: 1.7; }
            .rv-skills { font-size: 0.85rem; color: #333; }
            @media print { body { padding: 0; } }
        </style>
    </head><body>`);

    const name = document.getElementById('rv-name').value;
    const title = document.getElementById('rv-title').value;
    const email = document.getElementById('rv-email').value;
    const phone = document.getElementById('rv-phone').value;
    const loc = document.getElementById('rv-location').value;
    const link = document.getElementById('rv-link').value;
    const summary = document.getElementById('rv-summary').value;

    const contact = [email, phone, loc].filter(Boolean).join(' • ');
    printWin.document.write(`<div class="rv-name">${esc(name)}</div>`);
    printWin.document.write(`<div class="rv-title">${esc(title)}</div>`);
    printWin.document.write(`<div class="rv-contact">${esc(contact)}</div>`);
    if (link) printWin.document.write(`<div class="rv-contact">${esc(link)}</div>`);
    printWin.document.write(`<div class="rv-divider"></div>`);

    if (summary) {
        printWin.document.write(`<div class="rv-heading">Professional Summary</div>`);
        printWin.document.write(`<div class="rv-text">${esc(summary)}</div>`);
    }

    const expCards = document.querySelectorAll('.resume-exp-card');
    if (expCards.length) {
        printWin.document.write(`<div class="rv-heading">Experience</div>`);
        expCards.forEach(card => {
            const t = card.querySelector('.exp-title')?.value || '';
            const c = card.querySelector('.exp-company')?.value || '';
            const p = card.querySelector('.exp-period')?.value || '';
            const d = card.querySelector('.resume-exp-desc')?.value || '';
            printWin.document.write(`<div class="rv-exp-title">${esc(t)}</div>`);
            printWin.document.write(`<div class="rv-exp-sub">${esc(c)}${p ? ' • ' + esc(p) : ''}</div>`);
            d.split('\n').filter(Boolean).forEach(line => {
                line = line.replace(/^[•\-\*]\s*/, '');
                printWin.document.write(`<div class="rv-text">• ${esc(line)}</div>`);
            });
        });
    }

    const skillTags = [];
    document.querySelectorAll('.resume-skill-tag').forEach(t => {
        const txt = t.childNodes[0]?.textContent?.trim();
        if (txt) skillTags.push(txt);
    });
    if (skillTags.length) {
        printWin.document.write(`<div class="rv-heading">Skills</div>`);
        printWin.document.write(`<div class="rv-skills">${esc(skillTags.join(' • '))}</div>`);
    }

    const eduCards = document.querySelectorAll('.resume-edu-card');
    if (eduCards.length) {
        printWin.document.write(`<div class="rv-heading">Education</div>`);
        eduCards.forEach(card => {
            const deg = card.querySelector('.edu-degree')?.value || '';
            const sch = card.querySelector('.edu-school')?.value || '';
            const per = card.querySelector('.edu-period')?.value || '';
            printWin.document.write(`<div class="rv-exp-title">${esc(deg)}</div>`);
            printWin.document.write(`<div class="rv-exp-sub">${esc(sch)}${per ? ' • ' + esc(per) : ''}</div>`);
        });
    }

    printWin.document.write(`</body></html>`);
    printWin.document.close();
    setTimeout(() => { printWin.print(); }, 500);
}

function printResume() { exportPDF(); }

function esc(str) {
    const el = document.createElement('span');
    el.textContent = str || '';
    return el.innerHTML;
}

// ─── Helpers ──────────────────────────────────────────
function setLoading(btn, loading) {
    if (!btn) return;
    if (loading) {
        btn.dataset.original = btn.innerHTML;
        btn.innerHTML = '<span class="material-symbols-outlined" style="animation:spin 1s linear infinite;">progress_activity</span>';
        btn.disabled = true;
    } else {
        btn.innerHTML = btn.dataset.original || btn.innerHTML;
        btn.disabled = false;
    }
}

function showToast(msg, type) {
    const existing = document.querySelector('.toast-container');
    if (existing) {
        const toast = document.createElement('div');
        toast.className = `toast-item toast-${type || 'info'}`;
        toast.innerHTML = `<span class="material-symbols-outlined">${type === 'success' ? 'check_circle' : 'error'}</span><span>${msg}</span>`;
        existing.appendChild(toast);
        setTimeout(() => toast.remove(), 3000);
    } else {
        console.log(`[${type}] ${msg}`);
    }
}
