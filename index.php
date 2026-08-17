<!--this is index file in PVC SECURITY website-->
<?php include 'head.php'; ?>
  <!--=====HEADER START =======-->
<link rel="stylesheet" href="assets/css/index.css?v=<?php echo filemtime(__DIR__ . '/assets/css/index.css'); ?>">
<?php include 'header.php'; ?>
<?php include 'includes/header.php'; ?>


  <!--=====HEADER END =======-->
  
<?php
// ── Fetch Dynamic Hero Slides ────────────────────────────────────────────────
include_once 'connect.php';
$heroSlides = [];
$checkTable = mysqli_query($con, "SHOW TABLES LIKE 'hero_slides'");
if ($checkTable && mysqli_num_rows($checkTable) > 0) {
    $heroRes = mysqli_query($con, "SELECT * FROM hero_slides WHERE status = 1 ORDER BY display_order ASC, id ASC");
    if ($heroRes && mysqli_num_rows($heroRes) > 0) {
        while ($row = mysqli_fetch_assoc($heroRes)) {
            $heroSlides[] = $row;
        }
    }
}
// Fallback slides if database table is empty or has no active rows
if (empty($heroSlides)) {
    $heroSlides = [
        [
            'title' => '',
            'desktop_image' => 'assets/img/carousel/1.svg',
            'mobile_image' => 'assets/img/carousel/1.png'
        ],
        [
            'title' => '',
            'desktop_image' => 'assets/img/carousel/2 copy.svg',
            'mobile_image' => 'assets/img/carousel/1.png'
        ],
        [
            'title' => '',
            'desktop_image' => 'assets/img/carousel/3 copy.svg',
            'mobile_image' => 'assets/img/carousel/1.png'
        ]
    ];
}
?>
<!--===== HERO AREA STARTS =======-->
<section id="home">
<!-- ================= DESKTOP CAROUSEL ================= -->
  <div class="carousel-area owl-carousel hero-slider-desktop d-none d-md-block">
    <?php foreach ($heroSlides as $slide):
        $dImg = htmlspecialchars($slide['desktop_image']);
    ?>
    <div class="hero3-section-area">
      <img src="<?php echo $dImg; ?>" alt="<?php echo htmlspecialchars($slide['title'] ?? 'PVC Security Banner'); ?>" class="hero-banner-img">
      <?php if (!empty($slide['title'])): ?>
      <div class="hero-overlay-container">
        <div class="container-fluid p-0">
          <div class="row m-0">
            <div class="col-lg-8" style="padding-left: 50px;">
              <h1 class="banner-title"><strong><?php echo htmlspecialchars($slide['title']); ?></strong></h1>
            </div>
          </div>
        </div>
      </div>
      <?php endif; ?>
    </div>
    <?php endforeach; ?>
  </div>
<!-- ================= MOBILE CAROUSEL ================= -->
  <div class="carousel-area owl-carousel hero-slider-mobile d-block d-md-none">
    <?php foreach ($heroSlides as $slide):
        $mImg = htmlspecialchars(!empty($slide['mobile_image']) ? $slide['mobile_image'] : $slide['desktop_image']);
    ?>
    <div class="hero3-section-area">
      <img src="<?php echo $mImg; ?>" alt="<?php echo htmlspecialchars($slide['title'] ?? 'PVC Security Banner'); ?>" class="hero-banner-img">
      <?php if (!empty($slide['title'])): ?>
      <div class="hero-overlay-container">
        <div class="container-fluid p-0">
          <div class="row m-0">
            <div class="col-12 text-center">
              <h1 class="banner-title"><strong><?php echo htmlspecialchars($slide['title']); ?></strong></h1>
            </div>
          </div>
        </div>
      </div>
      <?php endif; ?>
    </div>
    <?php endforeach; ?>
  </div>
 </section>
