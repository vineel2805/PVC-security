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
          <img src="assets/img/logo/logo1.png" alt="PVC Security Logo" width="180" height="60" style="object-fit:contain;">
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
          <span class="pvc-cart-badge" id="pvc-cart-count" style="display:none;">0</span>
        </a>

        <!-- Hamburger (mobile only) -->
        <div class="pvc-mobile-toggle" id="pvc-mobile-toggle">
          <span></span><span></span><span></span>
        </div>
      </div>
    </div>
  </header>

  <div id="pvc-header-spacer" style="height:0px;"></div>

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
  <link rel="stylesheet" href="assets/css/global_header.css">

  <style id="pvc-header-fixes">

  /* ==========================================================
     ICON BUTTONS — Search / Phone / Cart
     Matches screenshot: solid black circle, thick gold ring,
     large centred gold icon, subtle glow on hover
  ========================================================== */

  .pvc-header-utils {
    display: flex !important;
    align-items: center !important;
    gap: 14px !important;
  }

  /* The single shared class for all three circle icon buttons */
  .pvc-icon-btn {
    /* size */
    flex: 0 0 auto !important;
    width:  50px !important;
    height: 50px !important;
    min-width: 50px !important;
    padding: 0 !important;

    /* circle shape */
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    border-radius: 50% !important;

    /* colours — solid black fill, thick gold ring */
    background-color: #111111 !important;
    border: 2.5px solid #c9a14a !important;
    color: #c9a14a !important;

    /* icon size */
    font-size: 18px !important;
    line-height: 1 !important;

    /* misc */
    cursor: pointer !important;
    text-decoration: none !important;
    position: relative !important;
    box-shadow: none !important;
    outline: none !important;
    transition: background-color 0.22s ease,
                border-color     0.22s ease,
                color            0.22s ease,
                box-shadow       0.22s ease !important;
    -webkit-appearance: none !important;
    appearance: none !important;
  }

  .pvc-icon-btn i {
    color: inherit !important;
    font-size: inherit !important;
    pointer-events: none !important;
  }

  /* Hover: gold fill, dark icon, soft glow */
  .pvc-icon-btn:hover {
    background-color: #c9a14a !important;
    border-color:     #c9a14a !important;
    color:            #111111 !important;
    box-shadow: 0 0 14px rgba(201, 161, 74, 0.45) !important;
  }

  /* Cart badge */
  .pvc-cart-badge {
    position: absolute !important;
    top:   -4px !important;
    right: -4px !important;
    background:  #c9a14a !important;
    color:       #111111 !important;
    font-size:   10px !important;
    font-weight: 700 !important;
    min-width:   18px !important;
    height:      18px !important;
    border-radius: 50% !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    padding: 0 3px !important;
    line-height: 1 !important;
    pointer-events: none !important;
  }

  /* Slightly smaller on very small phones */
  @media (max-width: 420px) {
    .pvc-icon-btn {
      width:     42px !important;
      height:    42px !important;
      min-width: 42px !important;
      font-size: 15px !important;
    }
    .pvc-header-logo img { width: 96px !important; }
  }
/* Adjust size for mobile devices */
@media (max-width: 767px) {
  .pvc-icon-btn {
    width: 40px !important;    /* Reduced from 50px */
    height: 40px !important;   /* Reduced from 50px */
    min-width: 40px !important;
    font-size: 15px !important; /* Reduced icon size */
  }

  /* Adjust cart badge position for smaller buttons */
  .pvc-cart-badge {
    top: -2px !important;
    right: -2px !important;
    min-width: 16px !important;
    height: 16px !important;
    font-size: 9px !important;
  }
}

