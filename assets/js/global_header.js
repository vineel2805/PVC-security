/* global_header.js
 * Handles global header functionality, including sticky behavior,
 mobile menu toggling,*/

document.addEventListener('DOMContentLoaded', function () {
    (function () {

        /* ── DOM refs ─────────────────────────────────────────── */
        const header = document.getElementById('pvc-global-header');
        const spacer = document.getElementById('pvc-header-spacer');
        const mobileToggle = document.getElementById('pvc-mobile-toggle');
        const mobileMenu = document.getElementById('pvc-mobile-menu');
        const mobileClose = document.getElementById('pvc-mobile-close');
        const overlay = document.getElementById('pvc-overlay');
        
        

        /* ── Header and Spacer clearance ──────────────────────── */
        function updateHeaderSpacing() {
            const isHomePage = (window.location.pathname.split('/').pop() || 'index.php') === 'index.php';
            if (window.innerWidth < 992) {
                if (isHomePage) {
                    spacer.style.height = '0px';
                } else {
                    // Mobile: Set fixed spacing using header height + 20px breathing room
                    spacer.style.height = (header.offsetHeight + 20) + 'px';
                }
                header.classList.add('sticky'); // Keep sticky styling on mobile
            } else {
                // Desktop: Reset to default flow, allow sticky scroll listener to manage it
                header.classList.remove('sticky');
                spacer.style.height = '0px';
                applyStickyState();
            }
        }

        /* ── Sticky header ────────────────────────────────────── */
        let lastIsSticky = false, ticking = false;
        function applyStickyState() {
            if (window.innerWidth < 992) return; // Managed by updateHeaderSpacing on mobile

            const shouldStick = window.scrollY > 50;
            if (shouldStick !== lastIsSticky) {
                lastIsSticky = shouldStick;
                if (shouldStick) {
                    spacer.style.height = header.offsetHeight + 'px';
                    header.classList.add('sticky');
                } else {
                    header.classList.remove('sticky');
                    spacer.style.height = '0px';
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
       
        /* ── Bottom Nav active highlight ──────────────────────── */
        (function () {
            const currentLoc = window.location.pathname.split('/').pop() || 'index.php';
            const params = new URLSearchParams(window.location.search);
            let activeId = '';

            if (currentLoc === 'index.php') {
                activeId = 'bottom-nav-home';
            } else if (currentLoc === 'all-products.php') {
                activeId = 'bottom-nav-brands';
            } else if (currentLoc === 'all-categories.php') {
                if (params.has('brand')) {
                    activeId = 'bottom-nav-brands';
                } else {
                    activeId = 'bottom-nav-categories';
                }
            } else if (currentLoc === 'cart.php') {
                activeId = 'bottom-nav-rfq';
            } else if (currentLoc === 'search.php') {
                activeId = 'bottom-nav-search';
            }

            if (activeId) {
                const activeEl = document.getElementById(activeId);
                if (activeEl) {
                    activeEl.classList.add('active');
                    activeEl.setAttribute('aria-current', 'page');
                }
            }
        })();

        /* ── Search init ──────────────────────────────────────── */
            Search.init({
                container: "#pvc-header-search-container",
                input: "#liveSearchInput",
                results: "#pvc-search-results",
                endpoint: "search-suggest.php"
            });
    })();
});
