# Page-by-Page Audit

## index.php
Purpose:
- Home page with hero carousel, search, featured brand cards, and quick links.
Dependencies:
- header.php, connect.php, multiple CSS/JS assets.
SQL queries:
- SELECT brandid, brandname, imagelink FROM brands WHERE status='Active' AND display_status=1.
Findings:
- The page includes an inline search form that submits to all-products.php.
- Some quick links target all-products.php?category=ACCESSORIES, which is a route assumption that may not match the actual catalog structure.
- The hero carousel uses several SVG/image assets; some may be visually heavy but not necessarily broken.
Severity: Medium

## header.php
Purpose:
- Shared header, mega-menu, mobile menu, cart button.
Dependencies:
- connect.php.
SQL queries:
- Brands query: SELECT brandid, brandname, imagelink FROM brands WHERE status='Active'.
- Categories query: SELECT TRIM(REPLACE(...)) ... GROUP BY key_name FROM category WHERE status='Active'.
Findings:
- Navigation is dynamically generated but still uses a merged category approach that can hide legitimate duplicates.
- The header includes a mobile menu and cart badge, but some labels use all-products.php in a way that does not always align with the actual product route logic.
Severity: Medium

## all-products.php
Purpose:
- Main product catalog and product detail page.
Dependencies:
- connect.php, header.php.
SQL queries:
- Brand and category lookups, product detail lookup, product listing query.
Findings:
- The page has a product detail mode and a category/brand tile mode.
- It relies heavily on query parameters such as brand, cat, and pid.
- The page uses direct mysqli strings with escaped variables, which is functional but not ideal for maintainability.
Severity: Medium

## all-categories.php
Purpose:
- Main category browsing experience.
Dependencies:
- connect.php, header.php.
SQL queries:
- Category listing, brand listing, product existence checks, product listing by category.
Findings:
- The category page appears to support both direct category paths and brand/category context.
- The page contains a lot of conditional view logic and may be harder to maintain.
Severity: Medium

## about-us.php
Purpose:
- Company profile and brand story page.
Dependencies:
- header.php.
SQL queries:
- None.
Findings:
- The page is mostly static and visually rich.
- Content is not connected to the catalog or CMS, so it will require manual editing for future changes.
Severity: Low

## contact-us.php
Purpose:
- Contact and support page.
Dependencies:
- header.php.
SQL queries:
- None.
Findings:
- Contact actions are useful, but the page is mostly static and lacks a captured lead form or backend processing.
Severity: Low

## cart.php
Purpose:
- RFQ/cart experience.
Dependencies:
- header.php, assets/js/cart.js.
SQL queries:
- None.
Findings:
- The cart is a client-side quotation flow using localStorage, not a real server-backed checkout process.
- This is functional for a quote request but not a full ecommerce checkout.
Severity: Medium

## 404.php
Purpose:
- Error page.
Dependencies:
- header.php.
SQL queries:
- None.
Findings:
- Basic fallback page present, but no deep redirect or suggestions layer.
Severity: Low

## admin pages
Purpose:
- CRUD interface for brands, categories, products, and login.
Dependencies:
- admin/config/db.php and admin layout includes.
SQL queries:
- Multiple prepared statements and lookups.
Findings:
- CRUD logic is present and functional.
- The login flow is weak and uses a direct password comparison against a stored hash.
Severity: High
