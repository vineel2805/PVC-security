# CSS Audit

## Overview
The CSS architecture suffers from specificity wars, duplication, and missing variables.

## Duplicate CSS Rules
- **File:** `all-products.css` and `search.php` (inline)
  - **Issue:** Both define identical `.product-card` and `.product-image` rules.
  - **Fix:** Move to a shared `components.css`.

## Repeated Properties
- **Colors:** Hex `#b8860b` (Gold) and `#111` (Black) are repeated over 80 times across 32 CSS files.
- **Border Radius:** `border-radius: 8px` and `12px` are redefined continuously instead of using a variable (e.g., `--radius-md`).

## Conflicting Styles & Specificity
- **File:** `global_header.css` vs `all-products.css`
  - **Issue:** Generic class names like `.search-input` previously caused conflicts. While partially fixed, similar global selectors (e.g., `[class*="-hero"]`) still exist and cause layout bleed.

## !important Abuse
- **File:** `all-products.css`
  - **Line:** ~690 (`overflow: visible !important;`)
  - **Line:** ~595 (`width: 100% !important;`)
  - **Issue:** Overriding layout constraints using `!important` instead of fixing the root specificity issue.

## Hardcoded Values
- **File:** `cart_styles.css`
  - **Issue:** Hardcoded `z-index: 9999` and `top: 60px` which breaks when the header height changes.

## Media Queries
- **Issue:** Breakpoints are inconsistent. Some files use `max-width: 991px`, others `992px`. Some use `768px`, others `767px`. This causes layout flickering on specific tablet resolutions.