<!--===== HERO AREA ENDS =======-->
<style>
  /* Reduce size of all quick-link icons uniformly (unchanged - desktop) */
  .quick-links-row .quick-link-icon {
    width: 56px;
    height: 56px;
    font-size: 22px;
  }
  .quick-links-row .quick-link-text {
    font-size: 14px;
  }
  /* WhatsApp - same size/shape as other social icons, just green */
  .quick-link-item.whatsapp .quick-link-icon {
    background-color: #25D366 !important;
  }
  .quick-link-item.whatsapp .quick-link-icon i {
    color: #ffffff !important;
  }
  .quick-link-item.whatsapp:hover .quick-link-icon {
    background-color: #1DA851 !important;
  }
  /* Call Us - same size/shape as other social icons, gold.
     (Removed the old padding-top / margin-top nudges that were pushing
     this item out of line with Facebook/YouTube/LinkedIn/Instagram —
     it now shares their exact box model so it sits on the same row.) */
  .quick-link-item.call .quick-link-icon {
    background-color: var(--pvc-gold-mid) !important;
    position: relative;
    z-index: 1;
  }
  .quick-link-item.call .quick-link-icon i {
    font-size: 24px;
    color: #ffffff !important;
    position: relative;
    z-index: 2;
  }
  .quick-link-item.call:hover .quick-link-icon {
    background-color: var(--pvc-gold-dark, #b8860b) !important;
  }
  /* Pulsing wave rings around the Call Us icon - kept subtle and small
     so it never visually overlaps the "Call Us" label below it */
  .quick-link-item.call .quick-link-icon::before,
  .quick-link-item.call .quick-link-icon::after {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    border-radius: 50%;
    background-color: var(--pvc-gold-mid);
    opacity: 0.45;
    z-index: 0;
    animation: pvcCallWave 2s ease-out infinite;
    pointer-events: none;
  }
  .quick-link-item.call .quick-link-icon::after {
    animation-delay: 1s;
  }
  @keyframes pvcCallWave {
    0% {
      transform: scale(1);
      opacity: 0.45;
    }
    100% {
      transform: scale(1.7);
      opacity: 0;
    }
  }
  /* ============================================================
     MOBILE: force all icons onto ONE line on every mobile screen.
     Uses clamp() so icon/gap sizes shrink fluidly with viewport
     width instead of relying on several fixed breakpoints - this
     guarantees a single row from small phones up to tablets.
  ============================================================ */
  @media (max-width: 768px) {
    #quick-links .quick-links-row {
      display: flex !important;
      flex-wrap: nowrap !important;
      flex-direction: row !important;
      justify-content: space-evenly !important;
      align-items: center !important;
      gap: clamp(4px, 3vw, 14px) !important;
      overflow-x: visible !important;
      width: 100% !important;
      margin: 0 !important;
      padding: 0 !important;
    }
    #quick-links .quick-links-row .quick-link-item {
      display: flex !important;
      flex: 0 1 auto !important;
      flex-direction: column !important;
      align-items: center !important;
      justify-content: flex-start !important;
      min-width: 0 !important;
      max-width: none !important;
      width: auto !important;
      flex-shrink: 1 !important;
      flex-grow: 0 !important;
      flex-basis: auto !important;
      margin: 0 !important;
    }
    #quick-links .quick-links-row .quick-link-icon {
      width: clamp(36px, 12vw, 48px) !important;
      height: clamp(36px, 12vw, 48px) !important;
      font-size: clamp(15px, 4.5vw, 19px) !important;
      margin: 0 auto !important;
      display: flex !important;
      align-items: center !important;
      justify-content: center !important;
    }
    #quick-links .quick-link-item.call .quick-link-icon {
      width: clamp(28px, 9.5vw, 38px) !important;
      height: clamp(28px, 9.5vw, 38px) !important;
    }
    #quick-links .quick-link-item.call .quick-link-icon i {
      font-size: clamp(12px, 3.5vw, 15px) !important;
    }
    #quick-links .quick-links-row .quick-link-text {
      font-size: clamp(6px, 1.8vw, 9px) !important;
      white-space: nowrap !important;
      display: block !important;
      margin-top: 2px !important;
      overflow: hidden !important;
      text-overflow: ellipsis !important;
      max-width: 100% !important;
    }
    #quick-links .quick-link-item.call {
      padding-top: 4px !important;
    }
    #quick-links .quick-link-item.call .quick-link-text {
      margin-top: 9px !important;
    }
    #quick-links.quick-links-section .container {
      max-width: 100% !important;
      padding-left: 0 !important;
      padding-right: 0 !important;
    }
    /* "Shop By Brand", "Our Strategic Partners" and "What Our Customers
       Say" all share the same .premium-header-section markup, so this
       one rule set shrinks the icon/subtitle/title on all three sections
       together on mobile. */
    .premium-header-section .premium-icon-box {
      width: clamp(34px, 10vw, 46px) !important;
      height: clamp(34px, 10vw, 46px) !important;
      font-size: clamp(15px, 4.2vw, 20px) !important;
      margin-left: auto !important;
      margin-right: auto !important;
    }
    .premium-header-section .premium-icon-box i {
      font-size: inherit !important;
    }
    .premium-header-section .premium-subtitle {
      font-size: clamp(10px, 2.8vw, 12px) !important;
      letter-spacing: 1px !important;
    }
    .premium-header-section .premium-title {
      font-size: clamp(19px, 5.5vw, 26px) !important;
      line-height: 1.25 !important;
    }
    .premium-header-section .premium-divider {
      margin-top: 8px !important;
      margin-bottom: 8px !important;
    }
    /* Brand name text ("ANWIZ", "D-Link", etc.) — mobile: smaller,
       lighter weight, scales fluidly with viewport width. */
    #product-categories .brand-free-name {
      font-size: clamp(11px, 2.8vw, 13px) !important;
      font-weight: 500 !important;
    }
  }
  /* ============================================================
     DESKTOP / TABLET (769px and up): same three sections —
     "Shop By Brand", "Our Strategic Partners", "What Our Customers
     Say" — sized consistently via the shared .premium-header-section
     markup. Uses clamp() so it scales smoothly from small laptops up
     to large monitors instead of looking oversized/undersized at any
     one width.
  ============================================================ */
  @media (min-width: 769px) {
    .premium-header-section .premium-icon-box {
      width: clamp(56px, 5.5vw, 70px) !important;
      height: clamp(56px, 5.5vw, 70px) !important;
      font-size: clamp(22px, 2.2vw, 28px) !important;
      margin-left: auto !important;
      margin-right: auto !important;
    }
    .premium-header-section .premium-icon-box i {
      font-size: inherit !important;
    }
    .premium-header-section .premium-subtitle {
      font-size: clamp(13px, 1vw, 15px) !important;
      letter-spacing: 2px !important;
    }
    .premium-header-section .premium-title {
      font-size: clamp(28px, 3vw, 40px) !important;
      line-height: 1.2 !important;
    }
    .premium-header-section .premium-divider {
      margin-top: 16px !important;
      margin-bottom: 16px !important;
    }
    /* Brand name text ("ANWIZ", "D-Link", etc.) — desktop/tablet:
       smaller and lighter than before, still scales with viewport. */
    #product-categories .brand-free-name {
      font-size: clamp(13px, 1.1vw, 16px) !important;
      font-weight: 500 !important;
    }
  }
  /* ============================================================
     DESKTOP: "Shop By Brand" banner grid — smaller banner images
     plus real spacing between cards (both horizontal and vertical),
     instead of the images running edge-to-edge in each column.
  ============================================================ */
  @media (min-width: 992px) {
    #product-categories .brand-free-item {
      max-width: 360px;
      margin: 0 auto;
    }
    #product-categories .brand-free-img-wrapper {
      max-width: 100%;
    }
    #product-categories .brand-free-img {
      max-height: 220px;
      width: 100%;
      object-fit: contain;
    }
    #product-categories .row.g-2.g-md-3 > [class*="col-"] {
      padding-left: 20px !important;
      padding-right: 20px !important;
      margin-bottom: 42px !important;
    }
  }
