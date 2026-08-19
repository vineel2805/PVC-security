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
$query = "SELECT brandid, brandname, imagelink FROM brands WHERE display_status = 1 ORDER BY display_order ASC";
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