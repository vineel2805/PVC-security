<?php
/*
 * all-categories.php
 * ------------------------------------------------------------------
 * Same conversion as all-products.php, with one extra step: in your
 * original file, <!DOCTYPE html><head>...</head><body> was already
 * printed BEFORE any PHP ran (connect.php, the queries, etc. were
 * included inside <body>). That meant there was no way to bail out
 * with a clean JSON response for AJAX — the doctype/head/body-open
 * would already be sent.
 *
 * So here the data-loading block (params, sidebarCats, brandRow,
 * catRow, viewMode, products, tiles) has been moved ABOVE the
 * <!DOCTYPE html> line, same as all-products.php. Everything it does
 * is otherwise identical to your original — same queries, same
 * merge/normalize logic for category names, same view-mode branches.
 * ------------------------------------------------------------------
 */

ini_set('display_errors', 1);
error_reporting(E_ALL);

include 'connect.php';
include 'product-helpers.php';

$defaultImg = get_default_placeholder_img();

function db_image($path, $defaultImg) {
    $path = trim((string)$path);
    if ($path === '') {
        return $defaultImg;
    }
    if (strpos($path, '../uploads/') === 0) {
        $path = substr($path, 3);
    }
    return $path;
}

/* ===================================================================
   DATA LOADING — identical logic to your original file
   =================================================================== */

$selectedBrand   = isset($_GET['brand'])   ? trim($_GET['brand'])   : '';
$selectedCat     = isset($_GET['cat'])     ? trim($_GET['cat'])     : '';
$selectedCatName = isset($_GET['catname']) ? trim($_GET['catname']) : '';

$selectedBrandEscaped   = mysqli_real_escape_string($con, $selectedBrand);
$selectedCatEscaped     = mysqli_real_escape_string($con, $selectedCat);
$selectedCatNameEscaped = mysqli_real_escape_string($con, $selectedCatName);

