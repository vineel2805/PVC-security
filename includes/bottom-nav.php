<!-- Material Design 3 / Android-First Mobile Bottom Navigation (Dual-State SVGs) -->
<nav class="pvc-bottom-nav" aria-label="Mobile Navigation">
  <a href="index.php" class="pvc-bottom-nav-item<?php echo ($activeTab === 'home') ? ' active" aria-current="page' : ''; ?>" id="bottom-nav-home" aria-label="Home">
    <div class="pvc-nav-icon-wrap">
      <!-- Inactive Outline Icon -->
      <svg class="icon-outline" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9.5L12 3l9 6.5V20a1 1 0 0 1-1 1h-5v-6h-6v6H4a1 1 0 0 1-1-1V9.5z"/></svg>
      <!-- Active Filled Icon -->
      <svg class="icon-filled" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.1a1 1 0 0 1 .6.2l9 6.75a1 1 0 0 1 .4.8V20a2 2 0 0 1-2 2h-4a1 1 0 0 1-1-1v-5h-6v5a1 1 0 0 1-1 1H4a2 2 0 0 1-2-2V9.85a1 1 0 0 1 .4-.8l9-6.75a1 1 0 0 1 .6-.2z"/></svg>
    </div>
    <span>Home</span>
  </a>
  <a href="all-products.php" class="pvc-bottom-nav-item<?php echo ($activeTab === 'brands') ? ' active" aria-current="page' : ''; ?>" id="bottom-nav-brands" aria-label="Brands">
    <div class="pvc-nav-icon-wrap">
      <!-- Inactive Outline Icon -->
      <svg class="icon-outline" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2H2v10l9.29 9.29a2.4 2.4 0 0 0 3.42 0l6.58-6.58a2.4 2.4 0 0 0 0-3.42L12 2Z"/><circle cx="7" cy="7" r="1.5"/></svg>
      <!-- Active Filled Icon -->
      <svg class="icon-filled" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="currentColor"><path d="M12.586 2.586A2 2 0 0 0 11.172 2H3a1 1 0 0 0-1 1v8.172a2 2 0 0 0 .586 1.414l10 10a2 2 0 0 0 2.828 0l7-7a2 2 0 0 0 0-2.828l-10-10zM7 8a2 2 0 1 1 0-4 2 2 0 0 1 0 4z"/></svg>
    </div>
    <span>Brands</span>
  </a>
  <!-- CENTER SEARCH BUTTON — opens the inline #globalSearchOverlay (from includes/search-overlay.php) via JS. Not a link, since there is no standalone search.php page. -->
  <button type="button" class="pvc-bottom-nav-item pvc-nav-search-center" id="bottom-nav-search" aria-label="Search" aria-haspopup="dialog" aria-controls="globalSearchOverlay">
    <div class="pvc-nav-icon-wrap">
      <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
    </div>
    <span>Search</span>
  </button>
  <a href="all-categories.php" class="pvc-bottom-nav-item<?php echo ($activeTab === 'categories') ? ' active" aria-current="page' : ''; ?>" id="bottom-nav-categories" aria-label="Categories">
    <div class="pvc-nav-icon-wrap">
      <!-- Inactive Outline Icon -->
      <svg class="icon-outline" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="7" height="7" x="3" y="3" rx="1.5"/><rect width="7" height="7" x="14" y="3" rx="1.5"/><rect width="7" height="7" x="14" y="14" rx="1.5"/><rect width="7" height="7" x="3" y="14" rx="1.5"/></svg>
      <!-- Active Filled Icon -->
      <svg class="icon-filled" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="currentColor"><rect width="8" height="8" x="2.5" y="2.5" rx="2"/><rect width="8" height="8" x="13.5" y="2.5" rx="2"/><rect width="8" height="8" x="13.5" y="13.5" rx="2"/><rect width="8" height="8" x="2.5" y="13.5" rx="2"/></svg>
    </div>
    <span>Categories</span>
  </a>
  <a href="cart.php" class="pvc-bottom-nav-item<?php echo ($activeTab === 'cart') ? ' active" aria-current="page' : ''; ?>" id="bottom-nav-cart" aria-label="Cart">
    <div class="pvc-nav-icon-wrap">
      <span class="pvc-bottom-cart-badge" id="pvc-bottom-cart-count">0</span>
      <!-- Inactive Outline Icon -->
      <svg class="icon-outline" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
      <!-- Active Filled Icon -->
      <svg class="icon-filled" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="currentColor"><path d="M7 22a2 2 0 1 0 0-4 2 2 0 0 0 0 4zm10 0a2 2 0 1 0 0-4 2 2 0 0 0 0 4zM2 2h2.2l.68 2H21a1 1 0 0 1 .96 1.28l-2.5 8.5A2 2 0 0 1 17.55 15H8.1l-.3 1.5H19v2H7a1.5 1.5 0 0 1-1.47-1.8l.62-3.1L3.8 4H2V2z"/></svg>
    </div>
    <span>Cart</span>
  </a>
</nav>