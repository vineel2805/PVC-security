
document.addEventListener('DOMContentLoaded', function () {
    (function () {

        /* ── DOM refs ─────────────────────────────────────────── */
        const header = document.getElementById('pvc-global-header');
        const spacer = document.getElementById('pvc-header-spacer');
        const mobileToggle = document.getElementById('pvc-mobile-toggle');
        const mobileMenu = document.getElementById('pvc-mobile-menu');
        const mobileClose = document.getElementById('pvc-mobile-close');
        const overlay = document.getElementById('pvc-overlay');
        const searchToggle = document.querySelector('.pvc-header-search-btn-toggle');
        const searchForm = document.getElementById('pvc-header-search-form');
        const searchContainer = document.getElementById('pvc-header-search-container');
        const searchInput = document.getElementById('liveSearchInput');
        const resultsBox = document.getElementById('pvc-search-results');

        /* ── Header and Spacer clearance ──────────────────────── */
        function updateHeaderSpacing() {
           if (!header || !spacer) return;

          spacer.style.height = header.offsetHeight + "px";
          document.documentElement.style.setProperty(
        "--header-height",
        header.offsetHeight + "px");
        console.log(
    window.location.pathname,
    header.offsetHeight
);
        }
        

        /* ── Sticky header ────────────────────────────────────── */
        let lastIsSticky = false, ticking = false;
        function applyStickyState() {
            if (window.innerWidth < 992) return; // Managed by updateHeaderSpacing on mobile

            const shouldStick = window.scrollY > 50;
            if (shouldStick !== lastIsSticky) {
                lastIsSticky = shouldStick;
                if (shouldStick) {
                    //spacer.style.height = header.offsetHeight + 'px';
                    header.classList.add('sticky');
                } else {
                    header.classList.remove('sticky');
                   // spacer.style.height = '0px';
                }
            }
            ticking = false;
        }
        window.addEventListener('scroll', () => {
            if (!ticking) { window.requestAnimationFrame(applyStickyState); ticking = true; }
        }, { passive: true });

        // Initial update and resize event registration
        updateHeaderSpacing();
        window.addEventListener('resize', updateHeaderSpacing);
        window.addEventListener('load', updateHeaderSpacing);

        /* ── Mobile menu ──────────────────────────────────────── */
        function openMobileMenu() {
            mobileMenu.classList.add('active');
            overlay.classList.add('active');
            document.body.classList.add('no-scroll');
            mobileMenu.scrollTop = 0;
        }
        function closeMobileMenu() {
            mobileMenu.classList.remove('active');
            overlay.classList.remove('active');
            document.body.classList.remove('no-scroll');
        }
        mobileToggle.addEventListener('click', () => mobileMenu.classList.contains('active') ? closeMobileMenu() : openMobileMenu());
        mobileClose.addEventListener('click', closeMobileMenu);
        overlay.addEventListener('click', closeMobileMenu);

        /* ── Active nav highlight ─────────────────────────────── */
        const currentPage = window.location.pathname.split('/').pop() || 'index.php';
        const urlParams = new URLSearchParams(window.location.search);

        document.querySelectorAll('.pvc-nav-list .pvc-nav-link').forEach(link => {
            const href = link.getAttribute('href');
            if (href === 'all-categories.php') return;
            if (href === currentPage) link.classList.add('active');
        });

        document.querySelectorAll('.pvc-mobile-nav-link').forEach(link => {
            const parent = link.closest('.pvc-mobile-nav-item');
            const href = link.getAttribute('href');
            if (href === 'all-categories.php') return;
            if (href === currentPage) {
                link.classList.add('active');
                if (parent) parent.classList.add('active');
            }
        });

        if (currentPage === 'all-categories.php') {
            const key = urlParams.has('brand') ? 'nav-brand' : 'nav-categories';
            const mobKey = urlParams.has('brand') ? 'mob-nav-brand' : 'mob-nav-categories';
            const el = document.getElementById(key);
            const elMob = document.getElementById(mobKey);
            if (el) el.classList.add('active');
            if (elMob) { elMob.classList.add('active'); elMob.closest('.pvc-mobile-nav-item').classList.add('active'); }
        }

        /* ── Cart badge ───────────────────────────────────────── */
        function updateCartCount() {
            const el = document.getElementById('pvc-cart-count');
            const elBottom = document.getElementById('pvc-bottom-cart-count');
            try {
                const cart = JSON.parse(localStorage.getItem('pvcCart')) || [];
                const count = cart.reduce((t, i) => t + (i.quantity || 0), 0);

                if (el) {
                    el.textContent = count;
                    el.style.display = count > 0 ? 'flex' : 'none';
                }
                if (elBottom) {
                    elBottom.textContent = count;
                    elBottom.style.display = count > 0 ? 'flex' : 'none';
                }
            } catch (e) {
                if (el) el.style.display = 'none';
                if (elBottom) elBottom.style.display = 'none';
            }
        }
        updateCartCount();
        window.addEventListener('storage', updateCartCount);
        window.addEventListener('pvc-cart-updated', updateCartCount);

        /* ── Search helpers ───────────────────────────────────── */
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

                    fetch('search-suggest.php?q=' + encodeURIComponent(query), {
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
    })();
});