// ── 1. Fetch all merged categories for sidebar ──────────────────────
$sidebarCats = [];
$sidebarCatRes = mysqli_query($con, "
    SELECT
        TRIM(REPLACE(REPLACE(REPLACE(UPPER(cat.cname), '  ', ' '), '  ', ' '), '  ', ' ')) AS key_name,
        MIN(cat.cname)  AS cname,
        MIN(cat.cimage) AS cimage
    FROM   category cat
    WHERE  cat.display_status = 1
      AND  EXISTS (
            SELECT 1 FROM products p
            WHERE p.pcat = cat.cid AND p.status = 'Active' AND p.display_status = 1
          )
    GROUP  BY key_name
    ORDER  BY key_name ASC
");
if ($sidebarCatRes) {
    while ($row = mysqli_fetch_assoc($sidebarCatRes)) $sidebarCats[] = $row;
}

// ── 2. Resolve brand row ─────────────────────────────────────────────
$brandRow = null;
if (!empty($selectedBrandEscaped)) {
    $bl = mysqli_query($con, "
        SELECT * FROM brands
        WHERE  brandid = '$selectedBrandEscaped'
          AND  status = 'Active'
          AND  display_status = 1
    ");
    if ($bl && mysqli_num_rows($bl) > 0) $brandRow = mysqli_fetch_assoc($bl);
}

// ── 3. Resolve single category (must belong to the brand) ───────────
$catRow = null;
if ($brandRow && !empty($selectedCatEscaped)) {
    $cl = mysqli_query($con, "
        SELECT * FROM category
        WHERE  cid = '$selectedCatEscaped'
          AND  brandid = '{$brandRow['brandid']}'
          AND  display_status = 1
    ");
    if ($cl && mysqli_num_rows($cl) > 0) $catRow = mysqli_fetch_assoc($cl);
}

// ── 4. Determine view mode ────────────────────────────────────────────
$viewMode           = 'tiles_all';
$brandCategoryTiles = [];
$products           = [];
$totalRows          = 0;
$namedCatIds        = [];

// ── 4a. Cross-brand category name view ───────────────────────────────
if (!empty($selectedCatNameEscaped)) {
    $viewMode = 'products_named';

    $brandClause = $brandRow ? "AND c.brandid = '{$brandRow['brandid']}'" : '';

    $cidRes = mysqli_query($con, "
        SELECT c.cid, c.cname, b.brandname
        FROM   category c
        JOIN   brands   b ON b.brandid = c.brandid
        WHERE  TRIM(REPLACE(REPLACE(REPLACE(UPPER(c.cname), '  ', ' '), '  ', ' '), '  ', ' '))
               = TRIM(REPLACE(REPLACE(REPLACE(UPPER('$selectedCatNameEscaped'), '  ', ' '), '  ', ' '), '  ', ' '))
          AND  c.display_status = 1
          AND  b.display_status = 1
          $brandClause
    ");
    if ($cidRes) {
        while ($r = mysqli_fetch_assoc($cidRes)) $namedCatIds[] = $r['cid'];
    }

    if (!empty($namedCatIds)) {
        $inList = "'" . implode("','", array_map(function($id) use ($con) {
            return mysqli_real_escape_string($con, $id);
        }, $namedCatIds)) . "'";

        $res = mysqli_query($con, "
            SELECT   p.*,
                     c.cname     AS cat_display,
                     c.cimage    AS cat_image,
                     c.status    AS cat_stock_status,
                     b.brandname AS brand_display
            FROM     products p
            JOIN     brands   b ON b.brandid = p.brandid
            JOIN     category c ON c.cid     = p.pcat
            WHERE    p.pcat   IN ($inList)
              AND    p.status = 'Active'
              AND    p.display_status = 1
              AND    b.status = 'Active'
              AND    b.display_status = 1
              AND    c.display_status = 1
            ORDER    BY b.brandname ASC, p.pname ASC
        ");
        if ($res) {
            $totalRows = mysqli_num_rows($res);
            while ($row = mysqli_fetch_assoc($res)) $products[] = $row;
        }
    }

// ── 4b. Brand + exact cid → single category products ─────────────────
} elseif ($brandRow && $catRow) {
    $viewMode = 'products';
    $res = mysqli_query($con, "
        SELECT   p.*,
                 c.cname     AS cat_display,
                 c.cimage    AS cat_image,
                 c.status    AS cat_stock_status,
                 b.brandname AS brand_display
        FROM     products p
        JOIN     brands   b ON b.brandid = p.brandid
        JOIN     category c ON c.cid     = p.pcat
        WHERE    p.pcat   = '{$catRow['cid']}'
          AND    p.status = 'Active'
          AND    p.display_status = 1
          AND    b.status = 'Active'
          AND    b.display_status = 1
          AND    c.display_status = 1
        ORDER BY p.pname ASC
    ");
    if ($res) {
        $totalRows = mysqli_num_rows($res);
        while ($row = mysqli_fetch_assoc($res)) $products[] = $row;
    }

// ── 4c. Brand only → that brand's category tiles ──────────────────────
} elseif ($brandRow) {
    $viewMode = 'tiles_brand';
    $tr = mysqli_query($con, "
        SELECT *
        FROM   category
        WHERE  brandid = '{$brandRow['brandid']}'
          AND  display_status = 1
          AND  EXISTS (
                SELECT 1 FROM products p
                WHERE p.pcat = category.cid AND p.status = 'Active' AND p.display_status = 1
              )
        ORDER  BY cname ASC
    ");
    if ($tr) {
        while ($tile = mysqli_fetch_assoc($tr)) $brandCategoryTiles[] = $tile;
    }

// ── 4d. No brand, no catname → merged tiles ─────────────────────────────
} else {
    $viewMode = 'tiles_all';

    $tr = mysqli_query($con, "
        SELECT c.cid, c.cname, c.cimage, c.brandid, b.brandname
        FROM   category c
        JOIN   brands   b ON b.brandid = c.brandid
        WHERE  c.display_status = 1
          AND  b.display_status = 1
          AND  EXISTS (
                SELECT 1 FROM products p
                WHERE p.pcat = c.cid AND p.status = 'Active' AND p.display_status = 1
              )
        ORDER  BY UPPER(c.cname) ASC, b.brandname ASC
    ");

    $mergedMap = [];
    if ($tr) {
        while ($row = mysqli_fetch_assoc($tr)) {
            $key = preg_replace('/\s+/', ' ', strtoupper(trim($row['cname'])));
            if (!isset($mergedMap[$key])) {
                $mergedMap[$key] = [
                    'cname'  => $row['cname'],
                    'cimage' => $row['cimage'],
                    'brands' => [],
                    'cids'   => [],
                ];
            }
            $mergedMap[$key]['brands'][] = $row['brandname'];
            $mergedMap[$key]['cids'][]   = $row['cid'];
        }
    }
    $brandCategoryTiles = array_values($mergedMap);
}

// ── 5. Page title ────────────────────────────────────────────────────
$pageTitle = "All Products";
if ($brandRow)        $pageTitle  = htmlspecialchars($brandRow['brandname']) . " Products";
if ($catRow)          $pageTitle .= " — " . htmlspecialchars($catRow['cname']);
if ($selectedCatName) $pageTitle  = htmlspecialchars($selectedCatName) . " Products";
$pageTitle .= " - PVC Security Systems";

/* ===================================================================
   RENDER HELPERS — shared by the full page and the AJAX partial
   =================================================================== */

function renderProductsGrid($viewMode, $brandCategoryTiles, $brandRow, $products, $totalRows, $catRow, $selectedCatName, $defaultImg) {
    ob_start();
    ?>
    <?php if (in_array($viewMode, ['tiles_all', 'tiles_brand'])): ?>
    <div class="products-grid tiles-view" id="productsGrid">
        <?php foreach ($brandCategoryTiles as $tile):
            $tileImg  = htmlspecialchars(db_image($tile['cimage'] ?? '', $defaultImg));
            $tileName = $tile['cname'];

            if ($viewMode === 'tiles_all') {
                $tileHref = 'all-categories.php?catname=' . urlencode($tileName);
            } else {
                $tileHref = 'all-categories.php?brand='
                    . urlencode($brandRow['brandid'])
                    . '&cat=' . urlencode($tile['cid']);
            }
        ?>
        <a href="<?php echo $tileHref; ?>"
           class="product-card js-cat-nav"
           style="text-decoration:none; color:inherit;"
           data-name="<?php echo htmlspecialchars($tileName); ?>"
           data-aos="fade-up">

            <div class="product-image">
                <img src="<?php echo $tileImg; ?>"
                     alt="<?php echo htmlspecialchars($tileName); ?>"
                     loading="lazy"
                     onerror="this.onerror=null; this.src='<?php echo $defaultImg; ?>';">
            </div>

            <div class="product-info">
                <p class="product-title">
                    <?php echo htmlspecialchars($tileName); ?>
                </p>
            </div>

        </a>
        <?php endforeach; ?>
    </div>

    <?php else: ?>
    <div class="products-grid" id="productsGrid">
        <?php if ($totalRows > 0): ?>
            <?php foreach ($products as $product):
                $imgSrc   = htmlspecialchars(db_image($product['pimage'] ?? '', $defaultImg));
                $priceVal = isset($product['price']) ? $product['price'] : 0;
            ?>
            <div class="product-card"
                 data-name="<?php echo htmlspecialchars($product['pname']); ?>"
                 data-category="<?php echo htmlspecialchars($product['cat_display'] ?? ''); ?>"
                 data-product-id="<?php echo htmlspecialchars($product['pid']); ?>"
                 data-model="<?php echo htmlspecialchars($product['pid']); ?>"
                 data-price="<?php echo htmlspecialchars($priceVal); ?>"
                 data-image="<?php echo $imgSrc; ?>"
                 data-aos="fade-up">

                <div class="product-image">
                    <img src="<?php echo $imgSrc; ?>"
                         alt="<?php echo htmlspecialchars($product['pname']); ?>"
                         loading="lazy"
                         onerror="this.onerror=null; this.src='<?php echo $defaultImg; ?>';">
                </div>

                <div class="product-info">
                    <p class="product-title">
                        <?php echo htmlspecialchars($product['pname']); ?>
                    </p>

                    <?php if (is_product_out_of_stock($product)): ?>
                        <span class="product-status-badge out-of-stock">OUT OF STOCK</span>
                    <?php else: ?>
                        <span class="product-status-badge in-stock">IN-STOCK</span>
                    <?php endif; ?>

                    <?php if (!empty($product['pdescription'])): ?>
                        <p class="product-description">
                            <?php echo htmlspecialchars($product['pdescription']); ?>
                        </p>
                    <?php endif; ?>
                </div>

                <div class="product-card-controls">
                    <?php if (is_product_out_of_stock($product)): ?>
                        <button class="btn out-of-stock-label" disabled>Out of Stock</button>
                    <?php else: ?>
                        <button class="card-cart-add"
                                data-id="<?php echo htmlspecialchars($product['pid']); ?>"
                                aria-label="Add to cart">
                            Add to Cart
                        </button>

                        <div class="card-qty"
                             data-id="<?php echo htmlspecialchars($product['pid']); ?>"
                             style="display:none;">
                            <button class="qty-decrease"><i class="fa-solid fa-minus"></i></button>
                            <span class="qty-value">1</span>
                            <button class="qty-increase"><i class="fa-solid fa-plus"></i></button>
                        </div>
                    <?php endif; ?>
                </div>

            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="no-products-found">
                <i class="fas fa-inbox"></i>
                <h3>No Products Found</h3>
                <p>
                    <?php
                    if ($selectedCatName)
                        echo 'No products found under "' . htmlspecialchars($selectedCatName) . '".';
                    elseif ($catRow)
                        echo 'No products in "' . htmlspecialchars($catRow['cname']) . '".';
                    else
                        echo 'No products available.';
                    ?>
                </p>
            </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>
    <?php
    return ob_get_clean();
}

function renderBackButton($viewMode, $brandRow, $catRow) {
    ob_start();
    if ($viewMode === 'products' && $brandRow && $catRow):
    ?>
    <div style="margin-bottom:16px;">
        <a href="all-categories.php?brand=<?php echo urlencode($brandRow['brandid']); ?>"
           class="clear-filters-btn js-cat-nav"
           style="display:inline-flex; width:auto; text-decoration:none;">
            <i class="fa-solid fa-arrow-left"></i>
            Back to <?php echo htmlspecialchars($brandRow['brandname']); ?> Categories
        </a>
    </div>
    <?php elseif ($viewMode === 'products_named'):
        $backUrl = $brandRow
            ? 'all-categories.php?brand=' . urlencode($brandRow['brandid'])
            : 'all-categories.php';
    ?>
    <div style="margin-bottom:16px;">
        <a href="<?php echo $backUrl; ?>"
           class="clear-filters-btn js-cat-nav"
           style="display:inline-flex; width:auto; text-decoration:none;">
            <i class="fa-solid fa-arrow-left"></i>
            Back to <?php echo $brandRow ? htmlspecialchars($brandRow['brandname']) . ' Categories' : 'All Categories'; ?>
        </a>
    </div>
    <?php endif;
    return ob_get_clean();
}

function renderBreadcrumb($brandRow, $catRow, $selectedCatName) {
    ob_start();
    ?>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
        <?php if ($brandRow): ?>
            <li class="breadcrumb-item">
                <a href="all-categories.php?brand=<?php echo urlencode($brandRow['brandid']); ?>" class="js-cat-nav">
                    <?php echo htmlspecialchars($brandRow['brandname']); ?>
                </a>
            </li>
            <?php if ($catRow): ?>
                <li class="breadcrumb-item active" aria-current="page">
                    <?php echo htmlspecialchars($catRow['cname']); ?>
                </li>
            <?php elseif ($selectedCatName): ?>
                <li class="breadcrumb-item active" aria-current="page">
                    <?php echo htmlspecialchars($selectedCatName); ?>
                </li>
            <?php endif; ?>
        <?php elseif ($selectedCatName): ?>
            <li class="breadcrumb-item">
                <a href="all-categories.php" class="js-cat-nav">All Categories</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                <?php echo htmlspecialchars($selectedCatName); ?>
            </li>
        <?php else: ?>
            <li class="breadcrumb-item active" aria-current="page">All Products</li>
        <?php endif; ?>
    </ol>
    <?php
    return ob_get_clean();
}

function renderResultsCount($viewMode, $brandCategoryTiles, $totalRows, $selectedCatName) {
    if (in_array($viewMode, ['tiles_all', 'tiles_brand'])) {
        $n = count($brandCategoryTiles);
        return '<span>' . $n . '</span> Categor' . ($n !== 1 ? 'ies' : 'y') . ' Found';
    }
    $html = '<span>' . $totalRows . '</span> Product' . ($totalRows !== 1 ? 's' : '') . ' Found';
    if ($selectedCatName) {
        $html .= ' <small style="color:#8a8a8a; font-size:12px; margin-left:6px;">— across all brands</small>';
    }
    return $html;
}

/* ===================================================================
   AJAX BRANCH — returns JSON only, no header/footer, no full page
   =================================================================== */

if (isset($_GET['ajax']) && $_GET['ajax'] == '1') {
    header('Content-Type: application/json');
    echo json_encode([
        'gridHtml'         => renderProductsGrid($viewMode, $brandCategoryTiles, $brandRow, $products, $totalRows, $catRow, $selectedCatName, $defaultImg),
        'backButtonHtml'   => renderBackButton($viewMode, $brandRow, $catRow),
        'breadcrumbHtml'   => renderBreadcrumb($brandRow, $catRow, $selectedCatName),
        'resultsCountHtml' => renderResultsCount($viewMode, $brandCategoryTiles, $totalRows, $selectedCatName),
        'showSort'         => in_array($viewMode, ['products', 'products_named']),
        'pageTitle'        => $pageTitle,
        'activeCatName'    => $selectedCatName,
    ]);
    exit;
}

include 'header.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <meta name="description"
        content="Explore PVC Security's extensive catalog of high-performance CCTV cameras, NVRs, DVRs, and access control systems from industry leaders like Hikvision and Dahua.">

    <link rel="shortcut icon" href="assets/img/logo/logo1.png" type="image/x-icon">
    <link rel="stylesheet" href="assets/css/plugins/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/plugins/fontawesome.css">
    <link rel="stylesheet" href="assets/css/plugins/aos.css">
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="assets/css/header_styles.css">
    <link rel="stylesheet" href="assets/css/products_styles.css">
    <link rel="stylesheet" href="assets/css/all-products.css">
    <link rel="stylesheet" href="assets/css/cart_styles.css">
    <link rel="stylesheet" href="assets/css/card-fix.css">
    <script src="assets/js/plugins/jquery-3-6-0.min.js"></script>
</head>

<body data-active-catname="<?php echo htmlspecialchars($selectedCatName); ?>">

<!-- Breadcrumb -->
<section class="all-products-breadcrumb">
    <div class="container">
        <div class="breadcrumb-content">
            <nav aria-label="breadcrumb" id="breadcrumbNav">
                <?php echo renderBreadcrumb($brandRow, $catRow, $selectedCatName); ?>
            </nav>
        </div>
    </div>
</section>

<!-- Main Products Section -->
<section class="all-products-section">
    <div class="container-fluid">
        <div class="row">

            <!-- Left Sidebar -->
            <div class="col-lg-3 col-md-4">
                <div class="product-sidebar-wrapper">

                    <button class="mobile-filter-toggle d-md-none" id="mobileFilterToggle">
                        <i class="fa-solid fa-filter"></i> Filters
                    </button>

                    <div class="product-sidebar" id="productSidebar">

                        <div class="filter-widget">
                            <h4>Shop by Category</h4>
                            <div class="filter-options" id="categoryFilters">

                                <label class="filter-checkbox-label <?php echo empty($selectedCatName) ? 'is-checked' : ''; ?>"
                                       data-url="all-categories.php">
                                    <input type="checkbox" name="catname" value=""
                                           <?php echo empty($selectedCatName) ? 'checked' : ''; ?> readonly>
                                    <span class="checkmark"></span>
                                    ALL CATEGORIES
                                </label>

                                <?php foreach ($sidebarCats as $sc):
                                    $isActive = (strtoupper(trim($selectedCatName)) === strtoupper(trim($sc['cname'])));
                                ?>
                                <label class="filter-checkbox-label <?php echo $isActive ? 'is-checked' : ''; ?>"
                                       data-url="all-categories.php?catname=<?php echo urlencode($sc['cname']); ?>">
                                    <input type="checkbox" name="catname"
                                           value="<?php echo htmlspecialchars($sc['cname']); ?>"
                                           <?php echo $isActive ? 'checked' : ''; ?> readonly>
                                    <span class="checkmark"></span>
                                    <?php echo htmlspecialchars(strtoupper($sc['cname'])); ?>
                                </label>
                                <?php endforeach; ?>

                            </div>
                        </div>

                        <button class="clear-filters-btn" id="clearFilters">
                            <i class="fa-solid fa-xmark"></i> Clear All Filters
                        </button>

                    </div>
                </div>
            </div>

            <!-- Right Content -->
            <div class="col-lg-9 col-md-8">
                <div class="products-content-area">

                    <!-- Top Bar -->
                    <div class="products-top-bar">
                        <div class="results-count" id="resultsCount">
                            <?php echo renderResultsCount($viewMode, $brandCategoryTiles, $totalRows, $selectedCatName); ?>
                        </div>
                        <div class="sort-wrapper" id="sortWrapper"
                             style="<?php echo in_array($viewMode, ['products', 'products_named']) ? '' : 'display:none;'; ?>">
                            <label>Sort By</label>
                            <select id="sortSelect" class="sort-select">
                                <option value="default">Default</option>
                                <option value="name-asc">Name: A to Z</option>
                                <option value="name-desc">Name: Z to A</option>
                            </select>
                        </div>
                    </div>

                    <!-- Back button -->
                    <div id="backButtonWrap"><?php echo renderBackButton($viewMode, $brandRow, $catRow); ?></div>

                    <!-- Grid (this is the only part AJAX swaps) -->
                    <div id="productsGridWrap">
                        <?php echo renderProductsGrid($viewMode, $brandCategoryTiles, $brandRow, $products, $totalRows, $catRow, $selectedCatName, $defaultImg); ?>
                    </div>

                </div>
            </div><!-- end right col -->

        </div>
    </div>
</section>

<script src="assets/js/plugins/bootstrap.min.js"></script>
<script src="assets/js/plugins/aos.js"></script>
<script src="assets/js/plugins/fontawesome.js"></script>
<script src="assets/js/main.js"></script>
<script src="assets/js/cart.js"></script>
<script src="assets/js/global_footer.js"></script>

<script>
/* ===================================================================
   AJAX category/product loading — replaces window.location.href reload
   =================================================================== */

async function loadCategoryView(url, pushState = true) {
    if (!url) url = 'all-categories.php';

    const gridWrap = document.getElementById('productsGridWrap');
    gridWrap.classList.add('is-loading');

    let data;
    try {
        const sep = url.includes('?') ? '&' : '?';
        const res = await fetch(url + sep + 'ajax=1');
        data = await res.json();
    } catch (err) {
        console.error('Failed to load categories:', err);
        gridWrap.classList.remove('is-loading');
        return;
    }

    document.getElementById('productsGridWrap').innerHTML = data.gridHtml;
    document.getElementById('resultsCount').innerHTML     = data.resultsCountHtml;
    document.getElementById('backButtonWrap').innerHTML   = data.backButtonHtml;
    document.getElementById('breadcrumbNav').innerHTML    = data.breadcrumbHtml;
    document.getElementById('sortWrapper').style.display  = data.showSort ? '' : 'none';
    document.title = data.pageTitle;
    document.body.dataset.activeCatname = data.activeCatName || '';

    if (pushState) {
        history.pushState({ ajaxUrl: url }, '', url);
    }

    gridWrap.classList.remove('is-loading');
    syncCategoryFilterState();
    rebindCatNavLinks();
    if (typeof AOS !== 'undefined') AOS.refreshHard();
}

function rebindCatNavLinks() {
    document.querySelectorAll('.js-cat-nav').forEach(function(link) {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            loadCategoryView(this.getAttribute('href'));
        });
    });
}

document.querySelectorAll('#categoryFilters .filter-checkbox-label').forEach(function(label) {
    label.addEventListener('click', function(e) {
        e.preventDefault();
        loadCategoryView(this.getAttribute('data-url'));
    });
});

window.addEventListener('popstate', function() {
    loadCategoryView(window.location.href.replace(window.location.origin, ''), false);
});

function syncCategoryFilterState() {
    const activeCatName = (new URLSearchParams(window.location.search).get('catname') || document.body.dataset.activeCatname || '').toUpperCase().trim();
    const filterContainer = document.getElementById('categoryFilters');
    let activeLabel = null;

    document.querySelectorAll('#categoryFilters .filter-checkbox-label').forEach(function(label) {
        const input = label.querySelector('input[type="checkbox"]');
        if (!input) return;

        const isActive = input.value.toUpperCase().trim() === activeCatName;
        input.checked = isActive;
        label.classList.toggle('is-checked', isActive);

        if (isActive) activeLabel = label;
    });

    if (filterContainer && activeLabel) {
        requestAnimationFrame(function() {
            const containerHeight = filterContainer.clientHeight;
            const labelTop    = activeLabel.offsetTop;
            const labelHeight = activeLabel.offsetHeight;
            const targetScrollTop = labelTop - (containerHeight / 2) + (labelHeight / 2);
            filterContainer.scrollTop = Math.max(0, targetScrollTop);
        });
    }
}

syncCategoryFilterState();
rebindCatNavLinks();

document.getElementById('clearFilters').addEventListener('click', function(e) {
    e.preventDefault();
    loadCategoryView('all-categories.php');
});

const mobileToggle = document.getElementById('mobileFilterToggle');
const sidebar      = document.getElementById('productSidebar');
if (mobileToggle && sidebar) {
    mobileToggle.addEventListener('click', function() { sidebar.classList.toggle('active'); });
}

document.addEventListener('change', function(e) {
    if (e.target.id !== 'sortSelect') return;
    const productsGrid = document.getElementById('productsGrid');
    if (!productsGrid) return;
    const cards = Array.from(productsGrid.querySelectorAll('.product-card'));
    cards.sort((a, b) => {
        if (e.target.value === 'name-asc')  return a.dataset.name.localeCompare(b.dataset.name);
        if (e.target.value === 'name-desc') return b.dataset.name.localeCompare(a.dataset.name);
        return 0;
    });
    productsGrid.innerHTML = '';
    cards.forEach(c => productsGrid.appendChild(c));
});

document.addEventListener('click', function(e) {
    if (e.target.closest('.card-cart-add')) {
        const btn     = e.target.closest('.card-cart-add');
        const card    = btn.closest('.product-card');
        const qtyWrap = card.querySelector('.card-qty');
        addToCart({
            name  : card.dataset.name  || '',
            model : card.dataset.model || '',
            price : parseFloat(card.dataset.price) || 0,
            image : card.dataset.image || ''
        });
        btn.style.display     = 'none';
        qtyWrap.style.cssText = 'display:flex !important; align-items:center; justify-content:center; gap:8px; padding:6px 8px; border-radius:999px; border:1.5px solid #f5f5f5; background:#fff;';
        qtyWrap.querySelector('.qty-value').textContent = '1';
        return;
    }
    if (e.target.closest('.qty-decrease')) {
        const qtyWrap = e.target.closest('.qty-decrease').closest('.card-qty');
        const card    = qtyWrap.closest('.product-card');
        const span    = qtyWrap.querySelector('.qty-value');
        let   qty     = parseInt(span.textContent, 10);
        if (qty > 1) {
            qty--;
            span.textContent = qty;
            updateCartItemQuantity(card.dataset.model, qty);
        } else {
            removeFromCart(card.dataset.model);
            qtyWrap.style.cssText   = 'display:none;';
            card.querySelector('.card-cart-add').style.display = '';
            span.textContent        = '1';
        }
        return;
    }
    if (e.target.closest('.qty-increase')) {
        const qtyWrap = e.target.closest('.qty-increase').closest('.card-qty');
        const card    = qtyWrap.closest('.product-card');
        const span    = qtyWrap.querySelector('.qty-value');
        const qty     = parseInt(span.textContent, 10) + 1;
        span.textContent = qty;
        updateCartItemQuantity(card.dataset.model, qty);
        return;
    }
});

if (typeof AOS !== 'undefined') AOS.init({ duration: 800, once: true });
</script>

<style>
/* =====================================================================
   card-fix.css content — unchanged from your version, plus a tiny
   loading-state rule so the AJAX swap dims instead of collapsing.
   ===================================================================== */

.products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
    gap: 16px;
}

#productsGridWrap.is-loading {
    opacity: 0.45;
    pointer-events: none;
    transition: opacity 0.15s ease;
}

.products-grid .product-card {
    background: #ffffff;
    border: 1px solid #ececec;
    border-radius: 8px;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    align-items: stretch;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
    transition: box-shadow 0.2s ease, transform 0.2s ease;
    height: 100%;
}

.products-grid .product-card:hover,
.products-grid .product-card:hover .product-image,
.products-grid .product-card:hover .product-info {
    background: #ffffff !important;
}

.products-grid .product-card:hover {
    box-shadow: 0 6px 18px rgba(0, 0, 0, 0.10);
    transform: translateY(-2px);
}

.products-grid .product-image {
    background: #ffffff;
    width: 100%;
    aspect-ratio: 3 / 4;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 14px;
    box-sizing: border-box;
    border-bottom: 1px solid #f2f2f2;
}

.products-grid .product-image img {
    max-width: 100%;
    max-height: 100%;
    width: auto;
    height: auto;
    object-fit: contain;
    mix-blend-mode: normal;
}

.products-grid .product-info {
    background: #ffffff;
    padding: 10px 12px 8px;
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    text-align: left;
    gap: 5px;
}

.products-grid .product-title {
    color: #1a1a1a !important;
    font-size: 13.5px;
    font-weight: 500;
    line-height: 1.35;
    margin: 0;
    text-align: left;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
}

.products-grid .product-description {
    color: #878787 !important;
    font-size: 12px;
    line-height: 1.4;
    margin: 0;
    text-align: left;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
}

.products-grid .product-status-badge {
    display: inline-block;
    width: fit-content;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.02em;
    padding: 3px 10px;
    border-radius: 999px;
}

.products-grid .product-status-badge.in-stock {
    color: #157347 !important;
    background: #e6f6ec;
}

.products-grid .product-status-badge.out-of-stock {
    color: #b02a37 !important;
    background: #fbe7e9;
}

.products-grid .out-of-stock-card {
    opacity: 0.75;
}

.products-grid .product-card-controls {
    margin-top: auto;
}

.products-grid.tiles-view .product-card {
    text-align: center;
}

.products-grid.tiles-view .product-image {
    aspect-ratio: 4 / 3;
}

.products-grid.tiles-view .product-info {
    align-items: center;
    text-align: center;
    padding: 14px 12px 16px;
}

.products-grid.tiles-view .product-title {
    -webkit-line-clamp: 2;
    text-align: center;
}

.no-products-found {
    color: #4a4a4a !important;
    text-align: center;
    padding: 60px 20px;
}

.no-products-found h3 {
    color: #1a1a1a !important;
}
</style>

</body>
</html>