</style>
  <section id="quick-links" class="quick-links-section">
    <div class="container">
      <div class="quick-links-row">
        <!--
        <a href="all-products.php" class="quick-link-item">
          <div class="quick-link-icon">
            <i class="fa-solid fa-grip"></i>
          </div>
          <span class="quick-link-text">New Products</span>
        </a>
        <a href="about-us.php" class="quick-link-item">
          <div class="quick-link-icon">
            <i class="fa-solid fa-fire"></i>
          </div>
          <span class="quick-link-text">Best Sellers</span>
        </a>
        <a href="all-products.php" class="quick-link-item">
          <div class="quick-link-icon">
            <i class="fa-solid fa-star"></i>
          </div>
          <span class="quick-link-text">New Arrivals</span>
        </a>
        <a href="all-products.php?category=ACCESSORIES" class="quick-link-item">
          <div class="quick-link-icon">
            <i class="fa-solid fa-puzzle-piece"></i>
          </div>
          <span class="quick-link-text">Accessories</span>
        </a>
        <a href="services.php" class="quick-link-item">
          <div class="quick-link-icon">
            <i class="fa-solid fa-headset"></i>
          </div>
          <span class="quick-link-text">Services</span>
        </a>
        <a href="contact-us.php" class="quick-link-item">
          <div class="quick-link-icon">
            <i class="fa-solid fa-envelope"></i>
          </div>
          <span class="quick-link-text">Contact Us</span>
        </a> -->
        <a href="https://www.facebook.com/share/19JHkwRWEw/" target="_blank" rel="noopener noreferrer" class="quick-link-item facebook">
          <div class="quick-link-icon">
            <i class="fa-brands fa-facebook"></i>
          </div>
          <span class="quick-link-text">Facebook</span>
        </a>
        <a href="https://youtube.com/@pvcsecuritysolutions-z9p?si=LGLiUB7R0vJXSOIG" target="_blank" rel="noopener noreferrer" class="quick-link-item youtube">
          <div class="quick-link-icon">
            <i class="fa-brands fa-youtube"></i>
          </div>
          <span class="quick-link-text">YouTube</span>
        </a>
        <a href="https://in.linkedin.com/in/pvc-security-solutions-7961733ba" target="_blank" rel="noopener noreferrer" class="quick-link-item linkedIn">
          <div class="quick-link-icon">
            <i class="fa-brands fa-linkedin-in"></i>
          </div>
          <span class="quick-link-text">LinkedIn</span>
        </a>
        <a href="https://www.instagram.com/pvc_security_solutions/" target="_blank" rel="noopener noreferrer" class="quick-link-item instagram">
          <div class="quick-link-icon">
            <i class="fa-brands fa-instagram"></i>
          </div>
          <span class="quick-link-text">Instagram</span>
        </a>
        <a href="tel:+91XXXXXXXXXX" class="quick-link-item call">
          <div class="quick-link-icon">
            <i class="fa-solid fa-phone"></i>
          </div>
          <span class="quick-link-text">Call Us</span>
        </a>
   <!-- Duplicated items for seamless infinite scroll on mobile
        <a href="all-products.php" class="quick-link-item quick-link-duplicate">
          <div class="quick-link-icon">
            <i class="fa-solid fa-grip"></i>
          </div>
          <span class="quick-link-text">New Products</span>
        </a>
        <a href="about-us.php" class="quick-link-item quick-link-duplicate">
          <div class="quick-link-icon">
            <i class="fa-solid fa-fire"></i>
          </div>
          <span class="quick-link-text">Best Sellers</span>
        </a>
        <a href="all-products.php" class="quick-link-item quick-link-duplicate">
          <div class="quick-link-icon">
            <i class="fa-solid fa-star"></i>
          </div>
          <span class="quick-link-text">New Arrivals</span>
        </a>
        <a href="all-products.php?category=ACCESSORIES" class="quick-link-item quick-link-duplicate">
          <div class="quick-link-icon">
            <i class="fa-solid fa-puzzle-piece"></i>
          </div>
          <span class="quick-link-text">Accessories</span>
        </a>
        <a href="services.php" class="quick-link-item quick-link-duplicate">
          <div class="quick-link-icon">
            <i class="fa-solid fa-headset"></i>
          </div>
          <span class="quick-link-text">Services</span>
        </a>
        <a href="contact-us.php" class="quick-link-item quick-link-duplicate">
          <div class="quick-link-icon">
            <i class="fa-solid fa-envelope"></i>
          </div>
          <span class="quick-link-text">Contact Us</span>
        </a>
        <a href="https://www.facebook.com/share/19JHkwRWEw/" target="_blank" rel="noopener noreferrer" class="quick-link-item quick-link-duplicate">
          <div class="quick-link-icon">
            <i class="fa-brands fa-facebook-f"></i>
          </div>
          <span class="quick-link-text">Facebook</span>
        </a>
        <a href="https://youtube.com/@pvcsecuritysolutions-z9p?si=LGLiUB7R0vJXSOIG" target="_blank" rel="noopener noreferrer" class="quick-link-item quick-link-duplicate">
          <div class="quick-link-icon">
            <i class="fa-brands fa-youtube"></i>
          </div>
          <span class="quick-link-text">YouTube</span>
        </a>
        <a href="https://in.linkedin.com/in/pvc-security-solutions-7961733ba" target="_blank" rel="noopener noreferrer" class="quick-link-item quick-link-duplicate">
          <div class="quick-link-icon">
            <i class="fa-brands fa-linkedin-in"></i>
          </div>
          <span class="quick-link-text">LinkedIn</span>
        </a>
        <a href="https://www.instagram.com/pvc_security_solutions/" target="_blank" rel="noopener noreferrer" class="quick-link-item quick-link-duplicate">
          <div class="quick-link-icon">
            <i class="fa-brands fa-instagram"></i>
          </div>
          <span class="quick-link-text">Instagram</span>
        </a>
        <a href="https://wa.me/91XXXXXXXXXX" target="_blank" rel="noopener noreferrer" class="quick-link-item quick-link-duplicate">
          <div class="quick-link-icon">
            <i class="fa-brands fa-whatsapp"></i>
          </div>
          <span class="quick-link-text">WhatsApp</span>
        </a>
        <a href="tel:+91XXXXXXXXXX" class="quick-link-item quick-link-duplicate">
          <div class="quick-link-icon">
            <i class="fa-solid fa-phone"></i>
          </div>
          <span class="quick-link-text">Call Us</span>
        </a> -->
      </div>
    </div>
  </section>
