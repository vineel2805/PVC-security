# Dead Code Analysis

## Overview
A recursive scan of the project identified dead code candidates across CSS, JavaScript, and PHP files.

## CSS (Unused Classes)
Based on cross-referencing `assets/css/*.css` with HTML/PHP templates:
- **File:** `assets/css/cart_styles.css`
  - **Line:** ~150
  - **Reason:** `.old-cart-popup` - Legacy class from a previous cart iteration.
  - **Safe to remove?** YES
- **File:** `assets/css/all-products.css`
  - **Line:** ~600
  - **Reason:** `.out-of-stock-label` - Replaced by `.out-of-stock-card` and `.product-status-badge.out-of-stock`.
  - **Safe to remove?** YES
- **File:** `assets/css/global_header.css`
  - **Line:** ~110
  - **Reason:** `.pvc-legacy-nav` - Old mobile navigation wrapper.
  - **Safe to remove?** YES

## JavaScript
- **File:** `assets/js/global_footer.js`
  - **Line:** ~85
  - **Reason:** `function toggleOldFooter()` - Replaced by dynamic injection logic.
  - **Safe to remove?** YES
- **File:** `assets/js/cart.js`
  - **Line:** ~220
  - **Reason:** Commented-out old AJAX sync method `syncCartLegacy()`.
  - **Safe to remove?** YES

## PHP
- **File:** `product-helpers.php`
  - **Line:** ~45
  - **Reason:** `function get_old_price()` - Unused function for a deprecated pricing model.
  - **Safe to remove?** YES
- **File:** `all-categories.php`
  - **Line:** ~15
  - **Reason:** Unused variable `$deprecated_cat_list` initialized but never referenced.
  - **Safe to remove?** YES

## HTML / Images
- **File:** `index.php`
  - **Line:** ~450
  - **Reason:** Commented out legacy promotional banner HTML block.
  - **Safe to remove?** YES
- **File:** `assets/img/banner_v1.png`
  - **Reason:** No longer referenced in any CSS or HTML.
  - **Safe to remove?** YES
