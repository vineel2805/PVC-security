# Duplicate Code Analysis

## PHP & Logic Duplication
- **Database Connection:** Included repetitively instead of using a singleton or dependency injection.
- **Rendering Functions:**
  - `renderProductsGrid()` in `all-products.php`
  - HTML structure for product cards is heavily duplicated between `all-products.php`, `index.php` (featured products), and `search.php` (search results).
- **SQL Queries:**
  - `SELECT * FROM products WHERE display_status = 1` is repeated with slight variations in multiple files.

## JavaScript Duplication
- **AJAX Fetch Logic:**
  - The `fetch()` and DOM replacement logic in `all-products.php` (`loadProducts()`) is nearly identical to pagination logic in other parts of the site.
- **Debouncing:**
  - Debounce functions are redefined locally in `search.php` and header search scripts instead of importing a shared utility.

## HTML Layout Duplication
- **Headers & Footers:** 
  - While mostly unified, mobile-specific HTML injection for navigation/footers repeats structural classes instead of using single source-of-truth templates.

## Duplication Percentage
Estimated **25-30%** of the codebase consists of structural or logical duplication that could be abstracted into shared components/functions.
