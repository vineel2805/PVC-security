/* ===================================================================
   Category page live search
   =================================================================== */
(function() {
    const input     = document.getElementById('catSearchInput');
    const clearBtn  = document.getElementById('catSearchClear');
    const resultsEl = document.getElementById('catSearchResults');
    if (!input || !resultsEl) return;

    let debounceTimer = null;

    function closeResults() {
        resultsEl.classList.remove('is-open');
        resultsEl.innerHTML = '';
    }

    function escapeHtml(str) {
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    function renderResults(data) {
        const products   = data.products   || [];
        const categories = data.categories || [];

        if (!products.length && !categories.length) {
            resultsEl.innerHTML = '<div class="cat-search-no-results">No matches found.</div>';
            resultsEl.classList.add('is-open');
            return;
        }

        let html = '';

        if (categories.length) {
            html += '<div class="cat-search-section-label">Categories</div>';
            categories.forEach(function(c) {
                html += '' +
                    '<a href="' + c.url + '" class="cat-search-result-item cat-only js-cat-nav">' +
                        '<i class="fa-solid fa-layer-group"></i>' +
                        '<span class="cat-search-result-name">' + escapeHtml(c.name) + '</span>' +
                    '</a>';
            });
        }

        if (products.length) {
            html += '<div class="cat-search-section-label">Products</div>';
            products.forEach(function(p) {
                html += '' +
                    '<a href="' + p.url + '" class="cat-search-result-item">' +
                        '<img src="' + p.image + '" alt="' + escapeHtml(p.name) + '" loading="lazy">' +
                        '<span>' +
                            '<span class="cat-search-result-name">' + escapeHtml(p.name) + '</span><br>' +
                            '<span class="cat-search-result-brand">' + escapeHtml(p.brand) + '</span>' +
                        '</span>' +
                    '</a>';
            });
        }

        resultsEl.innerHTML = html;
        resultsEl.classList.add('is-open');

        // Category results should use the AJAX in-page nav, not a full reload
        resultsEl.querySelectorAll('.js-cat-nav').forEach(function(link) {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                closeResults();
                input.value = '';
                clearBtn.style.display = 'none';
                loadCategoryView(this.getAttribute('href'));
            });
        });
    }

    input.addEventListener('input', function() {
        const term = this.value.trim();
        clearBtn.style.display = term ? '' : 'none';

        clearTimeout(debounceTimer);
        if (term.length < 2) {
            closeResults();
            return;
        }

        debounceTimer = setTimeout(async function() {
            try {
                const res  = await fetch('all-categories.php?live_search=' + encodeURIComponent(term));
                const data = await res.json();
                renderResults(data);
            } catch (err) {
                console.error('Search failed:', err);
            }
        }, 250);
    });

    clearBtn.addEventListener('click', function() {
        input.value = '';
        clearBtn.style.display = 'none';
        closeResults();
        input.focus();
    });

    document.addEventListener('click', function(e) {
        if (!e.target.closest('.cat-search-wrap')) closeResults();
    });
})();