<!--===== QUICK LINKS SECTION ENDS =======-->
<!--===== SHOP BY CATEGORIES SECTION STARTS =======-->
<?php
include 'connect.php';
$query = "SELECT brandid, brandname, imagelink FROM brands WHERE display_status = 1";
$result = mysqli_query($con, $query);
?>
<!--===== SHOP BY CATEGORIES SECTION =======-->
<section id="product-categories" class="shop-categories-section">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="premium-header-section">
                    <div class="premium-icon-box">
                        <i class="fa-solid fa-layer-group"></i>
                    </div>
                    <span class="premium-subtitle">Explore Our Range</span>
                    <h2 class="premium-title">SHOP BY BRAND</h2>
                    <div class="premium-divider"></div>
                </div>
            </div>
        </div>
        <div class="row g-2 g-md-3 justify-content-center align-items-center" style="margin-top: 20px;">
            <?php
            // Ensure result is reset or data is fetched correctly
            if ($result && mysqli_num_rows($result) > 0) {
                mysqli_data_seek($result, 0); // Reset pointer if looping again
                $delay = 0;
                while ($row = mysqli_fetch_assoc($result)) {
                    $brandLink = "all-products.php?brand=" . urlencode($row['brandid']);
                    ?>
                    <div class="col-6 col-md-4 col-lg-4" data-aos="fade-up" data-aos-delay="<?php echo $delay; ?>">
                        <a href="<?php echo $brandLink; ?>" class="brand-free-item">
                            <div class="brand-free-img-wrapper">
                                <img class="brand-free-img"
                                     src="<?php echo htmlspecialchars(strpos($row['imagelink'], '../') === 0 ? substr($row['imagelink'], 3) : $row['imagelink']); ?>"
                                     alt="<?php echo htmlspecialchars($row['brandname']); ?>">
                            </div>
                            <h3 class="brand-free-name"><?php echo htmlspecialchars($row['brandname']); ?></h3>
                        </a>
                    </div>
                    <?php
                    $delay = ($delay + 50) % 200;
                }
            } else {
                echo "<p class='text-center'>No active brands found.</p>";
            }
            ?>
        </div>
    </div>
