/**
 * Shared API wrapper for AJAX requests
 * Usage:
 *   const data = await API.get('jobs/search', { q: 'react' });
 *   const result = await API.post('jobs/bookmark', { job_id: 5 });
 */
var API = {
    get: function(endpoint, params) {
        params = params || {};
        var url = new URL('/api/' + endpoint, location.origin);
        Object.keys(params).forEach(function(k) {
            url.searchParams.set(k, params[k]);
        });
        return fetch(url).then(function(res) { return res.json(); });
    },

    post: function(endpoint, body) {
        body = body || {};
        return fetch('/api/' + endpoint, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(body)
        }).then(function(res) { return res.json(); });
    }
};
