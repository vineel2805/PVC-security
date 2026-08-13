<style>
  /* Brand text next to the logo — shown on ALL screen sizes now */
  .pvc-header-logo {
    display: flex !important;
    align-items: center !important;
    gap: 8px !important;
  }
  .pvc-header-logo a {
    display: inline-flex !important;
    margin: 0 !important;
    padding: 0 !important;
  }
  .pvc-header-brand-text {
    display: flex !important;
    flex-direction: column;
    justify-content: center;
    line-height: 1.15;
    white-space: nowrap;
    margin: 0 !important;
    padding-left: 6px !important;
    border-left: 1px solid rgba(184, 134, 11, 0.3);
  }
  .pvc-header-brand-text .pvc-brand-line1 {
    font-size: 18px;
    font-weight: 800;
    letter-spacing: 0.5px;
    background: linear-gradient(180deg, #e2b567 0%, #b8860b 100%);
    -webkit-background-clip: text;
    background-clip: text;
    -webkit-text-fill-color: transparent;
    color: #b8860b; /* fallback if gradient text isn't supported */
  }
  .pvc-header-brand-text .pvc-brand-line2 {
    font-size: 10px;
    font-weight: 600;
    letter-spacing: 1px;
    text-transform: uppercase;
    color: #FAF9F6;
    margin-top: 2px;
  }

  /* Desktop — bigger brand text so it reads clearly next to the nav */
  @media (min-width: 992px) {
    .pvc-header-brand-text {
      padding-left: 14px;
    }
    .pvc-header-brand-text .pvc-brand-line1 {
      font-size: 20px;
    }
    .pvc-header-brand-text .pvc-brand-line2 {
      font-size: 13px;
      margin-top: 3px;
    }
  }

  /* Search & Cart — desktop only (hidden on mobile; bottom nav covers them there) */
  @media (max-width: 991px) {
    .pvc-search-btn,
    .pvc-cart-btn {
      display: none !important;
    }
  }


  #pvc-global-header .pvc-support-btn {
    display: none !important;
  }
  @media (max-width: 991px) {
    .pvc-header-utils {
      margin-left: auto;
    }
    #pvc-global-header .pvc-header-utils a.pvc-support-btn {
      display: inline-flex !important;
      align-items: center !important;
      justify-content: center !important;
      box-sizing: border-box !important;
      width: 40px !important;
      height: 40px !important;
      min-width: 40px !important;
      min-height: 40px !important;
      max-width: 40px !important;
      max-height: 40px !important;
      flex: 0 0 40px !important;
      aspect-ratio: 1 / 1 !important;
      border-radius: 50% !important;
      background: transparent !important;
      border: 1.5px solid #b8860b !important;
      color: #b8860b !important;
      font-size: 20px !important;
      line-height: 1 !important;
      padding: 0 !important;
      margin: auto 0 !important;
      box-shadow: none !important;
      overflow: hidden;
    }
  }

  @media (max-width: 991px) {
    .pvc-header-logo img {
      height: 35px !important;
      width: auto !important;
      max-width: none !important;
      margin: 0 !important;
      padding: 0 !important;
    }
  }

  @media (max-width: 380px) {
    .pvc-header-brand-text .pvc-brand-line1 {
      font-size: 13px;
    }
    .pvc-header-brand-text .pvc-brand-line2 {
      font-size: 9px;
    }
  }
</style>
<header class="pvc-global-header" id="pvc-global-header">
    <div class="pvc-header-container">
      <!-- Hamburger (mobile only) -->
        <button
            type="button"
            class="pvc-mobile-toggle"
            id="pvc-mobile-toggle"
            aria-label="Open menu"
            aria-expanded="false"
            aria-controls="pvc-mobile-menu">
            <span></span>
            <span></span>
            <span></span>
        </button>
      <div class="pvc-header-logo">
        <a href="index.php">
          <img src="assets/img/logo/fav.png" alt="PVC Security Logo" width="60" height="59" >
        </a>
        <span class="pvc-header-brand-text">
          <span class="pvc-brand-line1">PVC</span>
          <span class="pvc-brand-line2">Security Solutions</span>
        </span>
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
        <!-- Search -->
        <button
            type="button"
            class="pvc-icon-btn pvc-search-btn"
            id="desktop-search-btn"
            aria-label="Search">
    <i class="fa-solid fa-magnifying-glass"></i>
  </button>
        <!-- Phone -->
        <a href="tel:+919114456666" class="pvc-icon-btn pvc-phone-btn" aria-label="Call Now">
          <i class="fa-solid fa-phone"></i>
        </a>
        <!-- Cart -->
        <a href="cart.php" class="pvc-icon-btn pvc-cart-btn" aria-label="Shopping Cart">
          <i class="fa-solid fa-cart-shopping"></i>
          <span class="pvc-cart-badge" id="pvc-cart-count">0</span>
        </a>
        <!-- Support (mobile only) -->
        <a href="contact-us.php" class="pvc-icon-btn pvc-support-btn" aria-label="Support">
          <i class="fa-solid fa-headset"></i>
        </a>
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