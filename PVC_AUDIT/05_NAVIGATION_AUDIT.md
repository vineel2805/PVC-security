# Navigation Audit

## Navigation Map
Home
- index.php
- Links to all-products.php, about-us.php, services.php, contact-us.php

Brand
- all-products.php?brand=<brandid>
- Can be reached from header mega-menu and home page brand cards

Category
- all-categories.php?catname=<category>
- also reachable via all-products.php?brand=<brand>&cat=<category>

Product
- all-products.php?pid=<productid>

Cart
- cart.php

Back
- Breadcrumbs and back links present on category and product views

## Verified Paths
- Home link: present in header and footer.
- About, services, contact: present.
- Brand navigation: present in header and home page.
- Category navigation: present in header and category pages.
- Cart link: present in header and cart page.

## Findings
### Broken routes
Evidence:
- The code contains references to products.php in several places, but this file does not exist in the public web root.
- The header and search logic also contain hard-coded route assumptions for categories such as network-cameras.php and turbo-hd.php, which are not present in the project.
Severity: High
Impact: Users may land on dead links or 404 pages.

### Inconsistent parameter use
Evidence:
- Some links use ?brand=... while others use ?category=... and some use ?cat=... for category context.
Severity: Medium
Impact: Navigation becomes inconsistent and harder to reason about.

### Missing route fallback
Evidence:
- The site does not clearly fallback when a requested brand/category/product ID is invalid.
Severity: Medium
Impact: Broken links can lead to empty or confusing pages.
