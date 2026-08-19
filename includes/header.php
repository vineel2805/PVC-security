

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
          <img src="assets/img/logo/logo_new1.png" alt="PVC Security Logo">
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
    <div class="pvc-mobile-menu-header">
      <div class="pvc-mobile-close" id="pvc-mobile-close">
        <i class="fa-solid fa-xmark"></i>
      </div>
    </div>
    <ul class="pvc-mobile-nav-list">
      <li class="pvc-mobile-nav-item"><a href="index.php"          class="pvc-mobile-nav-link"><span>Home</span></a></li>
      <li class="pvc-mobile-nav-item"><a href="about-us.php"       class="pvc-mobile-nav-link"><span>About Us</span></a></li>
      <li class="pvc-mobile-nav-item"><a href="all-products.php"   class="pvc-mobile-nav-link" id="mob-nav-brand"><span>Shop by Brand</span></a></li>
      <li class="pvc-mobile-nav-item"><a href="all-categories.php" class="pvc-mobile-nav-link" id="mob-nav-categories"><span>Shop by Categories</span></a></li>
      <li class="pvc-mobile-nav-item"><a href="services.php"       class="pvc-mobile-nav-link"><span>Services</span></a></li>
      <li class="pvc-mobile-nav-item"><a href="contact-us.php"     class="pvc-mobile-nav-link"><span>Contact Us</span></a></li>
    </ul>
  </div>
  <link id="fa-link" rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">