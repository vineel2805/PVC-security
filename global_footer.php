<?php
/**
 * PVC SECURITY - GLOBAL FOOTER COMPONENT (PHP)
 * Server-rendered equivalent of global_footer.js.
 * Include this file wherever the old <script src="assets/js/global_footer.js">
 * tag was used, e.g.:
 *     <?php require __DIR__ . '/global_footer.php'; ?>
 * placed just before </body>.
 */

$pvcFooterData = [
    'logoPath'        => 'assets/img/logo/footer_logo.png',
    'brandNameTop'    => '',                          // <-- was referenced below but never defined; set your top line here (or leave blank)
    'brandNameBottom' => 'PVC SECURITY SOLUTIONS',
    'description'     => 'PVC Security solutions provides professional CCTV and surveillance solutions for homes, shops, offices, and industries.',
    'phone1'          => '+91 91144 56666',
    'phone1Clean'     => '+919114456666',
    'phone2'          => '+91 91144 67777',
    'phone2Clean'     => '+919114467777',
    'phone3'          => '+91 91144 78888',
    'phone3Clean'     => '+919114478888',
    'email1'          => 'Service@pvcsecuritysolutions.com',
    'email2'          => 'Support@pvcsecuritysolutions.com',
    'addressLines'    => [
        'Near KLM Shopping Mall',
        'mavullamma Temple Road',
        'Bhimavaram 1-Town',
        'Andhra Pradesh',
        'Pincode - 534201'
    ],
    'mapEmbedUrl'     => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3769.856783893453!2d81.5259349!3d16.5447153!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3a37efe240620edd%3A0x2e27b608fff2d823!2sPVC%20SECURITY%20SOLUTIONS%20CCTV%20CAMERA%20SHOP!5e0!3m2!1sen!2sin!4v1234567890',
    'serviceArea'     => 'Andhra Pradesh & Telangana',
    'workingHours'    => 'Mon - Sat: 9:00 AM - 9:00 PM',
    // WhatsApp floating button settings
    'whatsappNumber'  => '919114456666',              // country code + number, no "+" and no spaces
    'whatsappMessage' => 'Hi PVC Security Solutions, I would like to know more about your products.',
];

$pvcNavLinks = [
    ['href' => 'index.php', 'label' => 'Home'],
    ['href' => 'about-us.php', 'label' => 'About Us'],
    ['href' => 'all-products.php', 'label' => 'Shop by Brand'],
    ['href' => 'all-categories.php?category=ACCESSORIES', 'label' => 'Shop by Categories'],
    ['href' => 'services.php', 'label' => 'Services'],
    ['href' => 'contact-us.php', 'label' => 'Contact Us'],
];

/* --- ACTIVE PAGE HIGHLIGHTING ---
   Same rules as the old client-side JS, computed server-side instead. */
$pvcCurrentPage = basename(parse_url($_SERVER['REQUEST_URI'] ?? 'index.php', PHP_URL_PATH));
if ($pvcCurrentPage === '') {
    $pvcCurrentPage = 'index.php';
}
$pvcQueryString  = $_SERVER['QUERY_STRING'] ?? '';
$pvcHasCatFilter = isset($_GET['category']) || isset($_GET['cat']);
$pvcCatVal       = strtoupper($_GET['category'] ?? $_GET['cat'] ?? '');
$pvcComponents   = ['ACCESSORIES', 'CABLES', 'HDD', 'MONITOR', 'RACK', 'SD CARDS'];

if (!function_exists('pvc_link_is_active')) {
    function pvc_link_is_active(string $href, string $currentPage, bool $hasCatFilter, string $catVal, array $components, string $currentQuery): bool
    {
        // "Shop by Categories" should read active for any component category, not just ACCESSORIES.
        if ($hasCatFilter && strpos($href, 'category=ACCESSORIES') !== false && $catVal !== 'ALL CATEGORIES') {
            if (in_array($catVal, $components, true)) {
                return true;
            }
        }

        $hrefPath  = parse_url($href, PHP_URL_PATH);
        $hrefQuery = parse_url($href, PHP_URL_QUERY);

        if ($hrefPath === $currentPage) {
            if (!$hasCatFilter) {
                return true;
            }
            if ($currentQuery !== '' && $hrefQuery && strpos($currentQuery, $hrefQuery) !== false) {
                return true;
            }
        }
        return false;
    }
}

