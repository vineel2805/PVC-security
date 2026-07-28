/**
 * PVC SECURITY - GLOBAL HEADER COMPONENT
 * Handles header injection, navigation, and core interactions.
 */

const PVC_HEADER_CONFIG = {
    logoPath: 'assets/img/logo/logo1.png',
    phone: '+919114456666',
    searchUrl: 'all-products.php', // Point to all-products which has the list
    brands: [
        { name: 'Anwiz', url: 'all-products.php?category=ANWIZ' },
        { name: 'D-Link', url: 'all-products.php?category=D-LINK' },
        { name: 'Dahua', url: 'all-products.php?category=DAHUA' },
        { name: 'ERD', url: 'all-products.php?category=ERD' },
        { name: 'Ezviz', url: 'all-products.php?category=EZVIZ' },
        { name: 'Finolex', url: 'all-products.php?category=FINOLEX' },
        { name: 'Hi Focus', url: 'all-products.php?category=HI FOCUS' },
        { name: 'Hikvision', url: 'all-products.php?category=HIKVISION' },
        { name: 'Imou', url: 'all-products.php?category=IMOU' },
        { name: 'Mastel', url: 'all-products.php?category=MASTEL' },
        { name: 'Maxxion', url: 'all-products.php?category=MAXXION' },
        { name: 'Secureye', url: 'all-products.php?category=SECUREYE' },
        { name: 'Securus', url: 'all-products.php?category=SECURUS' },
        { name: 'TP Link', url: 'all-products.php?category=TP LINK' },
        { name: 'True View', url: 'all-products.php?category=TRUE VIEW' },
        { name: 'Zebronics', url: 'all-products.php?category=ZEBRONICS' },
        { name: 'Yadon', url: 'all-products.php?category=YADON' },
        { name: 'CP Plus', url: 'all-products.php?category=CP PLUS' }
    ],
    categories: [
        { name: 'Accessories', url: 'all-products.php?category=ACCESSORIES' },
        { name: 'Cables', url: 'all-products.php?category=CABLES' },
        { name: 'Hard Disks (HDD)', url: 'all-products.php?category=HDD' },
        { name: 'Monitors', url: 'all-products.php?category=MONITOR' },
        { name: 'Racks', url: 'all-products.php?category=RACK' },
        { name: 'SD Cards', url: 'all-products.php?category=SD CARDS' }
    ]
};

