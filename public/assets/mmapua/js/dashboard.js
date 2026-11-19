
document.addEventListener('DOMContentLoaded', function () {

    const toggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('adminSidebar');

    if (toggle && sidebar) {

        toggle.addEventListener('click', function () {

            sidebar.classList.toggle('show');

        });

        document.addEventListener('click', function (event) {

            if (
                window.innerWidth <= 991 &&
                !sidebar.contains(event.target) &&
                !toggle.contains(event.target)
            ) {
                sidebar.classList.remove('show');
            }

        });

    }



     const apiUrl  = <?= json_encode($statsApiUrl) ?>;
    const version = <?= json_encode($dashboardVersion) ?>;
    const loaded  = {};          // groups already loaded
    let currentGroup = 'overview';

    const formatNumber = v => Number(v ?? 0).toLocaleString('en-IN');

    function getNestedValue(obj, path) {
        return path.split('.').reduce((cur, key) =>
            cur && cur[key] !== undefined ? cur[key] : null, obj);
    }

    function renderStats(data, scope) {
        scope.querySelectorAll('[data-stat]').forEach(el => {
            const value = getNestedValue(data, el.dataset.stat);
            el.textContent = value === null ? '0' : formatNumber(value);
        });
    }

    function setStatus(pane, type, html) {
        const box = pane.querySelector('.pane-status');
        box.innerHTML = html ? `<div class="alert alert-${type} py-2">${html}</div>` : '';
    }

    async function loadGroup(group, force = false) {
        if (loaded[group] && !force) return;

        const pane = document.querySelector(`[data-pane="${group}"]`);
        if (!pane) return;

        pane.classList.add('is-loading');
        setStatus(pane, 'info',
            '<span class="spinner-border spinner-border-sm me-2"></span>Loading data...');

        try {
            const url = `${apiUrl}?v=${encodeURIComponent(version)}&group=${group}` +
                        (force ? '&refresh=1' : '');

            const response = await fetch(url, {
                credentials: 'same-origin',
                headers: { 'Accept': 'application/json' }
            });

            if (response.status === 401) {
                window.location.href = <?= json_encode(site_url('login')) ?>;
                return;
            }

            const result = await response.json();
            if (!response.ok || !result.status) {
                throw new Error(result.message || 'Unable to load data.');
            }

            renderStats(result.data, pane);
            loaded[group] = true;
            setStatus(pane, '', '');

        } catch (error) {
            console.error('Dashboard API error:', error);
            setStatus(pane, 'danger',
                `${error.message} <button class="btn btn-sm btn-link p-0 ms-2 retry-btn">Retry</button>`);
            pane.querySelector('.retry-btn')
                ?.addEventListener('click', () => loadGroup(group, true));
        } finally {
            pane.classList.remove('is-loading');
        }
    }

    // Load a tab the first time it is shown
    document.querySelectorAll('#dashboardTabs [data-group]').forEach(btn => {
        btn.addEventListener('shown.bs.tab', () => {
            currentGroup = btn.dataset.group;
            loadGroup(currentGroup);
        });
    });

    // Refresh the active tab, bypassing the cache
    document.getElementById('refreshTab').addEventListener('click', () => {
        loadGroup(currentGroup, true);
    });

    // Initial tab
    loadGroup('overview');

});




