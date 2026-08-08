document.addEventListener('DOMContentLoaded', function () {
    /* ==========================================================================
       1. DOM References
       ========================================================================== */
    const header = document.getElementById('pvc-global-header');
    const spacer = document.getElementById('pvc-header-spacer');
    const mobileToggle = document.getElementById('pvc-mobile-toggle');
    const mobileMenu = document.getElementById('pvc-mobile-menu');
    const mobileClose = document.getElementById('pvc-mobile-close');
    const overlay = document.getElementById('pvc-overlay');

    /* ==========================================================================
       2. Header and Spacer Clearance
       ========================================================================== */
    function updateHeaderSpacing() {
        if (!header || !spacer) return;

        spacer.style.height = header.offsetHeight + 'px';

        document.documentElement.style.setProperty(
            '--header-height',
            header.offsetHeight + 'px'
        );
    }

    /* ==========================================================================
       3. Sticky Header
       ========================================================================== */
    let lastIsSticky = false;
    let ticking = false;

    function applyStickyState() {
        if (!header) return;

        // Sticky desktop behavior only
        if (window.innerWidth < 992) {
            header.classList.remove('sticky');
            lastIsSticky = false;
            ticking = false;
            return;
        }

        const shouldStick = window.scrollY > 50;

        if (shouldStick !== lastIsSticky) {
            lastIsSticky = shouldStick;

            if (shouldStick) {
                header.classList.add('sticky');
            } else {
                header.classList.remove('sticky');
            }
        }

        ticking = false;
    }

    /* ==========================================================================
       4. Mobile Menu
       ========================================================================== */
    function openMobileMenu() {
        if (!mobileMenu || !overlay) return;

        mobileMenu.classList.add('active');
        overlay.classList.add('active');
        document.body.classList.add('no-scroll');

        mobileMenu.scrollTop = 0;

        if (mobileToggle) {
            mobileToggle.setAttribute('aria-expanded', 'true');
        }
    }

    function closeMobileMenu() {
        if (mobileMenu) {
            mobileMenu.classList.remove('active');
        }

        if (overlay) {
            overlay.classList.remove('active');
        }

        document.body.classList.remove('no-scroll');

        if (mobileToggle) {
            mobileToggle.setAttribute('aria-expanded', 'false');
        }
    }

    function toggleMobileMenu() {
        if (!mobileMenu) return;

        if (mobileMenu.classList.contains('active')) {
            closeMobileMenu();
        } else {
            openMobileMenu();
        }
    }

    /* ==========================================================================
       5. Mobile Menu Event Listeners
       ========================================================================== */
    if (mobileToggle) {
        mobileToggle.addEventListener('click', toggleMobileMenu);
    }

    if (mobileClose) {
        mobileClose.addEventListener('click', closeMobileMenu);
    }

    if (overlay) {
        overlay.addEventListener('click', closeMobileMenu);
    }

    /* ==========================================================================
       6. Bottom Navigation Active State
       ========================================================================== */
    document.querySelectorAll('.pvc-bottom-nav-item').forEach(function (item) {
        item.addEventListener('click', function () {
            document.querySelectorAll('.pvc-bottom-nav-item').forEach(function (el) {
                el.classList.remove('active');
                el.removeAttribute('aria-current');
            });

            this.classList.add('active');
            this.setAttribute('aria-current', 'page');
        });
    });

    /* ==========================================================================
       7. Active Navigation Highlight
       ========================================================================== */
    function highlightActiveNavigation() {
        const currentPage =
            window.location.pathname.split('/').pop() || 'index.php';

        const urlParams = new URLSearchParams(window.location.search);

        // Desktop navigation
        document.querySelectorAll('.pvc-nav-list .pvc-nav-link').forEach(link => {
            const href = link.getAttribute('href');

            if (href === 'all-categories.php') return;

            if (href === currentPage) {
                link.classList.add('active');
            }
        });

        // Mobile navigation
        document.querySelectorAll('.pvc-mobile-nav-link').forEach(link => {
            const parent = link.closest('.pvc-mobile-nav-item');
            const href = link.getAttribute('href');

            if (href === 'all-categories.php') return;

            if (href === currentPage) {
                link.classList.add('active');

                if (parent) {
                    parent.classList.add('active');
                }
            }
        });

        // Category / Brand specific logic
        if (currentPage === 'all-categories.php') {
            const isBrand = urlParams.has('brand');

            const desktopKey = isBrand
                ? 'nav-brand'
                : 'nav-categories';

            const mobileKey = isBrand
                ? 'mob-nav-brand'
                : 'mob-nav-categories';

            const desktopEl = document.getElementById(desktopKey);
            const mobileEl = document.getElementById(mobileKey);

            if (desktopEl) {
                desktopEl.classList.add('active');
            }

            if (mobileEl) {
                mobileEl.classList.add('active');

                const parent =
                    mobileEl.closest('.pvc-mobile-nav-item');

                if (parent) {
                    parent.classList.add('active');
                }
            }
        }
    }

    /* ==========================================================================
       8. Cart Badge Updates
       ========================================================================== */
    function updateCartCount() {
        const headerBadge =
            document.getElementById('pvc-cart-count');

        const bottomBadge =
            document.getElementById('pvc-bottom-cart-count');

        try {
            const cartData = localStorage.getItem('pvcCart');
            const cart = cartData ? JSON.parse(cartData) : [];

            const count = cart.reduce(
                (total, item) => total + (item.quantity || 0),
                0
            );

            const displayStyle = count > 0 ? 'flex' : 'none';

            if (headerBadge) {
                headerBadge.textContent = count;
                headerBadge.style.display = displayStyle;
            }

            if (bottomBadge) {
                bottomBadge.textContent = count;
                bottomBadge.style.display = displayStyle;
            }
        } catch (error) {
            if (headerBadge) {
                headerBadge.style.display = 'none';
            }

            if (bottomBadge) {
                bottomBadge.style.display = 'none';
            }
        }
    }

    /* ==========================================================================
       9. Initialization
       ========================================================================== */
    updateHeaderSpacing();

    window.addEventListener('resize', updateHeaderSpacing);
    window.addEventListener('load', updateHeaderSpacing);

    window.addEventListener(
        'scroll',
        () => {
            if (!ticking) {
                window.requestAnimationFrame(applyStickyState);
                ticking = true;
            }
        },
        { passive: true }
    );

    highlightActiveNavigation();
    updateCartCount();

    window.addEventListener('storage', updateCartCount);
    window.addEventListener('pvc-cart-updated', updateCartCount);
});