function initPvcHeader() {
    const headerHtml = `
    <header class="pvc-global-header" id="pvc-global-header">
        <div class="pvc-header-container">
            <div class="pvc-header-logo">
                <a href="index.php">
                    <img src="${PVC_HEADER_CONFIG.logoPath}" alt="PVC Security Logo" width="180" height="60" style="object-fit: contain;">
                </a>
            </div>

            <nav class="pvc-main-nav">
                <ul class="pvc-nav-list">
                    <li class="pvc-nav-item"><a href="index.php" class="pvc-nav-link" id="nav-home">Home</a></li>
                    <li class="pvc-nav-item"><a href="about-us.php" class="pvc-nav-link" id="nav-about">About Us</a></li>
                    
                    <li class="pvc-nav-item has-mega-brand">
                        <a href="#" class="pvc-nav-link">Shop by Brand</a>
                        <div class="pvc-mega-menu">
                            <div class="pvc-mega-column">
                                <h4 class="pvc-mega-title">Our Partner Brands</h4>
                                <ul class="pvc-mega-list">
                                    ${PVC_HEADER_CONFIG.brands.map(b => `<li><a href="${b.url}">${b.name}</a></li>`).join('')}
                                </ul>
                            </div>
                        </div>
                    </li>

                    <li class="pvc-nav-item has-mega-category">
                        <a href="#" class="pvc-nav-link">Shop by Categories</a>
                        <div class="pvc-mega-menu">
                            <div class="pvc-mega-column">
                                <h4 class="pvc-mega-title">Product Components</h4>
                                <ul class="pvc-mega-list">
                                    ${PVC_HEADER_CONFIG.categories.map(c => `<li><a href="${c.url}">${c.name}</a></li>`).join('')}
                                </ul>
                            </div>
                        </div>
                    </li>

                    <li class="pvc-nav-item"><a href="all-products.php" class="pvc-nav-link" id="nav-new">New Products</a></li>
                    <li class="pvc-nav-item"><a href="services.php" class="pvc-nav-link" id="nav-services">Services</a></li>
                    <li class="pvc-nav-item"><a href="contact-us.php" class="pvc-nav-link" id="nav-contact">Contact Us</a></li>
                </ul>
            </nav>

            <div class="pvc-header-utils">
                <!-- Inline Expandable Search -->
                <div class="pvc-search-container">
                    <form class="pvc-search-form-inline" action="all-products.php" method="GET">
                        <input type="text" id="liveSearchInput" name="search" class="pvc-search-input-inline" placeholder="Search products..." autocomplete="off">
                        <button type="submit" class="pvc-search-submit" aria-label="Submit Search">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </button>
                    </form>
                    <div class="pvc-search-results" id="pvc-search-results" role="listbox" aria-label="Search suggestions"></div>
                    <button class="pvc-util-btn pvc-search-btn-toggle" aria-label="Search Toggle">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>
                </div>

                <a href="tel:${PVC_HEADER_CONFIG.phone}" class="pvc-util-btn pvc-phone-btn" aria-label="Call Now">
                    <i class="fa-solid fa-phone"></i>
                </a>
                <a href="cart.php" class="pvc-util-btn pvc-cart-btn" aria-label="Shopping Cart">
                    <i class="fa-solid fa-cart-shopping"></i>
                    <span class="pvc-cart-badge" id="pvc-cart-count">0</span>
                </a>
                <div class="pvc-mobile-toggle" id="pvc-mobile-toggle">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </div>
        </div>
    </header>

    <!-- Mobile Menu -->
    <div class="pvc-overlay" id="pvc-overlay"></div>
    <div class="pvc-mobile-menu" id="pvc-mobile-menu">
        <div class="pvc-mobile-close" id="pvc-mobile-close">
            <i class="fa-solid fa-xmark"></i>
        </div>
        <ul class="pvc-mobile-nav-list">
            <li class="pvc-mobile-nav-item"><a href="index.php" class="pvc-mobile-nav-link">Home</a></li>
            <li class="pvc-mobile-nav-item"><a href="about-us.php" class="pvc-mobile-nav-link">About Us</a></li>
            
            <li class="pvc-mobile-nav-item has-submenu">
                <div class="pvc-mobile-link-wrapper">
                    <a href="#" class="pvc-mobile-nav-link">Shop by Brand</a>
                </div>
                <div class="pvc-mobile-submenu">
                    ${PVC_HEADER_CONFIG.brands.map(b => `<a href="${b.url}" class="pvc-mobile-sub-link">${b.name}</a>`).join('')}
                </div>
            </li>

            <li class="pvc-mobile-nav-item has-submenu">
               <div class="pvc-mobile-link-wrapper">
                    <a href="#" class="pvc-mobile-nav-link">Shop by Categories</a>
                </div>
                <div class="pvc-mobile-submenu">
                    ${PVC_HEADER_CONFIG.categories.map(c => `<a href="${c.url}" class="pvc-mobile-sub-link">${c.name}</a>`).join('')}
                </div>
            </li>

            <li class="pvc-mobile-nav-item"><a href="all-products.php" class="pvc-mobile-nav-link">New Products</a></li>
            <li class="pvc-mobile-nav-item"><a href="services.php" class="pvc-mobile-nav-link">Services</a></li>
            <li class="pvc-mobile-nav-item"><a href="contact-us.php" class="pvc-mobile-nav-link">Contact Us</a></li>
        </ul>

        <div class="pvc-mobile-contact">
            <h4 class="pvc-mobile-contact-title">Quick Connect</h4>
            <div class="pvc-mobile-btns">
                <a href="tel:+919114456666" class="pvc-mobile-btn btn-call"><i class="fa-solid fa-phone"></i> Call Now</a>
                <a href="https://wa.me/919114456666" class="pvc-mobile-btn btn-whatsapp"><i class="fa-brands fa-whatsapp"></i> WhatsApp</a>
            </div>
            <div class="pvc-mobile-numbers">
                <a href="tel:+919114456666" class="pvc-mobile-num">+91 91144 56666</a>
                <a href="tel:+919114467777" class="pvc-mobile-num">+91 91144 67777</a>
            </div>
        </div>
    </div>
    `;

    // Inject into body (or a specific target if needed)
    document.body.insertAdjacentHTML('afterbegin', headerHtml);

    // --- INTERACTION LOGIC ---

    const header = document.getElementById('pvc-global-header');
    const mobileToggle = document.getElementById('pvc-mobile-toggle');
    const mobileMenu = document.getElementById('pvc-mobile-menu');
    const mobileClose = document.getElementById('pvc-mobile-close'); // New Close Button
    const overlay = document.getElementById('pvc-overlay');
    const searchTrigger = document.querySelector('.pvc-search-trigger');
    const searchOverlay = document.getElementById('pvc-search-overlay');
    const searchClose = document.getElementById('pvc-search-close');

    // Sticky Header
    window.addEventListener('scroll', () => {
        if (window.scrollY > 50) {
            header.classList.add('sticky');
        } else {
            header.classList.remove('sticky');
        }
    });

    // Mobile Menu Toggle
    function toggleMobileMenu() {
        mobileMenu.classList.toggle('active');
        overlay.classList.toggle('active');
        document.body.classList.toggle('no-scroll');

        // Reset scroll position when opening/closing
        if (mobileMenu.classList.contains('active')) {
            mobileMenu.scrollTop = 0;
        }
    }

    mobileToggle.addEventListener('click', toggleMobileMenu);
    mobileClose.addEventListener('click', toggleMobileMenu); // Close event
    overlay.addEventListener('click', toggleMobileMenu);

    // Prevent desktop nav top-level '#' links from jumping the page
    document.querySelectorAll('.pvc-nav-item.has-mega-brand > a[href="#"], .pvc-nav-item.has-mega-category > a[href="#"]').forEach(link => {
        link.addEventListener('click', (e) => e.preventDefault());
    });

    // Mobile Mega Menu Toggle
    document.querySelectorAll('.pvc-mobile-link-wrapper').forEach(wrapper => {
        wrapper.addEventListener('click', (e) => {
            e.preventDefault();
            const parent = wrapper.closest('.pvc-mobile-nav-item');
            const subMenu = parent.querySelector('.pvc-mobile-submenu');
            const icon = wrapper.querySelector('.pvc-mobile-toggle-btn');
            const linkText = wrapper.querySelector('.pvc-mobile-nav-link');

            parent.classList.toggle('active');

            if (parent.classList.contains('active')) {
                subMenu.style.maxHeight = subMenu.scrollHeight + "px";
                if (icon) icon.style.transform = 'rotate(90deg)';
                linkText.style.background = 'linear-gradient(to right, #BF953F, #FCF6BA, #B38728, #FBF5B7, #AA771C)';
                linkText.style.webkitBackgroundClip = 'text';
                linkText.style.webkitTextFillColor = 'transparent';
            } else {
                subMenu.style.maxHeight = null;
                if (icon) icon.style.transform = 'rotate(0deg)';
                linkText.style.background = 'none';
                linkText.style.webkitTextFillColor = 'var(--pvc-white)';
                linkText.style.color = 'var(--pvc-white)';
            }
        });
    });

    // --- GLOBAL SEARCH SYSTEM ---

    const PVC_SEARCH_MAPPING = {
        // High Priority Static Pages
        pages: {
            "contact": "contact-us.php",
            "support": "contact-us.php",
            "phone": "contact-us.php",
            "call": "contact-us.php",
            "address": "contact-us.php",
            "location": "contact-us.php",
            "about": "about-us.php",
            "company": "about-us.php",
            "profile": "about-us.php",
            "service": "services.php",
            "installation": "services.php",
            "repair": "services.php",
            "amc": "services.php",
            "cart": "cart.php",
            "basket": "cart.php",
            "home": "index.php"
        },
        // Categories
        categories: [
            { keys: ["ip", "network", "wifi", "wireless", "ethernet"], url: "network-cameras.php" },
            { keys: ["turbo", "hd", "analog", "coaxial", "cctv"], url: "turbo-hd.php" },
            { keys: ["access", "biometric", "fingerprint", "attendance"], url: "access-control.php" },
            { keys: ["intercom", "door", "bell", "videophone"], url: "video-intercom.php" },
            { keys: ["alarm", "instrusion", "sensor", "theft"], url: "alarm-systems.php" },
            { keys: ["display", "screen", "wall"], url: "all-products.php?category=MONITOR" },
            { keys: ["thermal", "heat"], url: "thermal-products.php" },
            { keys: ["traffic", "anpr", "plate", "speed"], url: "intelligent-traffic.php" },
            { keys: ["audio", "speaker", "sound"], url: "audio-products.php" },
            { keys: ["soft", "vms", "licen"], url: "software.php" },
            { keys: ["cable", "switch", "router", "connector"], url: "all-products.php?cat=Accessories" }
        ],
        // Brands
        brands: [
            { keys: ["hikvision", "hik"], url: "all-products.php?category=HIKVISION" },
            { keys: ["dahua"], url: "all-products.php?category=DAHUA" },
            { keys: ["cp", "plus", "cpplus"], url: "all-products.php?category=CP PLUS" },
            { keys: ["imou"], url: "all-products.php?category=IMOU" },
            { keys: ["ezviz"], url: "all-products.php?category=EZVIZ" },
            { keys: ["anwiz"], url: "all-products.php?category=ANWIZ" },
            { keys: ["d-link", "dlink"], url: "all-products.php?category=D-LINK" },
            { keys: ["erd", "edr"], url: "all-products.php?category=ERD" },
            { keys: ["finolex"], url: "all-products.php?category=FINOLEX" },
            { keys: ["hi focus", "hifocus", "hificous"], url: "all-products.php?category=HI FOCUS" },
            { keys: ["master", "mastel"], url: "all-products.php?category=MASTEL" },
            { keys: ["maxxion"], url: "all-products.php?category=MAXXION" },
            { keys: ["securi", "secureye"], url: "all-products.php?category=SECUREYE" },
            { keys: ["securus"], url: "all-products.php?category=SECURUS" },
            { keys: ["tp link", "tplink"], url: "all-products.php?category=TP LINK" },
            { keys: ["true view", "trueview"], url: "all-products.php?category=TRUE VIEW" },
            { keys: ["zebronics"], url: "all-products.php?category=ZEBRONICS" },
            { keys: ["yadon"], url: "all-products.php?category=YADON" }
        ]
    };

    function handleGlobalSearch(query) {
        const cleanQuery = (query || '').toLowerCase().trim();
        if (!cleanQuery) return;

        // 1) Exact/static pages
        for (const [key, url] of Object.entries(PVC_SEARCH_MAPPING.pages)) {
            if (cleanQuery === key || cleanQuery.includes(key)) {
                window.location.href = url;
                return;
            }
        }

        // 2) Brand/category direct route
        for (const brand of PVC_SEARCH_MAPPING.brands) {
            if (brand.keys.some(k => cleanQuery.includes(k) || k.includes(cleanQuery))) {
                window.location.href = brand.url;
                return;
            }
        }
        for (const cat of PVC_SEARCH_MAPPING.categories) {
            if (cat.keys.some(k => cleanQuery.includes(k) || k.includes(cleanQuery))) {
                window.location.href = cat.url;
                return;
            }
        }

        // 3) Product search fallback
        window.location.href = `all-products.php?q=${encodeURIComponent(cleanQuery)}`;
    }

    const normalizeSearch = (v) => (v || '').toString().toLowerCase().replace(/\s+/g, ' ').trim();
    const escapeHtml = (s) => (s || '').replace(/[&<>"']/g, (ch) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[ch]));
    const debounce = (fn, ms = 120) => {
        let t;
        return (...args) => {
            clearTimeout(t);
            t = setTimeout(() => fn(...args), ms);
        };
    };

    class PvcSmartSearch {
        constructor({ input, form, panel }) {
            this.input = input;
            this.form = form;
            this.panel = panel;
            this.activeIndex = -1;
            this.results = [];
            this.index = [];
            this.maxResults = 8;
            this.ready = false;
            this.categoryThumbMap = {
                'ACCESSORIES': 'assets/img/CATEGORY%20BANNERS/ACCESSORIES.svg',
                'ANWIZ': 'assets/img/CATEGORY%20BANNERS/ANVIZ.svg',
                'CABLES': 'assets/img/CATEGORY%20BANNERS/ACCESSORIES.svg',
                'CP PLUS': 'assets/img/CATEGORY%20BANNERS/ACCESSORIES.svg',
                'D-LINK': 'assets/img/CATEGORY%20BANNERS/D-LINK.svg',
                'DAHUA': 'assets/img/CATEGORY%20BANNERS/DAHUA.svg',
                'ERD': 'assets/img/CATEGORY%20BANNERS/EDR.svg',
                'EZVIZ': 'assets/img/CATEGORY%20BANNERS/EZVIZ.svg',
                'FINOLEX': 'assets/img/CATEGORY%20BANNERS/FINOLEX.svg',
                'HI FOCUS': 'assets/img/CATEGORY%20BANNERS/HI%20FOCUS.svg',
                'HIKVISION': 'assets/img/CATEGORY%20BANNERS/HIKVISON.svg',
                'IMOU': 'assets/img/CATEGORY%20BANNERS/IMOU.svg',
                'MASTEL': 'assets/img/CATEGORY%20BANNERS/MASTEL.svg',
                'MAXXION': 'assets/img/CATEGORY%20BANNERS/MAXXION.svg',
                'MONITOR': 'assets/img/CATEGORY%20BANNERS/MONITOR.svg',
                'HDD': 'assets/img/CATEGORY%20BANNERS/HDD.svg',
                'RACK': 'assets/img/CATEGORY%20BANNERS/RACK.svg',
                'SD CARDS': 'assets/img/CATEGORY%20BANNERS/SD%20CARDS.svg',
                'SECUREYE': 'assets/img/CATEGORY%20BANNERS/SECUREYE.svg',
                'SECURUS': 'assets/img/CATEGORY%20BANNERS/SECRUS.svg',
                'TP LINK': 'assets/img/CATEGORY%20BANNERS/TP%20LINK.svg',
                'TRUE VIEW': 'assets/img/CATEGORY%20BANNERS/TRUEVIEW.svg',
                'YADON': 'assets/img/CATEGORY%20BANNERS/YADON.svg',
                'ZEBRONICS': 'assets/img/CATEGORY%20BANNERS/ZEBRONICS.svg'
            };
            this.init();
        }

        async init() {
            this.index = this.buildBaseIndex();
            this.renderPopular();
            this.bindEvents();
            await this.loadProductIndex();
            this.ready = true;
        }

        buildBaseIndex() {
            const items = [];

            Object.entries(PVC_SEARCH_MAPPING.pages).forEach(([key, url]) => {
                items.push({ type: 'page', title: key.charAt(0).toUpperCase() + key.slice(1), subtitle: 'Page', category: 'Page', keywords: [key], url });
            });

            PVC_HEADER_CONFIG.brands.forEach((b) => {
                items.push({ type: 'brand', title: b.name, subtitle: 'Brand', category: 'Brand', keywords: [b.name, 'brand'], url: b.url });
            });

            PVC_HEADER_CONFIG.categories.forEach((c) => {
                items.push({ type: 'category', title: c.name, subtitle: 'Category', category: 'Category', keywords: [c.name, 'category', 'products'], url: c.url });
            });

            return items.map(this.prepareItem);
        }

        prepareItem(item) {
            const keyPool = [item.title, item.subtitle, item.category, ...(item.keywords || [])].join(' ');
            return {
                ...item,
                normTitle: normalizeSearch(item.title),
                normKeyPool: normalizeSearch(keyPool)
            };
        }

        categoryFromArrayName(nameVar) {
            const key = (nameVar || '').replace(/Names$/, '');
            const map = {
                accessory: 'ACCESSORIES', cable: 'CABLES', cpPlus: 'CP PLUS', dlink: 'D-LINK', dahua: 'DAHUA', erd: 'ERD', ezviz: 'EZVIZ',
                finolex: 'FINOLEX', hiFocus: 'HI FOCUS', hikvision: 'HIKVISION', imou: 'IMOU', mastel: 'MASTEL', maxxion: 'MAXXION',
                monitor: 'MONITOR', hdd: 'HDD', sdCard: 'SD CARDS', secureye: 'SECUREYE', securus: 'SECURUS', tplink: 'TP LINK',
                trueview: 'TRUE VIEW', yadon: 'YADON', zebronics: 'ZEBRONICS', prama: 'PRAMA', smartPro: 'SMART PRO', rack: 'RACK',
                vguard: 'VGUARD', voltaic: 'VOLTAIC', coef: 'COEF', anwiz: 'ANWIZ'
            };
            return map[key] || key.replace(/([A-Z])/g, ' $1').trim().toUpperCase();
        }

        async loadProductIndex() {
            try {
                const cacheKey = 'pvc-search-index-v3';
                const cached = sessionStorage.getItem(cacheKey);
                if (cached) {
                    const parsed = JSON.parse(cached);
                    this.index = this.index.concat(parsed.map(this.prepareItem));
                    return;
                }

                const res = await fetch('assets/js/all-products.js', { cache: 'force-cache' });
                if (!res.ok) return;
                const source = await res.text();

                const forcedImageMap = this.parseForcedImageMap(source);
                const imageMeta = this.parseImageMeta(source);

                const listRegex = /const\s+(\w+Names)\s*=\s*\[([\s\S]*?)\];/g;
                const strRegex = /"([^"\\]*(?:\\.[^"\\]*)*)"/g;
                const products = [];

                let listMatch;
                while ((listMatch = listRegex.exec(source)) !== null) {
                    const varName = listMatch[1];
                    const block = listMatch[2] || '';
                    const category = this.categoryFromArrayName(varName);

                    let m;
                    while ((m = strRegex.exec(block)) !== null) {
                        const title = (m[1] || '').trim();
                        if (!title || title.length < 2) continue;
                        products.push({
                            type: 'product',
                            title,
                            subtitle: category,
                            category,
                            keywords: [category, ...title.split(/\s+/).slice(0, 4)],
                            image: this.resolveProductImage(title, category, imageMeta, forcedImageMap),
                            url: `all-products.php?q=${encodeURIComponent(title)}`
                        });
                    }
                }

                const dedup = new Map();
                for (const p of products) {
                    const key = `${normalizeSearch(p.title)}|${normalizeSearch(p.category)}`;
                    if (!dedup.has(key)) dedup.set(key, p);
                }
                const finalProducts = Array.from(dedup.values());
                sessionStorage.setItem(cacheKey, JSON.stringify(finalProducts));
                this.index = this.index.concat(finalProducts.map(this.prepareItem));
            } catch (e) {
                // silent fail: base index still works
            }
        }

        parseForcedImageMap(source) {
            const out = new Map();
            const re = /\[_normalize\("([^"]+)"\)\]\s*:\s*"([^"]+)"/g;
            let m;
            while ((m = re.exec(source)) !== null) {
                out.set(normalizeSearch(m[1] || ''), m[2] || '');
            }
            return out;
        }

        parseImageMeta(source) {
            const imageArrays = new Map();
            const arrayRegex = /const\s+(\w+ImageFiles)\s*=\s*\[([\s\S]*?)\];/g;
            const fileRegex = /"([^"\\]*(?:\\.[^"\\]*)*)"/g;

            let m;
            while ((m = arrayRegex.exec(source)) !== null) {
                const varName = m[1];
                const block = m[2] || '';
                const files = [];
                let fm;
                while ((fm = fileRegex.exec(block)) !== null) {
                    files.push((fm[1] || '').trim());
                }
                imageArrays.set(varName, files);
            }

            const byCategory = new Map();
            const catRegex = /"([^"]+)"\s*:\s*\{\s*list:\s*(\w+)\s*,\s*path:\s*"([^"]+)"\s*\}/g;
            let cm;
            while ((cm = catRegex.exec(source)) !== null) {
                const category = (cm[1] || '').toUpperCase();
                const listVar = cm[2] || '';
                const path = cm[3] || '';
                byCategory.set(category, {
                    files: imageArrays.get(listVar) || [],
                    path
                });
            }

            return { byCategory };
        }

        encodePathKeepSlashes(p) {
            return (p || '')
                .split('/')
                .map(seg => (seg === '' || seg === '.' || seg === '..') ? seg : encodeURIComponent(seg))
                .join('/');
        }

        buildImageUrl(pathPrefix, file) {
            return this.encodePathKeepSlashes(`${pathPrefix || ''}${file || ''}`);
        }

        tokenOverlapScore(a, b) {
            const t1 = normalizeSearch(a).split(' ').filter(t => t.length > 1);
            const t2 = new Set(normalizeSearch(b).split(' ').filter(t => t.length > 1));
            if (!t1.length || !t2.size) return 0;
            let hit = 0;
            for (const t of t1) if (t2.has(t)) hit += 1;
            return hit / Math.max(t1.length, 1);
        }

        resolveProductImage(title, category, imageMeta, forcedImageMap) {
            const pn = normalizeSearch(title);
            if (!pn) return null;

            const forced = forcedImageMap.get(pn);
            if (forced) return forced;

            const cfg = imageMeta?.byCategory?.get((category || '').toUpperCase());
            if (!cfg || !cfg.files || !cfg.files.length) return null;

            // 1) Exact-ish filename match
            for (const f of cfg.files) {
                const base = (f || '').split('/').pop().replace(/\.[^.]+$/, '');
                const fn = normalizeSearch(base);
                if (!fn) continue;
                if (fn.includes(pn) || pn.includes(fn)) {
                    return this.buildImageUrl(cfg.path, f);
                }
            }

            // 2) Token-overlap best match
            let bestFile = null;
            let best = 0;
            for (const f of cfg.files) {
                const base = (f || '').split('/').pop().replace(/\.[^.]+$/, '');
                const sc = this.tokenOverlapScore(title, base);
                if (sc > best) {
                    best = sc;
                    bestFile = f;
                }
            }
            if (bestFile && best >= 0.4) {
                return this.buildImageUrl(cfg.path, bestFile);
            }

            return null;
        }

        score(item, query, tokens) {
            let s = 0;
            if (item.normTitle === query) s += 120;
            if (item.normTitle.startsWith(query)) s += 90;
            if (item.normTitle.includes(query)) s += 60;
            if (item.normKeyPool.includes(query)) s += 40;
            if (tokens.every(t => item.normKeyPool.includes(t))) s += 35;
            if (item.type === 'product') s += 20;
            if (item.type === 'category') s += 10;
            return s;
        }

        search(rawQuery) {
            const query = normalizeSearch(rawQuery);
            if (!query) return [];
            const tokens = query.split(' ').filter(Boolean);

            const found = [];
            for (const item of this.index) {
                if (!item.normKeyPool.includes(query) && !tokens.some(t => item.normKeyPool.includes(t))) continue;
                const sc = this.score(item, query, tokens);
                if (sc > 0) found.push({ item, score: sc });
            }

            found.sort((a, b) => b.score - a.score || a.item.title.localeCompare(b.item.title));
            return found.slice(0, this.maxResults).map(x => x.item);
        }

        highlight(text, query) {
            const cleanText = escapeHtml(text || '');
            const q = normalizeSearch(query);
            if (!q) return cleanText;
            const tokens = q.split(' ').filter(t => t.length >= 2).slice(0, 3);
            if (!tokens.length) return cleanText;
            const pattern = new RegExp(`(${tokens.map(t => t.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')).join('|')})`, 'ig');
            return cleanText.replace(pattern, '<mark>$1</mark>');
        }

        render(results, query) {
            this.results = results;
            this.activeIndex = -1;

            if (!results.length) {
                this.panel.innerHTML = `
                    <div class="pvc-search-empty">
                        <div class="pvc-search-empty-title">No results found</div>
                        <div class="pvc-search-empty-sub">Try searching by product name, category, or brand.</div>
                        <div class="pvc-search-empty-suggest">
                            <a href="all-products.php?category=ACCESSORIES">Accessories</a>
                            <a href="all-products.php?category=HIKVISION">Hikvision</a>
                            <a href="all-products.php">All Products</a>
                        </div>
                    </div>
                `;
                this.panel.classList.add('active');
                return;
            }

            this.panel.innerHTML = results.map((r, i) => `
                <button type="button" class="pvc-search-item" data-index="${i}" data-url="${escapeHtml(r.url)}" role="option">
                    <span class="pvc-search-thumb-wrap">
                        <img class="pvc-search-thumb" src="${this.getThumbForItem(r)}" alt="${escapeHtml(r.title)}" loading="lazy" />
                    </span>
                    <span class="pvc-search-type">${escapeHtml(r.type)}</span>
                    <span class="pvc-search-main">${this.highlight(r.title, query)}</span>
                    <span class="pvc-search-sub">${this.highlight(r.subtitle || r.category || '', query)}</span>
                </button>
            `).join('');
            this.panel.classList.add('active');
        }

        renderPopular() {
            const quick = [
                { title: 'Hikvision', subtitle: 'Brand', url: 'all-products.php?category=HIKVISION' },
                { title: 'Accessories', subtitle: 'Category', url: 'all-products.php?category=ACCESSORIES' },
                { title: 'Cables', subtitle: 'Category', url: 'all-products.php?category=CABLES' },
                { title: 'All Products', subtitle: 'Browse', url: 'all-products.php' }
            ];
            this.panel.innerHTML = quick.map((r) => `
                <a class="pvc-search-item pvc-search-quick" href="${r.url}">
                    <span class="pvc-search-thumb-wrap">
                        <img class="pvc-search-thumb" src="assets/img/CATEGORY%20BANNERS/ACCESSORIES.svg" alt="${escapeHtml(r.title)}" loading="lazy" />
                    </span>
                    <span class="pvc-search-type">quick</span>
                    <span class="pvc-search-main">${escapeHtml(r.title)}</span>
                    <span class="pvc-search-sub">${escapeHtml(r.subtitle)}</span>
                </a>
            `).join('');
        }

        getThumbForItem(item) {
            if (!item) return 'assets/img/CATEGORY%20BANNERS/ACCESSORIES.svg';
            if (item.image) return item.image;
            const cat = (item.category || item.subtitle || item.title || '').toUpperCase();
            for (const [key, img] of Object.entries(this.categoryThumbMap)) {
                if (cat.includes(key) || (item.title || '').toUpperCase().includes(key)) return img;
            }
            return 'assets/img/CATEGORY%20BANNERS/ACCESSORIES.svg';
        }

        open() { this.panel.classList.add('active'); }
        close() { this.panel.classList.remove('active'); this.activeIndex = -1; }

        move(step) {
            const items = Array.from(this.panel.querySelectorAll('.pvc-search-item'));
            if (!items.length) return;
            this.activeIndex = (this.activeIndex + step + items.length) % items.length;
            items.forEach((el, i) => el.classList.toggle('active', i === this.activeIndex));
            items[this.activeIndex].scrollIntoView({ block: 'nearest' });
        }

        goToActiveOrDefault() {
            const items = Array.from(this.panel.querySelectorAll('.pvc-search-item'));
            if (this.activeIndex >= 0 && items[this.activeIndex]) {
                const active = items[this.activeIndex];
                const url = active.getAttribute('data-url') || active.getAttribute('href');
                if (url) window.location.href = url;
                return true;
            }
            return false;
        }

        bindEvents() {
            const onInput = debounce(() => {
                const q = this.input.value || '';
                if (!q.trim()) {
                    this.renderPopular();
                    this.open();
                    return;
                }
                this.render(this.search(q), q);
            }, 120);

            this.input.addEventListener('focus', () => {
                if (!(this.input.value || '').trim()) this.renderPopular();
                this.open();
            });
            this.input.addEventListener('input', onInput);
            this.input.addEventListener('keydown', (e) => {
                if (!this.panel.classList.contains('active')) return;
                if (e.key === 'ArrowDown') { e.preventDefault(); this.move(1); }
                else if (e.key === 'ArrowUp') { e.preventDefault(); this.move(-1); }
                else if (e.key === 'Escape') { this.close(); }
                else if (e.key === 'Enter') {
                    if (this.goToActiveOrDefault()) {
                        e.preventDefault();
                    }
                }
            });

            this.panel.addEventListener('click', (e) => {
                const item = e.target.closest('.pvc-search-item');
                if (!item) return;
                const url = item.getAttribute('data-url') || item.getAttribute('href');
                if (url) window.location.href = url;
            });

            this.form.addEventListener('submit', (e) => {
                e.preventDefault();
                if (this.goToActiveOrDefault()) return;
                handleGlobalSearch(this.input.value);
            });
        }
    }

    // --- SEARCH TOGGLE LOGIC ---
    const searchContainer = document.querySelector('.pvc-search-container');
    const searchBtnToggle = document.querySelector('.pvc-search-btn-toggle');
    const searchFormInline = document.querySelector('.pvc-search-form-inline');
    const searchInputInline = document.querySelector('.pvc-search-input-inline');
    const searchResultsPanel = document.getElementById('pvc-search-results');
    const pvcSmartSearch = new PvcSmartSearch({
        input: searchInputInline,
        form: searchFormInline,
        panel: searchResultsPanel
    });

    // Toggle Search Bar
    searchBtnToggle.addEventListener('click', (e) => {
        // e.stopPropagation(); // Prevent closing immediately if we add document click listener later
        searchContainer.classList.toggle('active');
        if (searchContainer.classList.contains('active')) {
            setTimeout(() => {
                searchInputInline.focus();
                pvcSmartSearch.open();
            }, 100);
        }
    });

    // Close when clicking outside
    document.addEventListener('click', (e) => {
        if (!searchContainer.contains(e.target) && searchContainer.classList.contains('active')) {
            searchContainer.classList.remove('active');
            pvcSmartSearch.close();
        }
    });

    // Handle Active State (Desktop & Mobile)
    const currentPath = window.location.pathname.split('/').pop() || 'index.php';
    const params = new URLSearchParams(window.location.search);
    const hasCategory = params.has('category') || params.has('cat');

    // Desktop Active Logic
    document.querySelectorAll('.pvc-nav-list .pvc-nav-link').forEach(link => {
        const href = link.getAttribute('href');

        if (hasCategory) {
            // Check if this query belongs to Brands or Categories
            const catValue = (params.get('category') || params.get('cat')).toUpperCase();
            const isBrand = PVC_HEADER_CONFIG.brands.some(b => b.url.toUpperCase().includes(catValue));
            const isCategory = PVC_HEADER_CONFIG.categories.some(c => c.url.toUpperCase().includes(catValue));

            const parentItem = link.closest('.pvc-nav-item');
            if (isBrand && parentItem.classList.contains('has-mega-brand')) {
                link.classList.add('active');
            } else if (isCategory && parentItem.classList.contains('has-mega-category')) {
                link.classList.add('active');
            }
        } else {
            // Standard exact match (no filters)
            if (href === currentPath) {
                link.classList.add('active');
            }
        }
    });

    // Mobile Active Logic
    document.querySelectorAll('.pvc-mobile-nav-link').forEach(link => {
        const linkPath = link.getAttribute('href');
        const parentItem = link.closest('.pvc-mobile-nav-item');

        if (hasCategory) {
            const catValue = (params.get('category') || params.get('cat')).toUpperCase();
            const isBrandMenu = parentItem.classList.contains('has-submenu') && link.textContent.includes('Brand');
            const isCatMenu = parentItem.classList.contains('has-submenu') && link.textContent.includes('Categories');

            const isBrandMatch = PVC_HEADER_CONFIG.brands.some(b => b.url.toUpperCase().includes(catValue));
            const isCatMatch = PVC_HEADER_CONFIG.categories.some(c => c.url.toUpperCase().includes(catValue));

            if (isBrandMatch && isBrandMenu) {
                link.classList.add('active');
                parentItem.classList.add('active');
            } else if (isCatMatch && isCatMenu) {
                link.classList.add('active');
                parentItem.classList.add('active');
            }
        } else {
            if (linkPath === currentPath) {
                link.classList.add('active');
                if (parentItem) parentItem.classList.add('active');
            }
        }
    });

    // Cart Count Sync (Mock or real from localStorage)
    function updateCartCount() {
        // Read the site's cart key `pvcCart` and sum quantities
        try {
            const cart = JSON.parse(localStorage.getItem('pvcCart')) || [];
            const count = cart.reduce((total, item) => total + (item.quantity || 0), 0);
            const el = document.getElementById('pvc-cart-count');
            if (el) {
                el.textContent = count;
                el.style.display = count > 0 ? 'flex' : 'none';
            }
        } catch (e) {
            // If parsing fails, hide the badge
            const el = document.getElementById('pvc-cart-count');
            if (el) el.style.display = 'none';
        }
    }
    updateCartCount();
    // Update on storage events (other tabs) and on a custom event
    window.addEventListener('storage', updateCartCount);
    window.addEventListener('pvc-cart-updated', updateCartCount);
}

// Ensure FontAwesome and Global CSS are loaded
if (!document.getElementById('fa-link')) {
    const fa = document.createElement('link');
    fa.id = 'fa-link';
    fa.rel = 'stylesheet';
    fa.href = 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css';
    document.head.appendChild(fa);
}

const headerStyle = document.createElement('link');
headerStyle.rel = 'stylesheet';
headerStyle.href = 'assets/css/global_header.css';
document.head.appendChild(headerStyle);

// Init on DOM Content Loaded or immediately if already loaded
if (document.readyState === "complete" || document.readyState === "interactive") {
    initPvcHeader();
} else {
    document.addEventListener('DOMContentLoaded', initPvcHeader);
}
