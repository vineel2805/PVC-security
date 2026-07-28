# Brand Module Audit

## Inspection Summary
The brand module is implemented through the brands table and the public brand listing on the home page, plus filtering in all-products.php.

## Evidence
- The home page queries brands where status='Active' and display_status=1.
- The header also uses a brands query to populate the mega-menu.
- The admin CRUD page manages brands and their images.

## Findings
### Duplicate brands
Evidence:
- Live DB contains SECUREYE twice.
Severity: Medium
Impact: Brand filtering and navigation can duplicate or confuse the same brand.

### Brand images
The brand images are stored in the database and rendered on the home page. Some image paths appear to be stored relative to the public root or include ../ segments.
Severity: Medium

### Brand status handling
Brands are filtered by status and display_status in the storefront, but the code path is not fully consistent across all public pages.
Severity: Medium

### Brand product mapping
The product catalog uses brandid in products, so brand filtering is possible. However, the current query logic and route assumptions may not match the category-based navigation model consistently.
Severity: Medium
