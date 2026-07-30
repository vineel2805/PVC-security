<?php


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
   LIVE SEARCH — separate from the category ajax branch below.
   Searches products by name, and category names, returns JSON.
   =================================================================== */
if (isset($_GET['live_search'])) {
    header('Content-Type: application/json');

    $term = trim($_GET['live_search'] ?? '');
    if ($term === '' || mb_strlen($term) < 2) {
        echo json_encode(['products' => [], 'categories' => []]);
        exit;
    }

    $termEscaped = mysqli_real_escape_string($con, $term);
    $like        = "%{$termEscaped}%";
    $likeEscaped = mysqli_real_escape_string($con, $like);

    // Matching products
    $products = [];
    $pRes = mysqli_query($con, "
        SELECT p.pid, p.pname, p.pimage, b.brandname
        FROM   products p
        JOIN   brands   b ON b.brandid = p.brandid
        WHERE  p.pname LIKE '$likeEscaped'
          AND  p.display_status = 1
          AND  b.status = 'Active'
          AND  b.display_status = 1
        ORDER BY p.pname ASC
        LIMIT 6
    ");
    if ($pRes) {
        while ($row = mysqli_fetch_assoc($pRes)) {
            $products[] = [
                'id'    => $row['pid'],
                'name'  => $row['pname'],
                'brand' => $row['brandname'],
                'image' => db_image($row['pimage'] ?? '', $defaultImg),
                'url'   => 'product-details.php?id=' . urlencode($row['pid']),
            ];
        }
    }

    // Matching categories
    $categories = [];
    $cRes = mysqli_query($con, "
        SELECT DISTINCT TRIM(cat.cname) AS cname
        FROM   category cat
        WHERE  cat.cname LIKE '$likeEscaped'
          AND  cat.display_status = 1
          AND  EXISTS (
                SELECT 1 FROM products p
                WHERE p.pcat = cat.cid AND p.display_status = 1
              )
        ORDER BY cat.cname ASC
        LIMIT 5
    ");
    if ($cRes) {
        while ($row = mysqli_fetch_assoc($cRes)) {
            $categories[] = [
                'name' => $row['cname'],
                'url'  => 'all-categories.php?catname=' . urlencode($row['cname']),
            ];
        }
    }

    echo json_encode(['products' => $products, 'categories' => $categories]);
    exit;
}

/* ===================================================================
   DATA LOADING — identical logic to your original file, minus the
   status = 'Active' filters (see note above)
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
            WHERE p.pcat = cat.cid AND p.display_status = 1
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
                WHERE p.pcat = category.cid AND p.display_status = 1
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
                WHERE p.pcat = c.cid AND p.display_status = 1
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
                $imgSrc       = htmlspecialchars(db_image($product['pimage'] ?? '', $defaultImg));
                $priceVal     = isset($product['price']) ? $product['price'] : 0;
                $isOutOfStock = is_product_out_of_stock($product);
            ?>
            <div class="product-card<?php echo $isOutOfStock ? ' out-of-stock-card' : ''; ?>"
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

                    <?php if ($isOutOfStock): ?>
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
                    <?php if ($isOutOfStock): ?>
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
        'showSort'         => true,
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

                    <div class="product-sidebar" id="productSidebar">
                        <div class="product-sidebar-header d-md-none">
                            <h4>Filters</h4>
                            <button class="sidebar-close-btn" id="sidebarCloseBtn" aria-label="Close filters">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>

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
                    <div class="products-toolbar">

                        <!-- Category Page Search -->
                        <div class="cat-search-wrap">
                            <div class="cat-search-box" id="catSearchBox">
                                <i class="fa-solid fa-magnifying-glass cat-search-icon"></i>
                                <input type="text"
                                       id="catSearchInput"
                                       class="cat-search-input"
                                       placeholder="Search products or categories..."
                                       autocomplete="off">
                                <button type="button" class="cat-search-clear" id="catSearchClear" style="display:none;">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>
                            </div>
                            <div class="cat-search-results" id="catSearchResults"></div>
                        </div>

                        <!-- Filter / Results / Sort row -->
                        <div class="toolbar-controls-row">

                            <button class="mobile-filter-toggle d-md-none" id="mobileFilterToggle">
                                <i class="fa-solid fa-sliders"></i>
                                <span>Filter</span>
                            </button>

                            <div class="results-count" id="resultsCount">
                                <?php echo renderResultsCount($viewMode, $brandCategoryTiles, $totalRows, $selectedCatName); ?>
                            </div>

                            <div class="sort-wrapper" id="sortWrapper">
                                <i class="fa-solid fa-arrow-up-short-wide sort-icon"></i>
                                <label class="d-none d-md-inline">Sort By</label>
                                <select id="sortSelect" class="sort-select">
                                    <option value="default">Default</option>
                                    <option value="name-asc">Name: A to Z</option>
                                    <option value="name-desc">Name: Z to A</option>
                                </select>
                                <i class="fa-solid fa-chevron-down sort-chevron d-md-none"></i>
                            </div>

                        </div>
                    </div>



                    <!-- Mobile sidebar overlay backdrop -->
                    <div class="sidebar-backdrop d-md-none" id="sidebarBackdrop"></div>

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

const mobileToggle  = document.getElementById('mobileFilterToggle');
const sidebar       = document.getElementById('productSidebar');
const sidebarClose  = document.getElementById('sidebarCloseBtn');
const sidebarBackdrop = document.getElementById('sidebarBackdrop');

function openMobileSidebar() {
    if (!sidebar) return;
    sidebar.classList.add('active');
    if (sidebarBackdrop) sidebarBackdrop.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeMobileSidebar() {
    if (!sidebar) return;
    sidebar.classList.remove('active');
    if (sidebarBackdrop) sidebarBackdrop.classList.remove('active');
    document.body.style.overflow = '';
}

if (mobileToggle && sidebar) {
    mobileToggle.addEventListener('click', openMobileSidebar);
}
if (sidebarClose) {
    sidebarClose.addEventListener('click', closeMobileSidebar);
}
if (sidebarBackdrop) {
    sidebarBackdrop.addEventListener('click', closeMobileSidebar);
}
// Selecting a category filter on mobile should close the sidebar too
document.querySelectorAll('#categoryFilters .filter-checkbox-label').forEach(function(label) {
    label.addEventListener('click', closeMobileSidebar);
});

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

/* ===================================================================
   Category page live search
   =================================================================== */
(function() {
    const input     = document.getElementById('catSearchInput');
    const clearBtn  = document.getElementById('catSearchClear');
    const resultsEl = document.getElementById('catSearchResults');
    if (!input || !resultsEl) return;

    let debounceTimer = null;

    function closeResults() {
        resultsEl.classList.remove('is-open');
        resultsEl.innerHTML = '';
    }

    function escapeHtml(str) {
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    function renderResults(data) {
        const products   = data.products   || [];
        const categories = data.categories || [];

        if (!products.length && !categories.length) {
            resultsEl.innerHTML = '<div class="cat-search-no-results">No matches found.</div>';
            resultsEl.classList.add('is-open');
            return;
        }

        let html = '';

        if (categories.length) {
            html += '<div class="cat-search-section-label">Categories</div>';
            categories.forEach(function(c) {
                html += '' +
                    '<a href="' + c.url + '" class="cat-search-result-item cat-only js-cat-nav">' +
                        '<i class="fa-solid fa-layer-group"></i>' +
                        '<span class="cat-search-result-name">' + escapeHtml(c.name) + '</span>' +
                    '</a>';
            });
        }

        if (products.length) {
            html += '<div class="cat-search-section-label">Products</div>';
            products.forEach(function(p) {
                html += '' +
                    '<a href="' + p.url + '" class="cat-search-result-item">' +
                        '<img src="' + p.image + '" alt="' + escapeHtml(p.name) + '" loading="lazy">' +
                        '<span>' +
                            '<span class="cat-search-result-name">' + escapeHtml(p.name) + '</span><br>' +
                            '<span class="cat-search-result-brand">' + escapeHtml(p.brand) + '</span>' +
                        '</span>' +
                    '</a>';
            });
        }

        resultsEl.innerHTML = html;
        resultsEl.classList.add('is-open');

        // Category results should use the AJAX in-page nav, not a full reload
        resultsEl.querySelectorAll('.js-cat-nav').forEach(function(link) {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                closeResults();
                input.value = '';
                clearBtn.style.display = 'none';
                loadCategoryView(this.getAttribute('href'));
            });
        });
    }

    input.addEventListener('input', function() {
        const term = this.value.trim();
        clearBtn.style.display = term ? '' : 'none';

        clearTimeout(debounceTimer);
        if (term.length < 2) {
            closeResults();
            return;
        }

        debounceTimer = setTimeout(async function() {
            try {
                const res  = await fetch('all-categories.php?live_search=' + encodeURIComponent(term));
                const data = await res.json();
                renderResults(data);
            } catch (err) {
                console.error('Search failed:', err);
            }
        }, 250);
    });

    clearBtn.addEventListener('click', function() {
        input.value = '';
        clearBtn.style.display = 'none';
        closeResults();
        input.focus();
    });

    document.addEventListener('click', function(e) {
        if (!e.target.closest('.cat-search-wrap')) closeResults();
    });
})();
</script>

<style>
/* =====================================================================
   card-fix.css content — unchanged from your version, plus a tiny
   loading-state rule so the AJAX swap dims instead of collapsing.
   ===================================================================== */

/* Breathing room between the site header/nav and the page content.
   Mobile is left untouched — this is a desktop-only spacing fix. */
@media (min-width: 768px) {
    .all-products-breadcrumb {
        margin-top: 28px;
    }

    .all-products-section {
        margin-top: 32px;
    }
}

.products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
    gap: 16px;
}

