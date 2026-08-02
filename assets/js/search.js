/* search.js
 * Handles live search functionality in the global header, including
 * debounced input handling, result rendering, and click-outside-to-close behavior.
 */
const Search = {

    init(options) {

        const searchContainer = document.querySelector(options.container);
        const searchInput = document.querySelector(options.input);
        const resultsBox = document.querySelector(options.results);
        const endpoint = options.endpoint;

            function closeResults() {
            if (!resultsBox) return;
            resultsBox.innerHTML = '';
            resultsBox.classList.remove('active');
            }

            function closeSearch() {
            closeResults();
            }

            /* ── Click anywhere outside search → close everything ─── */
            document.addEventListener('click', function (e) {
            if (searchContainer && !searchContainer.contains(e.target)) {
            closeSearch();
            }
            });

            /* ── Misc helpers ─────────────────────────────────────── */
            function escapeHtml(str) {
            return String(str).replace(/[&<>"']/g, m =>
            ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[m])
            );
            }

            function highlight(text, q) {
            const safe = q.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
            const regex = new RegExp('(' + safe + ')', 'gi');
            return escapeHtml(text).replace(regex,
            '<mark style="background:#fff3cd;color:#1a1a1a;font-weight:700;border-radius:2px;padding:0 2px;">$1</mark>'
            );
            }

            /* ── Badge config per result type ────────────────────── */
            const BADGE = {
            product: { bg: '#e8f4fd', color: '#1a6fa8', text: 'Product' },
            brand: { bg: '#fef3e2', color: '#b8711a', text: 'Brand' },
            category: { bg: '#edfaee', color: '#1a7a2e', text: 'Category' },
            };

            /* ── Render dropdown results ──────────────────────────── */
            function renderResults(items, query) {
            if (!resultsBox) return;

            if (!items || !items.length) {
            resultsBox.innerHTML =
                '<div class="pvc-header-search-empty">No results found for "<strong>' +
                escapeHtml(query) + '</strong>"</div>';
            resultsBox.classList.add('active');
            return;
            }

            resultsBox.innerHTML = items.map(item => {
            const type = item.type || 'product';
            const badge = BADGE[type] || BADGE.product;
            const img = (item.pimage && item.pimage.trim())
                ? item.pimage
                : 'assets/img/logo/logo1.png';
            return `
            <a class="pvc-header-search-result-item" href="${escapeHtml(item.url)}">
            <img src="${escapeHtml(img)}"
            alt="${escapeHtml(item.label)}"
            loading="lazy"
            onerror="this.onerror=null;this.src='assets/img/logo/logo1.png';">
            <div class="pvc-header-search-result-info">
            <span class="pvc-header-search-result-name" style="display:flex;align-items:center;gap:6px;flex-wrap:wrap;">
            <span>${highlight(item.label, query)}</span>
            <span style="font-size:10px;font-weight:600;padding:2px 7px;border-radius:4px;
                background:${badge.bg};color:${badge.color};flex-shrink:0;">${badge.text}</span>
            </span>
            ${item.sublabel
                    ? `<span class="pvc-header-search-result-meta">${escapeHtml(item.sublabel)}</span>`
                    : ''}
            </div>
            </a>`;
            }).join('');

            resultsBox.classList.add('active');
            }

            /* ── Live search (debounced + abortable) ─────────────── */
            if (searchInput && resultsBox) {
            let debounceTimer = null;
            let activeController = null;

            searchInput.addEventListener('input', function () {
            const query = this.value.trim();
            clearTimeout(debounceTimer);

            if (query.length < 1) { closeResults(); return; }

            resultsBox.innerHTML = '<div class="pvc-header-search-loading">Searching…</div>';
            resultsBox.classList.add('active');

            debounceTimer = setTimeout(() => {
                if (activeController) activeController.abort();
                activeController = new AbortController();

                fetch(endpoint + '?q=' + encodeURIComponent(query), {
                    signal: activeController.signal
                })
                    .then(res => {
                        if (!res.ok) throw new Error('HTTP ' + res.status);
                        return res.json();
                    })
                    .then(items => renderResults(items, query))
                    .catch(err => {
                        if (err.name !== 'AbortError') {
                            console.error('Search error:', err);
                            resultsBox.innerHTML =
                                '<div class="pvc-header-search-empty">Something went wrong. Please try again.</div>';
                        }
                    });
            }, 280);
            });

            /* Escape closes entire search */
            searchInput.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') { closeSearch(); this.blur(); }
            });
            }
            
 }

};