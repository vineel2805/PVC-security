# Search Audit

## Search Implementation
The live search endpoint is implemented in search-suggest.php and is called by the homepage and header search UI.

## Data Flow
Search Input
- UI input on home page and header
- JavaScript sends request to search-suggest.php
- PHP queries products, brands, and categories
- Results returned as JSON and displayed in the dropdown

## Evidence
The search endpoint checks:
- Products: pname, brandname, category name
- Brands: brandname
- Categories: cname

The query uses LOWER() and simple LIKE matching.

## Findings
### Expected vs Actual
Expected:
- Users should be able to search by brand, category, product name, and common synonyms.
Actual:
- Results are limited to simple LIKE matching against a few columns.
- No SKU search, no description search, no fuzzy matching, no stemming, no synonyms, no ranking beyond simple prefix preference.
Severity: High

### Test Queries
- camera: likely returns products with camera in the name, but may miss variants.
- bullet camera: likely returns limited results, depending on exact text.
- dome: may return partial results but not all relevant products.
- hikvision: likely returns brand matches and product matches by brand.
- tp link / tp-link / router / switch / cctv / dvr / nvr / ssd / hdd / cable / monitor / wifi
These terms are not fully normalized and may not match consistently due to the lack of tokenization and synonym handling.

### Root Cause
The search logic is implemented as a simple string search rather than a full-text or relevance-based search layer.

### Business Impact
Poor search quality reduces product discoverability and increases bounce rate.
