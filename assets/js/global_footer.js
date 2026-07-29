/**
 * PVC SECURITY - GLOBAL FOOTER COMPONENT
 * Handles footer injection and interactive contact links.
 */

const PVC_FOOTER_DATA = {
    logoPath: 'assets/img/logo/logo1.png',
    description: 'PVC Security provides professional CCTV and surveillance solutions for homes, shops, offices, and industries.',
    phone1: '+91 91144 56666',
    phone1Clean: '+919114456666',
    phone2: '+91 91144 67777',
    phone2Clean: '+919114467777',
    email: 'pvcsecurity@gmail.com',
    address: 'NEAR KLM SHOPPING MALL, MAVULLAMMA TEMPLE ROAD, Bhimavaram Town - 1',
    serviceArea: 'Andhra Pradesh & Telangana',
    whatsapp: '+919114456666',
    workingHours: 'Mon - Sat: 9:00 AM - 9:00 PM'
};

function initPvcFooter() {
    // Prevent duplicate injection
    if (document.querySelector('.pvc-global-footer')) return;

    const footerHtml = `
    <footer class="pvc-global-footer">
        <div class="pvc-footer-container">
            <div class="pvc-footer-row">
                <!-- 1. BRAND SECTION -->
                <div class="pvc-footer-col">
                    <div class="pvc-footer-logo">
                        <img src="${PVC_FOOTER_DATA.logoPath}" alt="PVC Security Logo" width="200" height="80" style="object-fit: contain;">
                    </div>
                    <p class="pvc-footer-desc">${PVC_FOOTER_DATA.description}</p>
                    <div class="pvc-trust-icons">
                        <div class="pvc-trust-item"><i class="fa-solid fa-shield-check"></i> Genuine Products</div>
                        <div class="pvc-trust-item"><i class="fa-solid fa-headset"></i> Expert Support</div>
                    </div>
                </div>

                <!-- 2. QUICK LINKS -->
                <div class="pvc-footer-col">
                    <h4 class="pvc-footer-title">Quick Links</h4>
                    <ul class="pvc-footer-links">
                        <li><a href="index.php">Home</a></li>
                        <li><a href="about-us.php">About Us</a></li>
                        <li><a href="all-products.php">Shop by Brand</a></li>
                        <li><a href="all-categories.php?category=ACCESSORIES">Shop by Categories</a></li>
                        <li><a href="services.php">Services</a></li>
                        <li><a href="contact-us.php">Contact Us</a></li>
                    </ul>
                </div>

                <!-- 3. CONTACT INFORMATION -->
                <div class="pvc-footer-col">
                    <h4 class="pvc-footer-title">Contact Support</h4>
                    <ul class="pvc-contact-list">
                        <li class="pvc-contact-item">
                            <a href="tel:${PVC_FOOTER_DATA.phone1Clean}" class="pvc-contact-icon" aria-label="Call Primary"><i class="fa-solid fa-phone"></i></a>
                            <div class="pvc-contact-text">
                                <span>Call Primary</span>
                                <a href="tel:${PVC_FOOTER_DATA.phone1Clean}">${PVC_FOOTER_DATA.phone1}</a>
                            </div>
                        </li>
                        <li class="pvc-contact-item">
                            <a href="tel:${PVC_FOOTER_DATA.phone2Clean}" class="pvc-contact-icon" aria-label="Call Secondary"><i class="fa-solid fa-phone"></i></a>
                            <div class="pvc-contact-text">
                                <span>Call Secondary</span>
                                <a href="tel:${PVC_FOOTER_DATA.phone2Clean}">${PVC_FOOTER_DATA.phone2}</a>
                            </div>
                        </li>
                        <li class="pvc-contact-item">
                            <a href="mailto:${PVC_FOOTER_DATA.email}" class="pvc-contact-icon" aria-label="Email Us"><i class="fa-solid fa-envelope"></i></a>
                            <div class="pvc-contact-text">
                                <span>Email Address</span>
                                <a href="mailto:${PVC_FOOTER_DATA.email}">${PVC_FOOTER_DATA.email}</a>
                            </div>
                        </li>
                        <li class="pvc-contact-item">
                            <a href="contact-us.php" class="pvc-contact-icon" aria-label="Our Location"><i class="fa-solid fa-location-dot"></i></a>
                            <div class="pvc-contact-text">
                                <span>Our Address</span>
                                <p>${PVC_FOOTER_DATA.address}</p>
                            </div>
                        </li>
                    </ul>
                </div>

                <!-- 4. SUPPORT & CONNECT -->
                <div class="pvc-footer-col">
                    <h4 class="pvc-footer-title">Secure Now</h4>
                    <div class="pvc-action-btns">
                        <a href="https://wa.me/${PVC_FOOTER_DATA.whatsapp}" target="_blank" class="pvc-footer-btn pvc-btn-whatsapp" aria-label="Chat on WhatsApp">
                            <i class="fa-brands fa-whatsapp"></i> WhatsApp Chat
                        </a>
                    </div>
                    <div class="pvc-trust-icons" style="margin-bottom: 0px;">
                        <span style="font-size: 13px; color: rgba(255,255,255,0.5);"><i class="fa-regular fa-clock"></i> ${PVC_FOOTER_DATA.workingHours}</span>
                    </div>
                </div>
            </div>

        </div>

        <div class="pvc-footer-bottom-bar" style="background-color: #000000; text-align: center; padding: 20px 0; width: 100%; position: relative; z-index: 10;">
            <p style="background: var(--pvc-gradient); -webkit-background-clip: text; background-clip: text; -webkit-text-fill-color: transparent; font-weight: 900; font-size: 15px; letter-spacing: 1.5px; margin-bottom: 5px; text-transform: uppercase; font-family: 'Outfit', sans-serif;">WEB DEVELOPMENT TEAM</p>
            <p style="color: #ffffff; font-size: 14px; margin: 0; font-family: 'Inter', sans-serif;">
                © ${new Date().getFullYear()} | All Rights Reserved | <a href="https://bhimavaramdigitals.com/" target="_blank" style="color: #2196F3; text-decoration: none; font-weight: 600; transition: color 0.3s;" onmouseover="this.style.color='#64B5F6'" onmouseout="this.style.color='#2196F3'">Bhimavaram Digitals.</a>
            </p>
        </div>

        <!-- Global Floating Call Button (Mobile Only) -->
        <a href="tel:${PVC_FOOTER_DATA.phone1Clean}" class="pvc-floating-call" aria-label="Call Us">
            <i class="fa-solid fa-phone"></i>
        </a>

        <!-- Global Floating WhatsApp Button -->
        <a href="https://wa.me/${PVC_FOOTER_DATA.whatsapp}" target="_blank" class="pvc-floating-whatsapp" aria-label="WhatsApp Us">
            <i class="fa-brands fa-whatsapp"></i>
        </a>
    </footer>
    `;

    document.body.insertAdjacentHTML('beforeend', footerHtml);

    // --- ACTIVE PAGE HIGHLIGHTING ---
    const currentPagePath = window.location.pathname.split('/').pop() || 'index.php';
    const queryParams = new URLSearchParams(window.location.search);
    const hasCategoryFilter = queryParams.has('category') || queryParams.has('cat');

    document.querySelectorAll('.pvc-footer-links a').forEach(link => {
        const href = link.getAttribute('href');

        if (hasCategoryFilter) {
            const catVal = (queryParams.get('category') || queryParams.get('cat')).toUpperCase();

            // If it's the "Shop by Categories" link, check if it's a component
            if (href.includes('category=ACCESSORIES') && catVal !== 'ALL CATEGORIES') {
                // If the current category is one of the component categories (from header config)
                // We don't have direct access here but we can check if it matches common ones
                const components = ["ACCESSORIES", "CABLES", "HDD", "MONITOR", "RACK", "SD CARDS"];
                if (components.includes(catVal)) {
                    link.classList.add('active');
                    return;
                }
            }

            // If it's "New Products", we might want it active for brands if we want to follow header logic
            // But usually brands aren't "New Products". Header highlights "New Products" only for the exact page.
        }

        // Standard matching
        if (href === currentPagePath || (queryParams.toString() && href.includes(currentPagePath) && href.includes(queryParams.toString()))) {
            link.classList.add('active');
        } else if (href === currentPagePath && !hasCategoryFilter) {
            link.classList.add('active');
        }
    });
}

// Global Close Handler for Mobile CTA
document.addEventListener('click', function (e) {
    if (e.target.closest('#pvcStickyClose')) {
        const cta = document.getElementById('pvcMobileStickyCta');
        if (cta) {
            cta.style.display = 'none';
        }
    }
});

// Auto-load dependencies
if (!document.querySelector('link[href*="global_footer.css"]')) {
    const link = document.createElement('link');
    link.rel = 'stylesheet';
    link.href = 'assets/css/global_footer.css';
    document.head.appendChild(link);
}

// Ensure FontAwesome
if (!document.getElementById('fa-link') && !document.querySelector('link[href*="fontawesome"]')) {
    const fa = document.createElement('link');
    fa.id = 'fa-link';
    fa.rel = 'stylesheet';
    fa.href = 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css';
    document.head.appendChild(fa);
}

// Run on load
if (document.readyState === "complete" || document.readyState === "interactive") {
    initPvcFooter();
} else {
    document.addEventListener('DOMContentLoaded', initPvcFooter);
}
