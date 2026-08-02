/* ===================================================================
   Catalog Page Autocomplete Search
   =================================================================== */
(function() {
    function initSearch() {
        const input     = document.getElementById('catSearchInput') || document.getElementById('brandSearchInput');
        const clearBtn  = document.getElementById('catSearchClear') || document.getElementById('brandSearchClear');
        const resultsEl = document.getElementById('catSearchResults') || document.getElementById('brandSearchResults');
        
        if (!input || !resultsEl) {
            console.warn("Catalog search elements not found on this page.");
            return;
        }

        let debounceTimer = null;

        // Dynamically target the backend search endpoint based on current page
        const pagePath = window.location.pathname.split('/').pop() || 'all-categories.php';
        let endpoint = 'all-categories.php';
        if (pagePath.includes('brand') || pagePath.includes('products')) {
            endpoint = pagePath;
        }

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

        // Suggestion Clicks (handles SPA navigation vs full redirects safely)
        resultsEl.querySelectorAll('.js-cat-nav').forEach(function(link) {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                closeResults();
                input.value = '';
                if (clearBtn) clearBtn.style.display = 'none';
                
                const href = this.getAttribute('href');
                if (typeof loadCategoryView === 'function') {
                    loadCategoryView(href);
                } else if (typeof loadProducts === 'function') {
                    window.location.href = href;
                } else {
                    window.location.href = href;
                }
            });
        });
    }

    input.addEventListener('input', function() {
        const term = this.value.trim();
        if (clearBtn) clearBtn.style.display = term ? '' : 'none';

        clearTimeout(debounceTimer);
        if (term.length < 2) {
            closeResults();
            return;
        }

        debounceTimer = setTimeout(async function() {
            try {
                const res  = await fetch(endpoint + (endpoint.includes('?') ? '&' : '?') + 'live_search=' + encodeURIComponent(term));
                const data = await res.json();
                renderResults(data);
            } catch (err) {
                console.error('Search autocomplete fetch error:', err);
            }
        }, 250);
    });

    if (clearBtn) {
        clearBtn.addEventListener('click', function() {
            input.value = '';
            clearBtn.style.display = 'none';
            closeResults();
            input.focus();
        });
    }

    // Keydown handler for the Enter key to redirect/filter products cleanly
    input.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const query = this.value.trim();
            if (query.length > 0) {
                closeResults();
                if (typeof loadProducts === 'function') {
                    loadProducts(endpoint + (endpoint.includes('?') ? '&' : '?') + 'q=' + encodeURIComponent(query));
                } else {
                    window.location.href = 'all-products.php?q=' + encodeURIComponent(query);
                }
            }
        }
    });

    document.addEventListener('click', function(e) {
        if (!e.target.closest('.cat-search-wrap')) closeResults();
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initSearch);
} else {
    initSearch();
}
})();