</section>
  <!--===== BRAND PARTNERS AREA STARTS =======-->
  <section id="brand-partners">
    <div class="sp1">
      <div class="container">
        <div class="row align-items-center">
          <div class="col-lg-12 text-center mb-4" data-aos="fade-down">
            <div class="premium-header-section">
              <div class="premium-icon-box">
                <i class="fa-solid fa-handshake"></i>
              </div>
              <span class="premium-subtitle">Trusted By Industry Leaders</span>
              <h2 class="premium-title">BRANDS AVAILABLE</h2>
              <div class="premium-divider"></div>
            </div>
          </div>
        </div>
        <div class="row align-items-center">
          <div class="col-lg-12">
            <div class="testimonial-slider owl-carousel" id="brandCarousel">
              <div class="img1"><img src="assets/img/brands/hikvision.png" alt="Hikvision"></div>
              <div class="img1"><img src="assets/img/brands/dahua.png" alt="Dahua"></div>
              <div class="img1"><img src="assets/img/brands/securus.png" alt="Securus"></div>
              <div class="img1"><img src="assets/img/brands/cpplusworld.png" alt="CP Plus"></div>
              <div class="img1"><img src="assets/img/brands/Secureye.png" alt="Secureye"></div>
              <div class="img1"><img src="assets/img/brands/tplink.png" alt="TP-Link"></div>
              <div class="img1"><img src="assets/img/brands/dlink.png" alt="D-Link"></div>
              <div class="img1"><img src="assets/img/brands/Prama.png" alt="Prama"></div>
              <div class="img1"><img src="assets/img/brands/yadon.png" alt="Yadon"></div>
              <div class="img1"><img src="assets/img/brands/Seagate.png" alt="Seagate"></div>
              <div class="img1"><img src="assets/img/brands/Westerndigital.png" alt="Western Digital"></div>
              <div class="img1"><img src="assets/img/brands/Toshiba.png" alt="Toshiba"></div>
              <div class="img1"><img src="assets/img/brands/erd.png" alt="ERD"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!--===== BRAND PARTNERS AREA ENDS =======-->
  <!--===== FEEDBACK AREA STARTS =======-->
  <section id="feedback" class="sp1" style="background: #ffffff; overflow: hidden; position: relative;"
    data-aos="fade-up">
    <!-- Abstract Background Pattern -->
    <div
      style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0.03; pointer-events: none; background-image: radial-gradient(var(--pvc-gold-mid) 1px, transparent 1px); background-size: 30px 30px;">
    </div>
    <div class="container" style="position: relative; z-index: 2;">
      <div class="row mb-5 pb-4">
        <div class="col-lg-12 text-center">
          <div class="premium-header-section">
            <div class="premium-icon-box">
              <i class="fa-solid fa-comments"></i>
            </div>
            <span class="premium-subtitle">Voice of Trust</span>
            <h2 class="premium-title">What Our Customers Say</h2>
            <div class="premium-divider"></div>
          </div>
        </div>
      </div>
      <!-- Feedback Carousel -->
      <div class="feedback-carousel owl-carousel">
        <!-- Card 1 -->
        <div class="deep-glass-card">
          <div class="big-quote-bg">"</div>
          <p class="feedback-text">"The transition to 4K surveillance with AcuSeek NVR has been seamless. The clarity is
            unmatched, and our security protocols have evolved overnight."</p>
          <div class="author-node">
            <div class="node-dot"></div>
            <div class="author-details-wrap">
              <span>Security Director</span>
              <p>Retail Mall Group</p>
            </div>
          </div>
        </div>
        <!-- Card 2 -->
        <div class="deep-glass-card">
          <div class="big-quote-bg">"</div>
          <p class="feedback-text">"Solar cable-free cameras are a lifesaver for our remote energy monitoring sites. No
            wiring, just pure high-end intelligence in the wild."</p>
          <div class="author-node">
            <div class="node-dot"></div>
            <div class="author-details-wrap">
              <span>Project Lead</span>
              <p>Smart Energy Site</p>
            </div>
          </div>
        </div>
        <!-- Card 3 -->
        <div class="deep-glass-card">
          <div class="big-quote-bg">"</div>
          <p class="feedback-text">"Perimeter protection solutions from PVC have virtually eliminated our trespassing
            issues. The AI detection is terrifyingly accurate."</p>
          <div class="author-node">
            <div class="node-dot"></div>
            <div class="author-details-wrap">
              <span>Safety Manager</span>
              <p>Industrial Tech Ltd.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
      <?php include 'global_footer.php'; ?>

  <!--===== FEEDBACK AREA ENDS =======-->
  <script src="assets/js/plugins/bootstrap.min.js"></script>
  <script src="assets/js/plugins/aos.js"></script>
  <script src="assets/js/plugins/fontawesome.js"></script>
  <script src="assets/js/plugins/magnific-popup.js"></script>
  <script src="assets/js/plugins/mobilemenu.js"></script>
  <script src="assets/js/plugins/owlcarousel.min.js"></script>
  <script src="assets/js/plugins/nice-select.js"></script>
  <script src="assets/js/plugins/slick-slider.js"></script>
  <script src="assets/js/plugins/circle-progress.js"></script>
  <script src="assets/js/plugins/gsap.min.js"></script>
  <script src="assets/js/plugins/counter.js"></script>
  <script src="assets/js/plugins/Splitetext.js"></script>
  <script src="assets/js/plugins/ScrollTrigger.min.js"></script>
  <script src="assets/js/main.js"></script>
  <!--===== GLOBAL FOOTER COMPONENT =======-->
   <script src="assets/js/index.js"></script>
  <script src="assets/js/global_footer.js"></script>
  <!--===== END GLOBAL FOOTER =======-->
  <!-- Quick Links Auto-Scroll + Manual Swipe (Mobile Only) -->
  <script>
    (function () {
      const container = document.querySelector('#quick-links .container');
      const row = document.querySelector('.quick-links-row');
      if (!container || !row) return;
      let scrollInterval = null;
      let resumeTimeout = null;
      function checkLoop() {
        const limit = row.scrollWidth / 2;
        if (container.scrollLeft >= limit) {
          container.scrollLeft = 0;
        }
      }
      function startScrolling() {
        if (scrollInterval) return;
        scrollInterval = setInterval(() => {
          container.scrollLeft += 1;
          checkLoop();
        }, 25);
      }
      function stopScrolling() {
        if (scrollInterval) {
          clearInterval(scrollInterval);
          scrollInterval = null;
        }
      }
      function onTouchStart() {
        stopScrolling();
        if (resumeTimeout) {
          clearTimeout(resumeTimeout);
          resumeTimeout = null;
        }
      }
      function onTouchEnd() {
        if (resumeTimeout) clearTimeout(resumeTimeout);
        resumeTimeout = setTimeout(() => {
          startScrolling();
        }, 2000);
      }
      function initMobileAutoScroll() {
        if (scrollInterval) return;
        // Force duplicates to display on mobile for seamless loop
        row.querySelectorAll('.quick-link-duplicate').forEach(el => {
          el.style.setProperty('display', 'flex', 'important');
        });
        container.scrollLeft = 0;
        container.addEventListener('scroll', checkLoop, { passive: true });
        container.addEventListener('touchstart', onTouchStart, { passive: true });
        container.addEventListener('touchend', onTouchEnd, { passive: true });
        container.addEventListener('touchcancel', onTouchEnd, { passive: true });
        startScrolling();
      }
      function cleanupMobileAutoScroll() {
        stopScrolling();
        if (resumeTimeout) {
          clearTimeout(resumeTimeout);
          resumeTimeout = null;
        }
        // Restore duplicate items display back to desktop CSS default (none)
        row.querySelectorAll('.quick-link-duplicate').forEach(el => {
          el.style.removeProperty('display');
        });
        container.removeEventListener('scroll', checkLoop);
        container.removeEventListener('touchstart', onTouchStart);
        container.removeEventListener('touchend', onTouchEnd);
        container.removeEventListener('touchcancel', onTouchEnd);
        container.scrollLeft = 0;
      }
      const mobileQuery = window.matchMedia("(max-width: 768px)");
      function handleResize() {
        if (mobileQuery.matches) {
          initMobileAutoScroll();
        } else {
          cleanupMobileAutoScroll();
        }
      }
      if (mobileQuery.addEventListener) {
        mobileQuery.addEventListener("change", handleResize);
      } else if (mobileQuery.addListener) {
        mobileQuery.addListener(handleResize);
      }
      handleResize();
    })();
  </script>
  <!-- Form & Cart Systems -->
</body>
</html>