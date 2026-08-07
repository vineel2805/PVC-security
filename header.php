<?php include 'connect.php'; ?>
<?php include 'includes/search-overlay.php'; ?>
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
   <?php if (($currentPage ?? '') !== 'cart.php'): ?>
    <?php include 'includes/bottom-nav.php'; ?>
<?php endif; ?>
<script src="assets/js/global_header.js" defer></script>
