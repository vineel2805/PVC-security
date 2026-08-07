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
  <a href="all-categories.php" class="pvc-bottom-nav-item<?php echo ($activeTab === 'categories') ? ' active" aria-current="page' : ''; ?>" id="bottom-nav-categories" aria-label="Categories">
    <div class="pvc-nav-icon-wrap">
      <!-- Inactive Outline Icon -->
      <svg class="icon-outline" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="7" height="7" x="3" y="3" rx="1.5"/><rect width="7" height="7" x="14" y="3" rx="1.5"/><rect width="7" height="7" x="14" y="14" rx="1.5"/><rect width="7" height="7" x="3" y="14" rx="1.5"/></svg>
      <!-- Active Filled Icon -->
      <svg class="icon-filled" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="currentColor"><rect width="8" height="8" x="2.5" y="2.5" rx="2"/><rect width="8" height="8" x="13.5" y="2.5" rx="2"/><rect width="8" height="8" x="13.5" y="13.5" rx="2"/><rect width="8" height="8" x="2.5" y="13.5" rx="2"/></svg>
    </div>
    <span>Categories</span>
  </a>
  <a href="contact-us.php" class="pvc-bottom-nav-item<?php echo ($activeTab === 'support') ? ' active" aria-current="page' : ''; ?>" id="bottom-nav-rfq" aria-label="Contact Us">
    <div class="pvc-nav-icon-wrap">
      <!-- Inactive Outline Icon -->
      <svg class="icon-outline" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
      <!-- Active Filled Icon -->
      <svg class="icon-filled" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="currentColor"><path d="M6.62 10.79a15.053 15.053 0 0 0 6.59 6.59l2.2-2.2a1 1 0 0 1 1.01-.24c1.12.37 2.33.57 3.58.57a1 1 0 0 1 1 1v3.5a1 1 0 0 1-1 1A17 17 0 0 1 3 4a1 1 0 0 1 1-1h3.5a1 1 0 0 1 1 1c0 1.25.2 2.46.57 3.58a1 1 0 0 1-.25 1.02l-2.2 2.19z"/></svg>
    </div>
    <span>Support</span>
  </a>
</nav>