// Load CSS/FontAwesome once per response even if this file is (accidentally) included twice.
if (!defined('PVC_FOOTER_ASSETS_LOADED')) {
    define('PVC_FOOTER_ASSETS_LOADED', true);
    ?>
    <link rel="stylesheet" href="assets/css/global_footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <?php
}
?>
<footer class="pvc-global-footer">
    <div class="pvc-footer-container">
        <div class="pvc-footer-row">

            <!-- 1. BRAND SECTION -->
            <div class="pvc-footer-col">
                <div class="pvc-footer-logo">
                    <img src="<?= htmlspecialchars($pvcFooterData['logoPath']) ?>" alt="PVC Security Solutions Logo" width="170" height="170" style="object-fit: contain;">
                    <h3 class="pvc-footer-brand-name">
                        <span class="pvc-brand-top"><?= htmlspecialchars($pvcFooterData['brandNameTop']) ?></span>
                        <span class="pvc-brand-bottom"><?= htmlspecialchars($pvcFooterData['brandNameBottom']) ?></span>
                    </h3>
                </div>
                <p class="pvc-footer-desc"><?= htmlspecialchars($pvcFooterData['description']) ?></p>
                <div class="pvc-trust-icons">
                    <div class="pvc-trust-item"><i class="fa-solid fa-certificate"></i> Genuine Products</div>
                    <div class="pvc-trust-item"><i class="fa-solid fa-headset"></i> Expert Support</div>
                </div>
            </div>

            <!-- 2. QUICK LINKS -->
            <div class="pvc-footer-col">
                <h4 class="pvc-footer-title">Quick Links</h4>
                <ul class="pvc-footer-links">
                    <?php foreach ($pvcNavLinks as $pvcLink):
                        $pvcActive = pvc_link_is_active($pvcLink['href'], $pvcCurrentPage, $pvcHasCatFilter, $pvcCatVal, $pvcComponents, $pvcQueryString);
                    ?>
                    <li><a href="<?= htmlspecialchars($pvcLink['href']) ?>"<?= $pvcActive ? ' class="active"' : '' ?>><?= htmlspecialchars($pvcLink['label']) ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <!-- 3. CONTACT INFORMATION -->
            <div class="pvc-footer-col">
                <h4 class="pvc-footer-title">Contact Support</h4>
                <ul class="pvc-contact-list">
                    <li class="pvc-contact-item">
                        <a href="tel:<?= htmlspecialchars($pvcFooterData['phone1Clean']) ?>" class="pvc-contact-icon" aria-label="Call Primary"><i class="fa-solid fa-phone"></i></a>
                        <div class="pvc-contact-text">
                            <span>Mobile Number 1</span>
                            <a href="tel:<?= htmlspecialchars($pvcFooterData['phone1Clean']) ?>"><?= htmlspecialchars($pvcFooterData['phone1']) ?></a>
                        </div>
                    </li>
                    <li class="pvc-contact-item">
                        <a href="tel:<?= htmlspecialchars($pvcFooterData['phone2Clean']) ?>" class="pvc-contact-icon" aria-label="Call Secondary"><i class="fa-solid fa-phone"></i></a>
                        <div class="pvc-contact-text">
                            <span>Mobile Number 2</span>
                            <a href="tel:<?= htmlspecialchars($pvcFooterData['phone2Clean']) ?>"><?= htmlspecialchars($pvcFooterData['phone2']) ?></a>
                        </div>
                    </li>
                     <li class="pvc-contact-item">
                        <a href="tel:<?= htmlspecialchars($pvcFooterData['phone3Clean']) ?>" class="pvc-contact-icon" aria-label="Call Tertiary"><i class="fa-solid fa-phone"></i></a>
                        <div class="pvc-contact-text">
                            <span>Mobile Number 3</span>
                            <a href="tel:<?= htmlspecialchars($pvcFooterData['phone3Clean']) ?>"><?= htmlspecialchars($pvcFooterData['phone3']) ?></a>
                        </div>
                    </li>
                    <li class="pvc-contact-item">
                        <a href="mailto:<?= htmlspecialchars($pvcFooterData['email1']) ?>" class="pvc-contact-icon" aria-label="Email Service"><i class="fa-solid fa-envelope"></i></a>
                        <div class="pvc-contact-text">
                            <span>Service Support</span>
                            <a href="mailto:<?= htmlspecialchars($pvcFooterData['email1']) ?>"><?= htmlspecialchars($pvcFooterData['email1']) ?></a>
                        </div>
                    </li>
                    <li class="pvc-contact-item">
                        <a href="mailto:<?= htmlspecialchars($pvcFooterData['email2']) ?>" class="pvc-contact-icon" aria-label="Email Support"><i class="fa-solid fa-envelope"></i></a>
                        <div class="pvc-contact-text">
                            <span>General Support</span>
                            <a href="mailto:<?= htmlspecialchars($pvcFooterData['email2']) ?>"><?= htmlspecialchars($pvcFooterData['email2']) ?></a>
                        </div>
                    </li>
                </ul>
            </div>

            <!-- 4. ADDRESS & HOURS & MAP -->
            <div class="pvc-footer-col">
                <h4 class="pvc-footer-title">Our Address</h4>
                <ul class="pvc-contact-list">
                    <li class="pvc-contact-item">
                        <a href="https://www.google.com/maps/place/PVC+SECURITY+SOLUTIONS+CCTV+CAMERA+SHOP/@16.5447153,81.5259349,16z/data=!3m1!4b1!4m6!3m5!1s0x3a37efe240620edd:0x2e27b608fff2d823!8m2!3d16.5447153!4d81.5259349!16s%2Fg%2F11vf4klr8g?entry=ttu"
                           class="pvc-contact-icon"
                           target="_blank"
                           rel="noopener noreferrer"
                           aria-label="Our Location">
                            <i class="fa-solid fa-location-dot"></i>
                        </a>
                        <div class="pvc-contact-text pvc-address-lines">
                            <span>Visit Us</span>
                            <?php foreach ($pvcFooterData['addressLines'] as $line): ?>
                                <div class="pvc-address-line"><?= htmlspecialchars($line) ?></div>
                            <?php endforeach; ?>
                        </div>
                    </li>
                </ul>
                <div class="pvc-working-hours-badge">
                    <i class="fa-regular fa-clock"></i>
                    <span><?= htmlspecialchars($pvcFooterData['workingHours']) ?></span>
                </div>
                <div class="pvc-map-container">
                    <iframe
                        src="<?= htmlspecialchars($pvcFooterData['mapEmbedUrl']) ?>"
                        width="100%"
                        height="180"
                        style="border:0;"
                        allowfullscreen=""
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                        class="pvc-location-map">
                    </iframe>
                </div>
            </div>

        </div>
    </div>

    <div class="pvc-footer-bottom-bar" style="background-color: #000000; text-align: center; padding: 20px 0; width: 100%; position: relative; z-index: 10;">
        <p style="background: var(--pvc-gradient); -webkit-background-clip: text; background-clip: text; -webkit-text-fill-color: transparent; font-weight: 900; font-size: 15px; letter-spacing: 1.5px; margin-bottom: 5px; text-transform: uppercase; font-family: 'Outfit', sans-serif;">WEBSITE DESIGNED BY</p>
        <p style="color: #ffffff; font-size: 14px; margin: 0; font-family: 'Inter', sans-serif;">
            &copy; <?= date('Y') ?> | All Rights Reserved | <a href="https://bhimavaramdigitals.com/" target="_blank" style="color: #2196F3; text-decoration: none; font-weight: 600; transition: color 0.3s;" onmouseover="this.style.color='#64B5F6'" onmouseout="this.style.color='#2196F3'">Bhimavaram Digitals.</a>
        </p>
    </div>
