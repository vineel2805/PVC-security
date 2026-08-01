# Project Structure Audit

## Overview
The project is a PHP storefront with an admin module. The public website is organized as standalone PHP pages in the web root, while the admin area lives under the admin/ directory.

## Root Structure
- index.php: Home page
- all-products.php: Product and category listing page
- all-categories.php: Category browsing page
- about-us.php: About page
- contact-us.php: Contact page
- services.php: Services page
- cart.php: RFQ/cart experience
- search-suggest.php: AJAX autocomplete endpoint
- connect.php: Shared mysqli connection
- header.php: Shared site header and navigation


## Admin Structure
- admin/index.php: Login page
- admin/brands.php: Brand CRUD
- admin/categories.php: Category CRUD
- admin/products.php: Product CRUD
- admin/config/db.php: PDO database connection
- admin/sidebar.php: Admin navigation
- admin/nav_header.php / admin/header.php: Admin layout scaffolding

## Architecture Notes
- Public pages use mysqli directly through connect.php.
- Admin pages use PDO through admin/config/db.php.
- Shared UI components such as the header and footer are included by multiple pages.
- Frontend assets are split into CSS, JS, and images under assets/.

## Dependency Notes
- Bootstrap, Font Awesome, AOS, jQuery, Owl Carousel, and other plugins are bundled in assets/css/plugins and assets/js/plugins.
- The admin area includes a large vendored UI kit under admin/vendor/.

## Strengths
- Clear separation between public and admin content.
- Shared include files reduce duplication.
- The admin CRUD screens are fairly comprehensive.

## Risks
- Mixed PHP database access patterns increase maintenance complexity.
- Many pages include heavy CSS/JS bundles even when only a subset is needed.
- There is evidence of duplicated or legacy UI code across multiple templates.
