<?php include 'connect.php'; ?>
<?php
// ── Shop by Brand: all active brands ────────────────────────────────────────
$brandResult = mysqli_query($con, "
    SELECT brandid, brandname, imagelink
    FROM   brands
    WHERE  display_status = 1
    ORDER  BY brandname ASC
");

// ── Shop by Categories: MERGED — same cname across brands = one nav entry ───
// Normalizes multiple/extra internal spaces so "4G & WIFI" and "4G &  WIFI"
// (typo with double space) are treated as the same category.
$catRaw = mysqli_query($con, "
    SELECT
        TRIM(REPLACE(REPLACE(REPLACE(UPPER(cname), '  ', ' '), '  ', ' '), '  ', ' ')) AS key_name,
        MIN(cname)  AS cname,
        MIN(cimage) AS cimage
    FROM   category
    WHERE  display_status = 1
    GROUP  BY key_name
    ORDER  BY key_name ASC
");
$mergedCats = [];
if ($catRaw) {
    while ($row = mysqli_fetch_assoc($catRaw)) $mergedCats[] = $row;
}
?>
  <header class="pvc-global-header" id="pvc-global-header">
    <div class="pvc-header-container">
      <div class="pvc-header-logo">
        <a href="index.php">
          <img src="assets/img/logo/logo1.png" alt="PVC Security Logo" width="180" height="60" >
        </a>
      </div>

      <nav class="pvc-main-nav">
        <ul class="pvc-nav-list">
          <li class="pvc-nav-item"><a href="index.php"      class="pvc-nav-link" id="nav-home">Home</a></li>
          <li class="pvc-nav-item"><a href="about-us.php"   class="pvc-nav-link" id="nav-about">About Us</a></li>

          <!-- Shop by Brand -->
          <li class="pvc-nav-item has-mega-brand">
            <a href="all-products.php" class="pvc-nav-link" id="nav-brand">Shop by Brand</a>
            <div class="pvc-mega-menu">
              <div class="pvc-mega-column">
                <h4 class="pvc-mega-title">Our Partner Brands</h4>
                <ul class="pvc-mega-list">
                  <?php
                  if ($brandResult && mysqli_num_rows($brandResult) > 0) {
                      while ($b = mysqli_fetch_assoc($brandResult)) {
                          $bid   = htmlspecialchars($b['brandid'],   ENT_QUOTES, 'UTF-8');
                          $bname = htmlspecialchars($b['brandname'], ENT_QUOTES, 'UTF-8');
                          echo '<li><a href="all-products.php?brand=' . $bid . '">' . $bname . '</a></li>';
                      }
                  } else {
                      echo '<li><span class="pvc-mega-empty">No brands available</span></li>';
                  }
                  ?>
                </ul>
              </div>
            </div>
          </li>

          <!-- Shop by Categories -->
          <li class="pvc-nav-item has-mega-category">
            <a href="all-categories.php" class="pvc-nav-link" id="nav-categories">Shop by Categories</a>
            <div class="pvc-mega-menu">
              <div class="pvc-mega-column">
                <h4 class="pvc-mega-title">Product Categories</h4>
                <ul class="pvc-mega-list">
                  <?php
                  if (!empty($mergedCats)) {
                      foreach ($mergedCats as $cat) {
                          $cname = htmlspecialchars($cat['cname'], ENT_QUOTES, 'UTF-8');
                          echo '<li><a href="all-categories.php?catname=' . urlencode($cat['cname']) . '">' . $cname . '</a></li>';
                      }
                  } else {
                      echo '<li><span class="pvc-mega-empty">No categories available</span></li>';
                  }
                  ?>
                </ul>
              </div>
            </div>
          </li>

          <li class="pvc-nav-item"><a href="services.php"   class="pvc-nav-link" id="nav-services">Services</a></li>
          <li class="pvc-nav-item"><a href="contact-us.php" class="pvc-nav-link" id="nav-contact">Contact Us</a></li>
        </ul>
      </nav>

      <div class="pvc-header-utils">



        <!-- Phone -->
        <a href="tel:+919114456666" class="pvc-icon-btn pvc-phone-btn" aria-label="Call Now">
          <i class="fa-solid fa-phone"></i>
        </a>

        <!-- Cart -->
        <a href="cart.php" class="pvc-icon-btn pvc-cart-btn" aria-label="Shopping Cart">
          <i class="fa-solid fa-cart-shopping"></i>
          <span class="pvc-cart-badge" id="pvc-cart-count">0</span>
        </a>

        <!-- Hamburger (mobile only) -->
        <div class="pvc-mobile-toggle" id="pvc-mobile-toggle">
          <span></span><span></span><span></span>
        </div>
      </div>
    </div>
  </header>

  <div id="pvc-header-spacer"></div>

  <!-- Mobile Overlay -->
  <div class="pvc-overlay" id="pvc-overlay"></div>

  <!-- Mobile Menu -->
  <div class="pvc-mobile-menu" id="pvc-mobile-menu">
    <div class="pvc-mobile-close" id="pvc-mobile-close">
      <i class="fa-solid fa-xmark"></i>
    </div>
    <ul class="pvc-mobile-nav-list">
      <li class="pvc-mobile-nav-item"><a href="index.php"          class="pvc-mobile-nav-link">Home</a></li>
      <li class="pvc-mobile-nav-item"><a href="about-us.php"       class="pvc-mobile-nav-link">About Us</a></li>
      <li class="pvc-mobile-nav-item"><a href="all-products.php"   class="pvc-mobile-nav-link" id="mob-nav-brand">Shop by Brand</a></li>
      <li class="pvc-mobile-nav-item"><a href="all-categories.php" class="pvc-mobile-nav-link" id="mob-nav-categories">Shop by Categories</a></li>
      <li class="pvc-mobile-nav-item"><a href="services.php"       class="pvc-mobile-nav-link">Services</a></li>
      <li class="pvc-mobile-nav-item"><a href="contact-us.php"     class="pvc-mobile-nav-link">Contact Us</a></li>
    </ul>

    <div class="pvc-mobile-contact">
      <h4 class="pvc-mobile-contact-title">Quick Connect</h4>
      <div class="pvc-mobile-btns">
        <a href="tel:+919114456666"          class="pvc-mobile-btn btn-call">
          <i class="fa-solid fa-phone"></i> Call Now
        </a>
        <a href="https://wa.me/919114456666" class="pvc-mobile-btn btn-whatsapp">
          <i class="fa-brands fa-whatsapp"></i> WhatsApp
        </a>
      </div>
      <div class="pvc-mobile-numbers">
        <a href="tel:+919114456666" class="pvc-mobile-num">+91 91144 56666</a>
        <a href="tel:+919114467777" class="pvc-mobile-num">+91 91144 67777</a>
      </div>
    </div>
  </div>

  <link id="fa-link" rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">


  
  <?php
  $currentPage = basename($_SERVER['PHP_SELF']);
  $activeTab = '';
  if ($currentPage === 'index.php' || $currentPage === '') {
      $activeTab = 'home';
  } else if ($currentPage === 'all-products.php' || ($currentPage === 'all-categories.php' && isset($_GET['brand']))) {
      $activeTab = 'brands';
  } else if ($currentPage === 'all-categories.php') {
      $activeTab = 'categories';
  } else if ($currentPage === 'contact-us.php' || $currentPage === 'cart.php') {
      $activeTab = 'support';
  } else if ($currentPage === 'search.php') {
      $activeTab = 'search';
  }

  if (in_array($currentPage, ['index.php', 'all-products.php', 'all-categories.php'])) {
      echo '<style>
      @media (max-width: 991px) {
          .pvc-floating-whatsapp {
              bottom: calc(84px + env(safe-area-inset-bottom)) !important;
          }
          .pvc-floating-call {
              bottom: calc(148px + env(safe-area-inset-bottom)) !important;
              display: flex !important;
          }
      }
      </style>';
  }
  ?>

<!-- Modern Mobile Bottom Navigation -->
   <?php if (($currentPage ?? '') !== 'cart.php'): ?>
  <nav class="pvc-bottom-nav" aria-label="Mobile Navigation">
    <a href="index.php" class="pvc-bottom-nav-item<?php echo ($activeTab === 'home') ? ' active" aria-current="page' : ''; ?>" id="bottom-nav-home" aria-label="Home">
      <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-house"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
      <span>Home</span>
    </a>
    <a href="all-products.php" class="pvc-bottom-nav-item<?php echo ($activeTab === 'brands') ? ' active" aria-current="page' : ''; ?>" id="bottom-nav-brands" aria-label="Brands">
      <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-tag"><path d="M12 2H2v10l9.29 9.29c.94.94 2.48.94 3.42 0l6.58-6.58c.94-.94.94-2.48 0-3.42L12 2Z"/><path d="M7 7h.01"/></svg>
      <span>Brands</span>
    </a>
    <a href="search.php" class="pvc-bottom-nav-item search-fab<?php echo ($activeTab === 'search') ? ' active" aria-current="page' : ''; ?>" id="bottom-nav-search" aria-label="Search">
      <div class="pvc-header-search-fab-icon">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-search"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
      </div>
    </a>
    <a href="all-categories.php" class="pvc-bottom-nav-item<?php echo ($activeTab === 'categories') ? ' active" aria-current="page' : ''; ?>" id="bottom-nav-categories" aria-label="Categories">
      <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-layout-grid"><rect width="7" height="7" x="3" y="3" rx="1"/><rect width="7" height="7" x="14" y="3" rx="1"/><rect width="7" height="7" x="14" y="14" rx="1"/><rect width="7" height="7" x="3" y="14" rx="1"/></svg>
      <span>Categories</span>
    </a>
    <a href="contact-us.php" class="pvc-bottom-nav-item<?php echo ($activeTab === 'support') ? ' active" aria-current="page' : ''; ?>" id="bottom-nav-rfq" aria-label="Contact Us">
      <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-phone"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
      <span>Support</span>
    </a>
  </nav>
  <?php endif; ?>
<script src="assets/js/global_header.js" defer></script>