</footer>

<!--===== FLOATING WHATSAPP BUTTON (site-wide, sits above the bottom bar, right side) =======-->
<a href="https://wa.me/<?= htmlspecialchars($pvcFooterData['whatsappNumber']) ?>?text=<?= rawurlencode($pvcFooterData['whatsappMessage']) ?>"
   target="_blank"
   rel="noopener noreferrer"
   class="pvc-whatsapp-float"
   aria-label="Chat with us on WhatsApp">
    <span class="pvc-wa-wave"></span>
    <span class="pvc-wa-wave"></span>
    <i class="fa-brands fa-whatsapp"></i>
</a>

<style>
/* ============================================================
   FLOATING WHATSAPP BUTTON WITH PULSING WAVES
   Fixed to the bottom-right of the viewport, raised so it never
   sits on top of the black footer bottom bar.
   ============================================================ */
.pvc-whatsapp-float{
    position: fixed;
    right: 22px;
    bottom: 100px;                 /* clears the black bottom bar */
    z-index: 9998;
    width: 58px;
    height: 58px;
    border-radius: 50%;
    background-color: #25D366;
    color: #ffffff !important;
    display: none;                 /* hidden on desktop, re-enabled on mobile below */
    align-items: center;
    justify-content: center;
    box-shadow: 0 6px 18px rgba(0, 0, 0, 0.30);
    text-decoration: none;
    transition: background-color .25s ease, transform .25s ease;
}

.pvc-whatsapp-float i{
    font-size: 30px;
    line-height: 1;
    color: #ffffff !important;
    position: relative;
    z-index: 2;
}

.pvc-whatsapp-float:hover,
.pvc-whatsapp-float:focus{
    background-color: #1DA851;
    transform: scale(1.08);
    color: #ffffff !important;
}

/* Pulsing wave rings */
.pvc-wa-wave{
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    border-radius: 50%;
    background-color: #25D366;
    opacity: 0.45;
    z-index: 0;
    pointer-events: none;
    animation: pvcWaWave 2s ease-out infinite;
}

.pvc-wa-wave:nth-child(2){
    animation-delay: 1s;
}

@keyframes pvcWaWave{
    0%   { transform: scale(1);   opacity: 0.45; }
    100% { transform: scale(1.35); opacity: 0; }
}

/* Mobile: slightly smaller, still clear of the bottom bar / sticky CTA */
@media (max-width: 768px){
    .pvc-whatsapp-float{
        display: flex;
        right: 30px;
        bottom: 120px;              /* raise to ~140px if the mobile sticky CTA overlaps */
        width: 50px;
        height: 50px;
    }
    .pvc-whatsapp-float i{
        font-size: 26px;
    }
}

/* Respect reduced-motion preference */
@media (prefers-reduced-motion: reduce){
    .pvc-wa-wave{
        animation: none;
        opacity: 0;
    }
}
</style>

<script>
// Global Close Handler for Mobile CTA (client-side interaction, kept as JS)
document.addEventListener('click', function (e) {
    if (e.target.closest('#pvcStickyClose')) {
        var cta = document.getElementById('pvcMobileStickyCta');
        if (cta) {
            cta.style.display = 'none';
        }
    }
});
</script>