/* Further reduction for very small screens */
@media (max-width: 420px) {
  .pvc-icon-btn {
    width: 36px !important;    /* Smaller still */
    height: 36px !important;
    min-width: 36px !important;
    font-size: 14px !important;
  }
  
  /* Reduce gap in header so icons fit better */
  .pvc-header-utils {
    gap: 10px !important;
  }
}
  /* Hide phone icon in desktop header on mobile — it lives in the mobile menu */
  @media (max-width: 991px) {
    .pvc-phone-btn { display: none !important; }
  }

  /* ==========================================================
     SEARCH — collapsible dropdown form
  ========================================================== */
  .pvc-search-container {
    position: relative !important;
    display: inline-flex !important;
    align-items: center !important;
  }

  /* Remove browser default button chrome */
  .pvc-search-btn-toggle,
  .pvc-search-btn-toggle:focus,
  .pvc-search-btn-toggle:active {
    box-shadow: none !important;
    outline:    none !important;
  }

  .pvc-search-form {
    position:   absolute !important;
    top:        calc(100% + 12px) !important;
    right:      0 !important;
    width:      320px !important;
    max-width:  90vw !important;
    background: #fff !important;
    border-radius: 10px !important;
    box-shadow: 0 12px 30px rgba(0,0,0,.18) !important;
    padding:    10px !important;
    opacity:    0 !important;
    visibility: hidden !important;
    transform:  translateY(-8px) !important;
    transition: opacity .2s ease, transform .2s ease, visibility .2s ease !important;
    z-index:    99997 !important;
  }

  .pvc-search-form.active {
    opacity:    1 !important;
    visibility: visible !important;
    transform:  translateY(0) !important;
  }

  .pvc-search-input {
    width:         100% !important;
    border:        1px solid #e3e3e3 !important;
    border-radius: 8px !important;
    padding:       10px 12px !important;
    font-size:     14px !important;
    outline:       none !important;
    box-sizing:    border-box !important;
  }
  .pvc-search-input:focus { border-color: #c9a14a !important; }

  /* Results panel sits inside the dropdown */
  .pvc-search-form .pvc-search-results {
    position:    static !important;
    width:       100% !important;
    margin-top:  8px !important;
    border:      none !important;
    border-top:  1px solid #eee !important;
    border-radius: 0 !important;
    box-shadow:  none !important;
    max-height:  320px !important;
    overflow-y:  auto !important;
  }

  /* Mobile search form position */
  @media (max-width: 991px) {
    .pvc-search-form {
      right:     -95px !important;
      width:     92vw !important;
      max-width: 400px !important;
    }
  }
  @media (max-width: 480px) {
    .pvc-search-form {
      right: -80px !important;
      width: 94vw !important;
    }
  }

  /* ==========================================================
     CATEGORY MEGA-MENU — 4-column grid + scroll
  ========================================================== */
  .has-mega-category .pvc-mega-menu {
    width:     760px !important;
    max-width: 92vw !important;
  }

  .has-mega-category .pvc-mega-list {
    display:               grid !important;
    grid-template-columns: repeat(4, minmax(0, 1fr)) !important;
    column-gap:  28px !important;
    row-gap:      6px !important;
    max-height:  380px !important;
    overflow-y:  auto !important;
    padding-right: 10px !important;
    list-style:  none !important;
    margin:      0 !important;
  }

  .has-mega-category .pvc-mega-list li { min-width: 0 !important; }

  .has-mega-category .pvc-mega-list li a {
    display:       block !important;
    white-space:   nowrap !important;
    overflow:      hidden !important;
    text-overflow: ellipsis !important;
  }

  .has-mega-category .pvc-mega-list::-webkit-scrollbar       { width: 6px; }
  .has-mega-category .pvc-mega-list::-webkit-scrollbar-track { background: #f4f4f4; border-radius: 10px; }
  .has-mega-category .pvc-mega-list::-webkit-scrollbar-thumb { background: #c9a14a; border-radius: 10px; }

  @media (max-width: 1400px) {
    .has-mega-category .pvc-mega-menu  { width: 600px !important; }
    .has-mega-category .pvc-mega-list  { grid-template-columns: repeat(3, minmax(0, 1fr)) !important; }
  }

  /* ==========================================================
     MOBILE SLIDE-OUT MENU
  ========================================================== */
  @media (max-width: 991px) {

    .pvc-overlay {
      position:       fixed !important;
      inset:          0 !important;
      background:     rgba(0,0,0,.55) !important;
      opacity:        0 !important;
      visibility:     hidden !important;
      pointer-events: none !important;
      transition:     opacity .3s ease, visibility .3s ease !important;
      z-index:        99998 !important;
    }
    .pvc-overlay.active {
      opacity:        1 !important;
      visibility:     visible !important;
      pointer-events: auto !important;
    }

    .pvc-mobile-menu {
      position:   fixed !important;
      top:        0 !important;
      right:      0 !important;
      left:       auto !important;
      height:     100dvh !important;
      width:      270px !important;
      max-width:  82vw !important;
      background: #111 !important;
      box-sizing: border-box !important;
      padding:    18px 18px 24px !important;
      overflow-y: auto !important;
      z-index:    99999 !important;
      transform:  translateX(100%) !important;
      transition: transform .3s ease !important;
    }
    .pvc-mobile-menu.active { transform: translateX(0) !important; }

    .pvc-mobile-close {
      display:         flex !important;
      align-items:     center !important;
      justify-content: center !important;
      width:           32px !important;
      height:          32px !important;
      margin:          0 0 6px auto !important;
      border-radius:   50% !important;
      background:      rgba(255,255,255,.08) !important;
      font-size:       15px !important;
      cursor:          pointer !important;
      transition:      background .2s ease !important;
    }
    .pvc-mobile-close:hover { background: rgba(255,255,255,.18) !important; }
    .pvc-mobile-close i     { color: #fff !important; }

    body.no-scroll { overflow: hidden !important; }

    .pvc-mobile-nav-list { list-style: none !important; margin: 0 0 16px !important; padding: 0 !important; }
    .pvc-mobile-nav-item { width: 100% !important; }
    .pvc-mobile-nav-link {
      display:     block !important;
      width:       100% !important;
      white-space: nowrap !important;
      padding:     9px 0 !important;
      font-size:   14px !important;
      line-height: 1.2 !important;
    }

    .pvc-mobile-contact-title { font-size: 11px !important; letter-spacing: .06em !important; margin: 0 0 10px !important; }

    .pvc-mobile-btns    { display: flex !important; gap: 8px !important; flex-wrap: nowrap !important; margin-bottom: 14px !important; }
    .pvc-mobile-btn     { flex: 1 1 0 !important; min-width: 0 !important; white-space: nowrap !important; justify-content: center !important; padding: 9px 6px !important; font-size: 12px !important; gap: 5px !important; }

    .pvc-mobile-numbers { display: flex !important; flex-direction: column !important; gap: 7px !important; }
    .pvc-mobile-num     { font-size: 13px !important; white-space: nowrap !important; }

    .pvc-mobile-toggle span { background: #c9a14a !important; }
  }

  /* ==========================================================
     LIVE SEARCH RESULTS DROPDOWN
  ========================================================== */
  .pvc-search-results {
    display:       none;
    position:      absolute;
    top:           calc(100% + 8px);
    left:          0;
    width:         360px;
    max-height:    420px;
    overflow-y:    auto;
    background:    #fff;
    border:        1px solid #ececec;
    border-radius: 10px;
    box-shadow:    0 12px 30px rgba(0,0,0,.14);
    z-index:       1000;
  }
  .pvc-search-results.active { display: block; }

  .pvc-search-result-item {
    display:         flex;
    align-items:     center;
    gap:             12px;
    padding:         10px 14px;
    text-decoration: none;
    color:           #222;
    border-bottom:   1px solid #f4f4f4;
    transition:      .15s;
  }
  .pvc-search-result-item:last-child { border-bottom: none; }
  .pvc-search-result-item:hover      { background: #faf6ec; }

  .pvc-search-result-item img {
    width:        44px;
    height:       44px;
    object-fit:   contain;
    background:   #f6f6f8;
    border-radius: 6px;
    flex-shrink:  0;
  }

  .pvc-search-result-info {
    display:        flex;
    flex-direction: column;
    flex:           1;
    min-width:      0;
  }
  .pvc-search-result-name {
    font-size:     13px;
    font-weight:   600;
    color:         #1a1a1a;
    white-space:   nowrap;
    overflow:      hidden;
    text-overflow: ellipsis;
  }
  .pvc-search-result-meta {
    font-size:     11px;
    color:         #9a9a9a;
    margin-top:    2px;
    white-space:   nowrap;
    overflow:      hidden;
    text-overflow: ellipsis;
  }
  .pvc-search-result-price {
    font-size:   13px;
    font-weight: 700;
    color:       #c9a14a;
    flex-shrink: 0;
  }
  .pvc-search-empty,
  .pvc-search-loading {
    padding:    16px;
    text-align: center;
    font-size:  13px;
    color:      #9a9a9a;
  }

  /* Mobile Navigation */

  /* Bottom Navigation */
  @media (max-width: 991px) {
    .pvc-bottom-nav {
      position: fixed;
      bottom: 0;
      left: 0;
      width: 100%;
      height: 68px;
      background-color: #ffffff;
      border-top: 1px solid #eeeeee;
      box-shadow: 0 -5px 20px rgba(0, 0, 0, 0.05);
      border-radius: 18px 18px 0 0;
      display: flex;
      justify-content: space-around;
      align-items: center;
      z-index: 99999;
      padding-bottom: env(safe-area-inset-bottom);
      box-sizing: content-box;
    }

    /* Navigation Item */
    .pvc-bottom-nav-item {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      text-decoration: none;
      color: #888888;
      font-size: 11px;
      font-weight: 600;
      position: relative;
      height: 100%;
      flex: 1;
      transition: color 0.22s ease;
    }

    .pvc-bottom-nav-item i {
      font-size: 20px;
      margin-bottom: 4px;
      transition: color 0.22s ease;
    }

    .pvc-bottom-cart-badge {
      position: absolute !important;
      top: -6px !important;
      right: -8px !important;
      background: #c9a14a !important;
      color: #111111 !important;
      font-size: 9px !important;
      font-weight: 700 !important;
      min-width: 14px !important;
      height: 14px !important;
      border-radius: 50% !important;
      display: flex !important;
      align-items: center !important;
      justify-content: center !important;
      padding: 0 2px !important;
      line-height: 1 !important;
      pointer-events: none !important;
    }

    /* Active State */
    .pvc-bottom-nav-item.active {
      color: #c9a14a;
    }

    .pvc-bottom-nav-item.active i {
      color: #c9a14a;
    }

    .pvc-bottom-nav-item.active::after {
      content: '';
      position: absolute;
      bottom: 4px;
      left: 50%;
      transform: translateX(-50%);
      width: 16px;
      height: 3px;
      background-color: #c9a14a;
      border-radius: 2px;
    }

    /* Safe Area */
    @supports (padding-bottom: env(safe-area-inset-bottom)) {
      .pvc-bottom-nav {
        height: 68px;
      }
    }

    /* WhatsApp Position */
    .pvc-floating-whatsapp {
      bottom: calc(84px + env(safe-area-inset-bottom)) !important;
    }

    /* Mobile Header Fix */
    #pvc-mobile-toggle,
    .pvc-mobile-menu,
    .pvc-overlay {
      display: none !important;
    }
  }

  @media (min-width: 992px) {
    .pvc-bottom-nav {
      display: none !important;
    }
  }

  </style>

  <!-- Modern Mobile Bottom Navigation -->
  <nav class="pvc-bottom-nav" aria-label="Mobile Navigation">
    <a href="index.php" class="pvc-bottom-nav-item" id="bottom-nav-home" aria-label="Home">
      <i class="fa-solid fa-house"></i>
      <span>Home</span>
    </a>
    <a href="all-products.php" class="pvc-bottom-nav-item" id="bottom-nav-brands" aria-label="Brands">
      <i class="fa-solid fa-tags"></i>
      <span>Brands</span>
    </a>
    <a href="all-categories.php" class="pvc-bottom-nav-item" id="bottom-nav-categories" aria-label="Categories">
      <i class="fa-solid fa-border-all"></i>
      <span>Categories</span>
    </a>
    <a href="cart.php" class="pvc-bottom-nav-item" id="bottom-nav-rfq" aria-label="RFQ Cart">
      <div style="position: relative; display: inline-block;">
        <i class="fa-solid fa-cart-shopping"></i>
        <span class="pvc-bottom-cart-badge" id="pvc-bottom-cart-count" style="display: none;">0</span>
      </div>
      <span>RFQ</span>
    </a>
    <a href="contact-us.php" class="pvc-bottom-nav-item" id="bottom-nav-contact" aria-label="Contact Us">
      <i class="fa-solid fa-phone"></i>
      <span>Contact</span>
    </a>
  </nav>

<script>
document.addEventListener('DOMContentLoaded', function () {
(function () {

  /* ── DOM refs ─────────────────────────────────────────── */
  const header          = document.getElementById('pvc-global-header');
  const spacer          = document.getElementById('pvc-header-spacer');
  const mobileToggle    = document.getElementById('pvc-mobile-toggle');
  const mobileMenu      = document.getElementById('pvc-mobile-menu');
  const mobileClose     = document.getElementById('pvc-mobile-close');
  const overlay         = document.getElementById('pvc-overlay');
  const searchToggle    = document.querySelector('.pvc-search-btn-toggle');
  const searchForm      = document.getElementById('pvc-search-form');
  const searchContainer = document.getElementById('pvc-search-container');
  const searchInput     = document.getElementById('liveSearchInput');
  const resultsBox      = document.getElementById('pvc-search-results');

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
  function openMobileMenu()  {
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
  mobileClose .addEventListener('click', closeMobileMenu);
  overlay     .addEventListener('click', closeMobileMenu);

  /* ── Active nav highlight ─────────────────────────────── */
  const currentPage = window.location.pathname.split('/').pop() || 'index.php';
  const urlParams   = new URLSearchParams(window.location.search);

  document.querySelectorAll('.pvc-nav-list .pvc-nav-link').forEach(link => {
    const href = link.getAttribute('href');
    if (href === 'all-categories.php') return;
    if (href === currentPage) link.classList.add('active');
  });

  document.querySelectorAll('.pvc-mobile-nav-link').forEach(link => {
    const parent = link.closest('.pvc-mobile-nav-item');
    const href   = link.getAttribute('href');
    if (href === 'all-categories.php') return;
    if (href === currentPage) {
      link.classList.add('active');
      if (parent) parent.classList.add('active');
    }
  });

  if (currentPage === 'all-categories.php') {
    const key    = urlParams.has('brand') ? 'nav-brand'     : 'nav-categories';
    const mobKey = urlParams.has('brand') ? 'mob-nav-brand' : 'mob-nav-categories';
    const el     = document.getElementById(key);
    const elMob  = document.getElementById(mobKey);
    if (el)    el.classList.add('active');
    if (elMob) { elMob.classList.add('active'); elMob.closest('.pvc-mobile-nav-item').classList.add('active'); }
  }

  /* ── Cart badge ───────────────────────────────────────── */
  function updateCartCount() {
    const el = document.getElementById('pvc-cart-count');
    const elBottom = document.getElementById('pvc-bottom-cart-count');
    try {
      const cart  = JSON.parse(localStorage.getItem('pvcCart')) || [];
      const count = cart.reduce((t, i) => t + (i.quantity || 0), 0);
      
      if (el) {
        el.textContent   = count;
        el.style.display = count > 0 ? 'flex' : 'none';
      }
      if (elBottom) {
        elBottom.textContent   = count;
        elBottom.style.display = count > 0 ? 'flex' : 'none';
      }
    } catch(e) {
      if (el) el.style.display = 'none';
      if (elBottom) elBottom.style.display = 'none';
    }
  }
  updateCartCount();
  window.addEventListener('storage',          updateCartCount);
  window.addEventListener('pvc-cart-updated', updateCartCount);

  /* ── Search helpers ───────────────────────────────────── */
  function closeResults() {
    if (!resultsBox) return;
    resultsBox.innerHTML = '';
    resultsBox.classList.remove('active');
  }

  function closeSearch() {
    closeResults();
  }

  /* ── Click anywhere outside search → close everything ─── */
  document.addEventListener('click', function (e) {
    if (searchContainer && !searchContainer.contains(e.target)) {
      closeSearch();
    }
  });

  /* ── Misc helpers ─────────────────────────────────────── */
  function escapeHtml(str) {
    return String(str).replace(/[&<>"']/g, m =>
      ({ '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;' }[m])
    );
  }

  function highlight(text, q) {
    const safe  = q.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    const regex = new RegExp('(' + safe + ')', 'gi');
    return escapeHtml(text).replace(regex,
      '<mark style="background:#fff3cd;color:#1a1a1a;font-weight:700;border-radius:2px;padding:0 2px;">$1</mark>'
    );
  }

  /* ── Badge config per result type ────────────────────── */
  const BADGE = {
    product:  { bg: '#e8f4fd', color: '#1a6fa8', text: 'Product'  },
    brand:    { bg: '#fef3e2', color: '#b8711a', text: 'Brand'    },
    category: { bg: '#edfaee', color: '#1a7a2e', text: 'Category' },
  };

  /* ── Render dropdown results ──────────────────────────── */
  function renderResults(items, query) {
    if (!resultsBox) return;

    if (!items || !items.length) {
      resultsBox.innerHTML =
        '<div class="pvc-search-empty">No results found for "<strong>' +
        escapeHtml(query) + '</strong>"</div>';
      resultsBox.classList.add('active');
      return;
    }

    resultsBox.innerHTML = items.map(item => {
      const type  = item.type || 'product';
      const badge = BADGE[type] || BADGE.product;
      const img   = (item.pimage && item.pimage.trim())
                    ? item.pimage
                    : 'assets/img/logo/logo1.png';
      return `
<a class="pvc-search-result-item" href="${escapeHtml(item.url)}">
  <img src="${escapeHtml(img)}"
       alt="${escapeHtml(item.label)}"
       loading="lazy"
       onerror="this.onerror=null;this.src='assets/img/logo/logo1.png';">
  <div class="pvc-search-result-info">
    <span class="pvc-search-result-name" style="display:flex;align-items:center;gap:6px;flex-wrap:wrap;">
      <span>${highlight(item.label, query)}</span>
      <span style="font-size:10px;font-weight:600;padding:2px 7px;border-radius:4px;
                   background:${badge.bg};color:${badge.color};flex-shrink:0;">${badge.text}</span>
    </span>
    ${item.sublabel
      ? `<span class="pvc-search-result-meta">${escapeHtml(item.sublabel)}</span>`
      : ''}
  </div>
</a>`;
    }).join('');

    resultsBox.classList.add('active');
  }

  /* ── Live search (debounced + abortable) ─────────────── */
  if (searchInput && resultsBox) {
    let debounceTimer    = null;
    let activeController = null;

    searchInput.addEventListener('input', function () {
      const query = this.value.trim();
      clearTimeout(debounceTimer);

      if (query.length < 1) { closeResults(); return; }

      resultsBox.innerHTML = '<div class="pvc-search-loading">Searching…</div>';
      resultsBox.classList.add('active');

      debounceTimer = setTimeout(() => {
        if (activeController) activeController.abort();
        activeController = new AbortController();

        fetch('search-suggest.php?q=' + encodeURIComponent(query), {
          signal: activeController.signal
        })
          .then(res => {
            if (!res.ok) throw new Error('HTTP ' + res.status);
            return res.json();
          })
          .then(items => renderResults(items, query))
          .catch(err => {
            if (err.name !== 'AbortError') {
              console.error('Search error:', err);
              resultsBox.innerHTML =
                '<div class="pvc-search-empty">Something went wrong. Please try again.</div>';
            }
          });
      }, 280);
    });

    /* Escape closes entire search */
    searchInput.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') { closeSearch(); this.blur(); }
    });
  }

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
    } else if (currentLoc === 'contact-us.php') {
      activeId = 'bottom-nav-contact';
    }

    if (activeId) {
      const activeEl = document.getElementById(activeId);
      if (activeEl) {
        activeEl.classList.add('active');
        activeEl.setAttribute('aria-current', 'page');
      }
    }
  })();

})();
});
</script>