@media (max-width: 767px) {
    .products-grid {
        grid-template-columns: repeat(2, 1fr) !important;
        gap: 10px;
    }
}

@media (max-width: 380px) {
    .products-grid {
        gap: 8px;
    }
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

/* =====================================================================
   Category page live search — light theme suited to sidebar/products,
   now with a pill-shaped bar, glow-on-focus, animated icon/clear
   button, and a fade/slide-in results dropdown.
   ===================================================================== */

/* ---------------------------------------------------------------
   Toolbar shell.
   Mobile:  search full-width row, then Filter+Sort pills below.
   Desktop: search + results-count + sort all sit in one inline row.
   --------------------------------------------------------------- */
.products-toolbar {
    display: flex;
    flex-direction: column;
    gap: 12px;
    margin-bottom: 20px;
}

.toolbar-controls-row {
    display: flex;
    align-items: stretch;
    justify-content: space-between;
    gap: 12px;
}

.results-count {
    display: flex;
    align-items: center;
    font-size: 13.5px;
    color: #6b6b6b;
    font-weight: 600;
    white-space: nowrap;
}

.results-count span {
    color: #1a1a1a;
    font-weight: 800;
    margin-right: 3px;
}

/* Desktop: plain "Filter" button hidden, sort sits as a clean dropdown */
.mobile-filter-toggle {
    display: none;
}

.sort-wrapper {
    position: relative;
    display: flex;
    align-items: center;
    gap: 9px;
    flex-shrink: 0;
    height: 44px;
    box-sizing: border-box;
    border: 1.5px solid #e6e6e6;
    border-radius: 10px;
    background: #ffffff;
    padding: 0 14px;
    transition: border-color 0.2s ease;
}

.sort-wrapper:focus-within {
    border-color: var(--pvc-gold-mid, #d4af37);
}

.sort-wrapper label {
    font-size: 13px;
    color: #6b6b6b;
    font-weight: 600;
    white-space: nowrap;
}

.sort-icon {
    color: var(--pvc-gold-mid, #d4af37);
    font-size: 13px;
    flex-shrink: 0;
}

.sort-chevron {
    display: none;
    color: #9a9a9a;
    font-size: 11px;
    flex-shrink: 0;
}

.sort-select {
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    border: none;
    outline: none;
    background: transparent;
    padding: 0;
    margin: 0;
    height: 100%;
    font-size: 13px;
    font-weight: 700;
    color: #1a1a1a;
    cursor: pointer;
}

.sidebar-backdrop {
    display: none;
}

.product-sidebar-header {
    display: none;
}

.sidebar-close-btn {
    border: none;
    background: none;
    font-size: 18px;
    color: #6b6b6b;
    cursor: pointer;
    padding: 4px 8px;
}

/* ---------------------------------------------------------------
   Mobile: filter + sort become equal-height, equal-width pill
   buttons in a row; sidebar becomes an off-canvas panel with a
   backdrop; search bar spans full width above the controls row.
   --------------------------------------------------------------- */
@media (max-width: 767px) {

    .toolbar-controls-row {
        gap: 10px;
    }

    .mobile-filter-toggle {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        flex: 1;
        height: 44px;
        box-sizing: border-box;
        border: 1.5px solid #e6e6e6;
        border-radius: 10px;
        padding: 0 12px;
        background: #ffffff;
        font-size: 13.5px;
        font-weight: 700;
        color: #1a1a1a;
        cursor: pointer;
        transition: border-color 0.15s ease, background 0.15s ease;
    }

    .mobile-filter-toggle:active {
        background: #faf7ef;
        border-color: var(--pvc-gold-mid, #d4af37);
    }

    .mobile-filter-toggle i {
        color: var(--pvc-gold-mid, #d4af37);
        font-size: 14px;
    }

    .results-count {
        display: none;
    }

    .sort-wrapper {
        flex: 1;
        justify-content: center;
    }

    .sort-wrapper:active {
        border-color: var(--pvc-gold-mid, #d4af37);
    }

    .sort-select {
        flex: 1;
        text-align: center;
        padding-right: 2px;
    }

    .sort-chevron {
        display: inline-block;
    }

    /* Off-canvas sidebar */
    .product-sidebar-wrapper {
        position: static;
    }

    .product-sidebar {
        position: fixed;
        top: 0;
        left: 0;
        bottom: 0;
        width: 82%;
        max-width: 320px;
        background: #ffffff;
        z-index: 999999;
        padding: 0 18px 18px;
        overflow-y: auto;
        transform: translateX(-100%);
        transition: transform 0.3s ease;
        box-shadow: 8px 0 24px rgba(0, 0, 0, 0.15);
    }

    .product-sidebar.active {
        transform: translateX(0);
    }

    .product-sidebar-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        position: sticky;
        top: 0;
        left: 0;
        right: 0;
        margin: 0 -18px 8px;
        padding: 18px 18px 14px;
        background: #ffffff;
        box-shadow: 0 1px 0 rgba(0, 0, 0, 0.06);
        z-index: 2;
    }

    .product-sidebar-header h4 {
        margin: 0;
        font-size: 16px;
        font-weight: 800;
        color: #1a1a1a;
    }

    .sidebar-backdrop {
        display: block;
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.45);
        z-index: 999998;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.25s ease, visibility 0.25s ease;
    }

    .sidebar-backdrop.active {
        opacity: 1;
        visibility: visible;
    }
}

.cat-search-wrap {
    position: relative;
    width: 100%;
    max-width: 420px;
}

/* Desktop: search sits inline on the same row as results-count/sort,
   instead of stacked as a full-width block above them. */
@media (min-width: 768px) {
    .products-toolbar {
        flex-direction: row;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
    }

    .cat-search-wrap {
        max-width: 320px;
        flex: 1 1 320px;
    }

    .toolbar-controls-row {
        flex: 0 0 auto;
        gap: 20px;
    }
}

@media (max-width: 767px) {
    .cat-search-wrap {
        max-width: 100%;
    }
}

.cat-search-box {
    display: flex;
    align-items: center;
    gap: 10px;
    height: 46px;
    box-sizing: border-box;
    background: #f6f6f6;
    border: 1.5px solid transparent;
    border-radius: 999px;
    padding: 0 16px;
    transition: border-color 0.25s ease, background 0.25s ease,
}

.cat-search-box:hover {
    background: #f1f1f1;
}

.cat-search-box:focus-within {
    background: #ffffff;
    border-color: var(--pvc-gold-mid, #d4af37);

    transform: translateY(-1px);
}

.cat-search-icon {
    color: #9a9a9a;
    font-size: 14px;
    flex-shrink: 0;
    transition: color 0.25s ease, transform 0.25s ease;
}

.cat-search-box:focus-within .cat-search-icon {
    color: var(--pvc-gold-mid, #d4af37);
    transform: scale(1.1);
}

.cat-search-input {
    flex: 1;
    border: none;
    outline: none;
    font-size: 14px;
    color: #1a1a1a;
    background: transparent;
    height: 100%;
}

.cat-search-input::placeholder {
    color: #a3a3a3;
    transition: color 0.25s ease;
}

.cat-search-box:focus-within .cat-search-input::placeholder {
    color: #c2c2c2;
}

.cat-search-clear {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 22px;
    height: 22px;
    border: none;
    border-radius: 50%;
    background: #e9e9e9;
    color: #7a7a7a;
    cursor: pointer;
    font-size: 11px;
    flex-shrink: 0;
    transition: background 0.2s ease, color 0.2s ease, transform 0.2s ease;
}

.cat-search-clear:hover {
    background: var(--pvc-gold-mid, #d4af37);
    color: #ffffff;
    transform: rotate(90deg);
}

.cat-search-results {
    display: block;
    position: absolute;
    top: calc(100% + 8px);
    left: 0;
    right: 0;
    background: #fff;
    border: 1px solid #ececec;
    border-radius: 12px;
    box-shadow: 0 12px 30px rgba(0, 0, 0, 0.12);
    max-height: 360px;
    overflow-y: auto;
    z-index: 500;
    opacity: 0;
    visibility: hidden;
    transform: translateY(-6px);
    transition: opacity 0.2s ease, transform 0.2s ease, visibility 0.2s ease;
}

.cat-search-results.is-open {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}

.cat-search-section-label {
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: #9a9a9a;
    padding: 10px 14px 4px;
}

.cat-search-result-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 8px 14px;
    text-decoration: none;
    color: #1a1a1a;
    cursor: pointer;
    transition: background 0.15s ease, padding-left 0.15s ease;
}

.cat-search-result-item:hover {
    background: #faf7ef;
    padding-left: 18px;
}

.cat-search-result-item img {
    width: 34px;
    height: 34px;
    object-fit: contain;
    border: 1px solid #f0f0f0;
    border-radius: 6px;
    flex-shrink: 0;
    background: #fff;
}

.cat-search-result-name {
    font-size: 13.5px;
    font-weight: 500;
    line-height: 1.3;
}

.cat-search-result-brand {
    font-size: 11.5px;
    color: #9a9a9a;
}

.cat-search-result-item.cat-only i {
    color: var(--pvc-gold-mid, #d4af37);
    width: 34px;
    text-align: center;
    font-size: 15px;
}

.cat-search-no-results {
    padding: 16px 14px;
    font-size: 13px;
    color: #9a9a9a;
    text-align: center;
}

@media (max-width: 767px) {
    .cat-search-results {
        left: 0;
        right: 0;
    }
}
</style>

</body>
</html>