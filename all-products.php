<?php
/* ===================================================================
   shop by brands page
   =================================================================== */
ini_set('display_errors', 0);
ini_set('log_errors', 1);
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

function normalizeCategoryNameForDedup($name) {
    $name = preg_replace('/\s+/u', ' ', trim((string)$name));
    return mb_strtolower($name, 'UTF-8');
}

/* ===================================================================
   DATA LOADING
   =================================================================== */
$selectedSearch    = isset($_GET['q'])       ? trim($_GET['q'])       : '';
$selectedBrand     = isset($_GET['brand'])   ? trim($_GET['brand'])   : '';
$selectedCat       = isset($_GET['cat'])     ? trim($_GET['cat'])     : '';
$selectedProductId = isset($_GET['pid'])     ? trim($_GET['pid'])     : '';
$selectedCatName   = isset($_GET['catname']) ? trim($_GET['catname']) : '';

$selectedSearchEscaped    = mysqli_real_escape_string($con, $selectedSearch);
$selectedBrandEscaped     = mysqli_real_escape_string($con, $selectedBrand);
$selectedCatEscaped       = mysqli_real_escape_string($con, $selectedCat);
$selectedProductIdEscaped = mysqli_real_escape_string($con, $selectedProductId);
$selectedCatNameEscaped   = mysqli_real_escape_string($con, $selectedCatName);

$brands    = [];
$brandsRes = mysqli_query($con, "
    SELECT brandid, brandname, imagelink
    FROM brands
    WHERE display_status = 1
    ORDER BY brandname ASC
");
if ($brandsRes) {
    while ($row = mysqli_fetch_assoc($brandsRes)) {
        $brands[] = $row;
    }
}

$brandRow = null;
if (!empty($selectedBrandEscaped)) {
    $bl = mysqli_query($con, "
        SELECT * FROM brands
        WHERE brandid = '$selectedBrandEscaped'
          AND display_status = 1
    ");
    if ($bl && mysqli_num_rows($bl) > 0) {
        $brandRow = mysqli_fetch_assoc($bl);
    }
}

$catRow = null;
if ($brandRow && !empty($selectedCatEscaped)) {
    $cl = mysqli_query($con, "
        SELECT * FROM category
        WHERE cid     = '$selectedCatEscaped'
          AND brandid = '{$brandRow['brandid']}'
          AND display_status = 1
    ");
    if ($cl && mysqli_num_rows($cl) > 0) {
        $catRow = mysqli_fetch_assoc($cl);
    }
}

if (!empty($selectedProductIdEscaped) && !$brandRow) {
    $pidLookup = mysqli_query($con, "
        SELECT p.brandid, b.brandname
        FROM products p
        JOIN brands b ON b.brandid = p.brandid
        WHERE p.pid = '$selectedProductIdEscaped'
          AND p.display_status = 1
          AND b.display_status = 1
        LIMIT 1
    ");
    if ($pidLookup && mysqli_num_rows($pidLookup) > 0) {
        $pidRow = mysqli_fetch_assoc($pidLookup);
        $brandRow = [
            'brandid'   => $pidRow['brandid'],
            'brandname' => $pidRow['brandname'],
        ];
    }
}

if (empty($selectedBrandEscaped) && empty($selectedCatEscaped) && !empty($selectedSearchEscaped)) {
    $searchBrands = mysqli_query($con, "
        SELECT
            p.brandid,
            b.brandname,
            COUNT(*) AS result_count
        FROM products p
        JOIN brands b ON b.brandid = p.brandid
        JOIN category c ON c.cid = p.pcat
        WHERE p.display_status = 1
          AND b.display_status = 1
          AND c.display_status = 1
          AND (
                LOWER(p.pname)        LIKE '%$selectedSearchEscaped%'
             OR LOWER(p.pdescription) LIKE '%$selectedSearchEscaped%'
             OR LOWER(p.pid)          LIKE '%$selectedSearchEscaped%'
             OR LOWER(b.brandname)    LIKE '%$selectedSearchEscaped%'
             OR LOWER(c.cname)        LIKE '%$selectedSearchEscaped%'
          )
        GROUP BY p.brandid, b.brandname
        ORDER BY result_count DESC, b.brandname ASC
    ");
    if ($searchBrands && mysqli_num_rows($searchBrands) === 1) {
        $searchBrandRow = mysqli_fetch_assoc($searchBrands);
        $brandRow = [
            'brandid'   => $searchBrandRow['brandid'],
            'brandname' => $searchBrandRow['brandname'],
        ];
    }
}

$resolvedBrandId = $selectedBrand;
if (empty($resolvedBrandId) && isset($brandRow['brandid'])) {
    $resolvedBrandId = (string)$brandRow['brandid'];
}
$selectedBrand = $resolvedBrandId;

$viewMode           = 'products';
$brandCategoryTiles = [];
$namedCatIds        = [];

if (!empty($selectedSearchEscaped)) {
    $viewMode = 'products';
} elseif (!empty($selectedCatNameEscaped)) {
    // CROSS-BRAND CATEGORY VIEW: a merged "All Brands" tile was clicked.
    $viewMode = 'products';
    $brandClause = $brandRow ? "AND c.brandid = '{$brandRow['brandid']}'" : '';
    $cidRes = mysqli_query($con, "
        SELECT c.cid
        FROM   category c
        JOIN   brands   b ON b.brandid = c.brandid
        WHERE  TRIM(REPLACE(REPLACE(REPLACE(UPPER(c.cname), '  ', ' '), '  ', ' '), '  ', ' '))
               = TRIM(REPLACE(REPLACE(REPLACE(UPPER('$selectedCatNameEscaped'), '  ', ' '), '  ', ' '), '  ', ' '))
          AND  c.display_status = 1
          AND  b.display_status = 1
          $brandClause
    ");
    if ($cidRes) {
        while ($r = mysqli_fetch_assoc($cidRes)) {
            $namedCatIds[] = $r['cid'];
        }
    }
} elseif (empty($selectedProductIdEscaped) && !$brandRow) {
    // Default view: Show brand banners
    $viewMode = 'brands';
} elseif (empty($selectedProductIdEscaped) && $brandRow && !$catRow) {
    /* ---------------------------------------------------------------
       EMPTY-CATEGORY FIX
       Only build tiles for categories that actually hold at least one
       visible product. The HAVING clause drops "0 Products" categories
       at the database level so they never reach the grid, and the
       count itself only tallies products whose brand + category are
       both still displayed, so the number on the tile matches what the
       user sees after clicking it.
       --------------------------------------------------------------- */
    $tr = mysqli_query($con, "
        SELECT c.*,
               (
                   SELECT COUNT(p.pid)
                   FROM   products p
                   JOIN   brands   pb ON pb.brandid = p.brandid
                   WHERE  p.pcat = c.cid
                     AND  p.display_status  = 1
                     AND  pb.display_status = 1
               ) AS product_count
        FROM   category c
        WHERE  c.brandid = '{$brandRow['brandid']}'
          AND  c.display_status = 1
        HAVING product_count > 0
        ORDER  BY c.cname ASC
    ");
    if ($tr && mysqli_num_rows($tr) > 0) {
        while ($tile = mysqli_fetch_assoc($tr)) {
            $brandCategoryTiles[] = $tile;
        }
    }
    // If every category for this brand came back empty, fall through to
    // the flat product list for the brand instead of rendering an empty
    // tile grid with a misleading "0 Categories Found".
    if (!empty($brandCategoryTiles)) {
        $viewMode = 'tiles';
    }
}

$products  = [];
$totalRows = 0;

if ($viewMode === 'products') {
    $whereClauses = [];

    if (!empty($selectedProductIdEscaped)) {
        $whereClauses[] = "p.pid = '{$selectedProductIdEscaped}'";
    }
    if (!empty($selectedSearchEscaped)) {
        $whereClauses[] = "(
            LOWER(p.pname)        LIKE '%{$selectedSearchEscaped}%'
         OR LOWER(p.pdescription) LIKE '%{$selectedSearchEscaped}%'
         OR LOWER(p.pid)          LIKE '%{$selectedSearchEscaped}%'
         OR LOWER(b.brandname)    LIKE '%{$selectedSearchEscaped}%'
         OR LOWER(c.cname)        LIKE '%{$selectedSearchEscaped}%'
        )";
    }
    if ($brandRow && $catRow) {
        $whereClauses[] = "p.pcat = '{$catRow['cid']}'";
    } elseif (!empty($selectedCatNameEscaped)) {
        if (!empty($namedCatIds)) {
            $inList = "'" . implode("','", array_map(function($id) use ($con) {
                return mysqli_real_escape_string($con, $id);
            }, $namedCatIds)) . "'";
            $whereClauses[] = "p.pcat IN ($inList)";
        } else {
            $whereClauses[] = "1=0";
        }
    } elseif ($brandRow) {
        $whereClauses[] = "p.brandid = '{$brandRow['brandid']}'";
    }

    $query = "
        SELECT   p.*,
                 p.status    AS product_stock_status,
                 c.cname     AS cat_display,
                 c.status    AS cat_stock_status,
                 b.brandname AS brand_display,
                 b.status    AS brand_stock_status
        FROM     products p
        JOIN     brands   b ON b.brandid = p.brandid
        JOIN     category c ON c.cid     = p.pcat
    ";

    $conditions   = $whereClauses;
    $conditions[] = "p.display_status = 1";
    $conditions[] = "b.display_status = 1";
    $conditions[] = "c.display_status = 1";

    $query .= " WHERE " . implode(' AND ', $conditions) . " ORDER BY p.pname ASC";

    $res = mysqli_query($con, $query);
    if ($res) {
        $totalRows = mysqli_num_rows($res);
        while ($row = mysqli_fetch_assoc($res)) {
            $products[] = $row;
        }
    }
}

$pageTitle = "All Products";
if ($brandRow) {
    $pageTitle = htmlspecialchars($brandRow['brandname']) . " Products";
    if ($catRow) {
        $pageTitle .= " — " . htmlspecialchars($catRow['cname']);
    }
} elseif (!empty($selectedCatName)) {
    $pageTitle = htmlspecialchars($selectedCatName) . " Products";
}
$pageTitle .= " - PVC Security Systems";

/* ===================================================================
   RENDER HELPERS — shared by the full page and the AJAX partial
   =================================================================== */
function renderProductsGrid($viewMode, $brandCategoryTiles, $brandRow, $products, $totalRows, $catRow, $selectedSearch, $selectedCatName, $defaultImg) {
    global $brands;
    ob_start();
    ?>
    <?php if ($viewMode === 'brands'): ?>

    <div class="products-grid brands-view" id="productsGrid">
        <?php foreach ($brands as $b): ?>
        <a href="all-products.php?brand=<?php echo urlencode($b['brandid']); ?>"
           class="brand-card js-product-nav"
           style="text-decoration:none; color:inherit;"
           data-name="<?php echo htmlspecialchars($b['brandname']); ?>">
            <div class="brand-image">
                <img src="<?php echo htmlspecialchars(db_image($b['imagelink'] ?? '', $defaultImg)); ?>"
                     alt="<?php echo htmlspecialchars($b['brandname']); ?>"
                     loading="lazy"
                     onerror="this.onerror=null; this.src='<?php echo $defaultImg; ?>';">
            </div>
        </a>
        <?php endforeach; ?>
    </div>

    <?php elseif ($viewMode === 'tiles'): ?>

    <div class="products-grid tiles-view" id="productsGrid">
        <?php foreach ($brandCategoryTiles as $tile):
            $tileImg = htmlspecialchars(db_image($tile['cimage'] ?? '', $defaultImg));
            // A tile with a 'cids' array is a MERGED "All Brands" tile
            if (isset($tile['cids'])) {
                $tileHref = 'all-products.php?catname=' . urlencode($tile['cname']);
            } else {
                $tileBrandId = $tile['brandid'] ?? $brandRow['brandid'];
                $tileHref    = 'all-products.php?brand=' . urlencode($tileBrandId) . '&cat=' . urlencode($tile['cid']);
            }
        ?>
        <a href="<?php echo $tileHref; ?>"
           class="product-card js-product-nav"
           style="text-decoration:none; color:inherit;"
           data-name="<?php echo htmlspecialchars($tile['cname']); ?>">
            <div class="product-image">
                <img src="<?php echo $tileImg; ?>"
                     alt="<?php echo htmlspecialchars($tile['cname']); ?>"
                     loading="lazy"
                     onerror="this.onerror=null; this.src='<?php echo $defaultImg; ?>';">
            </div>
            <div class="product-info">
                <p class="product-title">
                    <?php echo htmlspecialchars($tile['cname']); ?>
                </p>
                <?php if (isset($tile['product_count'])): ?>
                    <span class="product-count-text"><?php echo (int)$tile['product_count']; ?> Product<?php echo (int)$tile['product_count'] !== 1 ? 's' : ''; ?></span>
                <?php endif; ?>
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
                 data-stock="<?php echo $isOutOfStock ? 'out' : 'in'; ?>"
                 data-image="<?php echo $imgSrc; ?>">
                <div class="product-image">
                    <img src="<?php echo $imgSrc; ?>"
                         alt="<?php echo htmlspecialchars(pvc_display_title($product['pname'])); ?>"
                         loading="lazy"
                         onerror="this.onerror=null; this.src='<?php echo $defaultImg; ?>';">
                </div>
                <div class="product-info">
                    <p class="product-title">
                        <?php echo htmlspecialchars(pvc_display_title($product['pname'])); ?>
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
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="no-products-found">
                <i class="fas fa-inbox"></i>
                <h3>No Products Found</h3>
                <p>
                    <?php if (!empty($selectedSearch)): ?>
                        No products found for "<?php echo htmlspecialchars($selectedSearch); ?>"
                    <?php elseif ($catRow): ?>
                        No products in "<?php echo htmlspecialchars($catRow['cname']); ?>".
                    <?php elseif (!empty($selectedCatName)): ?>
                        No products found under "<?php echo htmlspecialchars($selectedCatName); ?>".
                    <?php else: ?>
                        No products available.
                    <?php endif; ?>
                </p>
            </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>
    <?php
    return ob_get_clean();
}

function renderBackButton($brandRow, $catRow, $selectedCatName) {
    if ($brandRow && $catRow) {
        ob_start();
        ?>
        <div style="margin-bottom:16px;">
            <a href="all-products.php?brand=<?php echo urlencode($brandRow['brandid']); ?>"
               class="clear-filters-btn js-product-nav"
               style="display:inline-flex; width:auto; text-decoration:none;">
                <i class="fa-solid fa-arrow-left"></i>
                Back to <?php echo htmlspecialchars($brandRow['brandname']); ?> Categories
            </a>
        </div>
        <?php
        return ob_get_clean();
    }

    if (!$brandRow && !empty($selectedCatName)) {
        ob_start();
        ?>
        <div style="margin-bottom:16px;">
            <a href="all-products.php"
               class="clear-filters-btn js-product-nav"
               style="display:inline-flex; width:auto; text-decoration:none;">
                <i class="fa-solid fa-arrow-left"></i>
                Back to All Products
            </a>
        </div>
        <?php
        return ob_get_clean();
    }

    return '';
}

function renderBreadcrumb($brandRow, $catRow, $selectedCatName) {
    ob_start();
    ?>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
        <?php if ($brandRow): ?>
            <li class="breadcrumb-item">
                <a href="all-products.php?brand=<?php echo urlencode($brandRow['brandid']); ?>" class="js-product-nav">
                    <?php echo htmlspecialchars($brandRow['brandname']); ?>
                </a>
            </li>
            <?php if ($catRow): ?>
                <li class="breadcrumb-item active" aria-current="page">
                    <?php echo htmlspecialchars($catRow['cname']); ?>
                </li>
            <?php endif; ?>
        <?php elseif (!empty($selectedCatName)): ?>
            <li class="breadcrumb-item">
                <a href="all-products.php" class="js-product-nav">All Products</a>
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

function renderResultsCount($viewMode, $brandCategoryTiles, $totalRows) {
    global $brands;
    if ($viewMode === 'brands') {
        $n = count($brands);
        return '<span>' . $n . '</span> Brand' . ($n !== 1 ? 's' : '') . ' Found';
    }
    if ($viewMode === 'tiles') {
        $n = count($brandCategoryTiles);
        return '<span>' . $n . '</span> Categor' . ($n !== 1 ? 'ies' : 'y') . ' Found';
    }
    return '<span>' . $totalRows . '</span> Product' . ($totalRows !== 1 ? 's' : '') . ' Found';
}

/* ===================================================================
   AJAX BRANCH — returns JSON only, no header/footer, no full page
   =================================================================== */
if (isset($_GET['ajax']) && $_GET['ajax'] == '1') {
    while (ob_get_level() > 0) {
        ob_end_clean();
    }
    header('Content-Type: application/json');
    // Never let a proxy or the browser serve a stale filter payload —
    // a cached response is one of the ways the grid appears "stuck".
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Pragma: no-cache');

    echo json_encode([
        'gridHtml'        => renderProductsGrid($viewMode, $brandCategoryTiles, $brandRow, $products, $totalRows, $catRow, $selectedSearch, $selectedCatName, $defaultImg),
        'backButtonHtml'  => renderBackButton($brandRow, $catRow, $selectedCatName),
        'breadcrumbHtml'  => renderBreadcrumb($brandRow, $catRow, $selectedCatName),
        'resultsCountHtml'=> renderResultsCount($viewMode, $brandCategoryTiles, $totalRows),
        'showSort'        => $viewMode === 'products',
        'viewMode'        => $viewMode,
        'pageTitle'       => $pageTitle,
        'activeBrand'     => $resolvedBrandId,
    ]);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <meta name="description"
        content="Explore PVC Security's extensive catalog of high-performance CCTV cameras, NVRs, DVRs, and access control systems from industry leaders like Hikvision and Dahua.">
     <?php include 'head.php'; ?>
     <link rel="stylesheet" href="assets/css/all-products.css?v=<?php echo filemtime(__DIR__ . '/assets/css/all-products.css'); ?>">
    <script src="assets/js/plugins/jquery-3-6-0.min.js"></script>
    <script src="assets/js/cart-core.js"></script>

<style>
/* ===================================================================
   AJAX LOADING STATE — dim only, never collapse height (avoids the
   layout jump that used to throw the viewport down to the footer).
   =================================================================== */
#productsGridWrap{
    overflow-anchor: none;   /* stop Chrome scroll-anchoring from yanking the page */
    min-height: 200px;
}
#productsGridWrap.is-loading{
    opacity: .45;
    pointer-events: none;
    transition: opacity .15s ease;
}

/* Keep the sidebar's own scrolling contained so centering the active
   brand can never bubble up into a window scroll. */
#brandFilters{
    overscroll-behavior: contain;
}
</style>
</head>
<body data-active-brand="<?php echo htmlspecialchars($resolvedBrandId); ?>">
    <?php include 'header.php'; ?>
    <?php include 'includes/header.php'; ?>

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
<section class="all-products-section" id="allProductsSection">
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
                            <h4>Shop by Brand</h4>
                            <div class="filter-options" id="brandFilters">
                                <label class="filter-checkbox-label <?php echo empty($selectedBrand) ? 'is-checked' : ''; ?>"
                                       data-url="all-products.php">
                                    <input type="checkbox" name="brand" value=""
                                           <?php echo empty($selectedBrand) ? 'checked' : ''; ?>
                                           readonly>
                                    <span class="checkmark"></span>
                                    ALL BRANDS
                                </label>
                                <?php foreach ($brands as $b):
                                    $isActive = ($selectedBrand === (string)$b['brandid']);
                                ?>
                                <label class="filter-checkbox-label <?php echo $isActive ? 'is-checked' : ''; ?>"
                                       data-url="all-products.php?brand=<?php echo urlencode($b['brandid']); ?>">
                                    <input type="checkbox" name="brand"
                                           value="<?php echo htmlspecialchars($b['brandid']); ?>"
                                           <?php echo $isActive ? 'checked' : ''; ?>
                                           readonly>
                                    <span class="checkmark"></span>
                                    <?php echo htmlspecialchars(strtoupper($b['brandname'])); ?>
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
                    <div class="products-toolbar" id="productsToolbar">
                        <div class="toolbar-controls-row">
                            <button class="mobile-filter-toggle d-md-none" id="mobileFilterToggle">
                                <i class="fa-solid fa-sliders"></i>
                                <span>Filter</span>
                            </button>
                            <div class="results-count" id="resultsCount">
                                <?php echo renderResultsCount($viewMode, $brandCategoryTiles, $totalRows); ?>
                            </div>
                            <div class="sort-wrapper" id="sortWrapper"<?php echo $viewMode === 'products' ? '' : ' style="display:none;"'; ?>>
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
                    <div id="backButtonWrap"><?php echo renderBackButton($brandRow, $catRow, $selectedCatName); ?></div>

                    <!-- Grid (this is the only part AJAX swaps) -->
                    <div id="productsGridWrap" data-view-mode="<?php echo htmlspecialchars($viewMode); ?>">
                        <?php echo renderProductsGrid($viewMode, $brandCategoryTiles, $brandRow, $products, $totalRows, $catRow, $selectedSearch, $selectedCatName, $defaultImg); ?>
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>

    <?php include 'global_footer.php'; ?>

<script src="assets/js/plugins/bootstrap.min.js"></script>
<script src="assets/js/plugins/aos.js"></script>
<script src="assets/js/plugins/fontawesome.js"></script>
<script src="assets/js/main.js"></script>
<script src="assets/js/global_footer.js"></script>
<script src="assets/js/global_search.js"></script>

<script>
/* ===================================================================
   Browser owns scroll restoration on back/forward otherwise, and it
   restores AFTER our AJAX swap has changed the page height — which is
   how the viewport used to end up parked on the footer.
   =================================================================== */
if ('scrollRestoration' in history) {
    history.scrollRestoration = 'manual';
}

/* ===================================================================
   AJAX product loading
   ---------------------------------------------------------------
   STUCK-FILTER FIX, three parts:
     1. requestSeq  — every call takes a ticket; a response whose
                      ticket is no longer the newest is dropped, so a
                      slow first request can never overwrite a fast
                      second one.
     2. AbortController — the in-flight request is actually cancelled
                      when a new filter is clicked, instead of being
                      left to land later.
     3. no-store    — the browser can't hand back a cached JSON body
                      for a URL you already visited this session.
   The old code had none of these, so clicking brand A then brand B
   quickly could leave the grid showing A, or leave `is-loading`
   stuck on forever with a dimmed, click-through-disabled grid.
   =================================================================== */
let productsRequestSeq  = 0;
let productsAbortCtl    = null;
let lastResultsCountHtml = document.getElementById('resultsCount').innerHTML;

async function loadProducts(url, pushState = true, keepScroll = false) {
    if (!url) url = 'all-products.php';

    const gridWrap = document.getElementById('productsGridWrap');
    if (!gridWrap) return;

    const myTicket = ++productsRequestSeq;

    // Cancel whatever is still in flight from the previous click.
    if (productsAbortCtl) {
        try { productsAbortCtl.abort(); } catch (e) {}
    }
    productsAbortCtl = ('AbortController' in window) ? new AbortController() : null;

    gridWrap.classList.add('is-loading');

    let data;
    try {
        const sep = url.includes('?') ? '&' : '?';
        const opts = {
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            cache: 'no-store',
            credentials: 'same-origin'
        };
        if (productsAbortCtl) opts.signal = productsAbortCtl.signal;

        const res = await fetch(url + sep + 'ajax=1', opts);
        const raw = await res.text();

        try {
            data = JSON.parse(raw);
        } catch (parseErr) {
            console.error('Products response was not valid JSON. Raw response:', raw);
            if (myTicket === productsRequestSeq) gridWrap.classList.remove('is-loading');
            return;
        }
    } catch (err) {
        // An aborted request is expected — the newer request owns the
        // loading state now, so leave it alone and bail quietly.
        if (err && err.name === 'AbortError') return;
        console.error('Failed to load products:', err);
        if (myTicket === productsRequestSeq) gridWrap.classList.remove('is-loading');
        return;
    }

    // A newer click already superseded this response — discard it.
    if (myTicket !== productsRequestSeq) return;

    gridWrap.innerHTML = data.gridHtml;
    gridWrap.dataset.viewMode = data.viewMode || 'products';

    // Images injected via innerHTML with loading="lazy" can get stuck:
    // Chrome computes viewport intersection at insertion time and,
    // because an innerHTML swap fires no scroll/resize, sometimes never
    // recomputes. This content is always what the user is looking at,
    // so load it eagerly.
    gridWrap.querySelectorAll('img[loading="lazy"]').forEach(function (img) {
        img.loading = 'eager';
    });

    lastResultsCountHtml = data.resultsCountHtml;
    document.getElementById('resultsCount').innerHTML   = data.resultsCountHtml;
    document.getElementById('backButtonWrap').innerHTML = data.backButtonHtml;
    document.getElementById('breadcrumbNav').innerHTML  = data.breadcrumbHtml;
    document.getElementById('sortWrapper').style.display = data.showSort ? '' : 'none';
    document.title = data.pageTitle;
    document.body.dataset.activeBrand = data.activeBrand || '';

    // Always land back in naming order on a fresh view.
    const sortSelect = document.getElementById('sortSelect');
    if (sortSelect) {
        sortSelect.value = 'default';
        applySort('default');
    }

    applyStockFilter();

    if (pushState) {
        history.pushState({ ajaxUrl: url }, '', url);
    }

    gridWrap.classList.remove('is-loading');
    syncBrandFilterState();

    if (typeof AOS !== 'undefined' && AOS.refreshHard) AOS.refreshHard();

    if (!keepScroll) scrollToResultsTop();
}

/* ===================================================================
   SCROLL FIX
   Instead of letting the browser decide where to sit after the grid
   height changes, always park the viewport at the very top of the
   page after a category/brand click, rather than leaving it stranded
   wherever the old (taller/shorter) grid used to put it.
   =================================================================== */
function scrollToResultsTop() {
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

/* ===================================================================
   EVENT DELEGATION
   The old code re-ran rebindProductNavLinks() after every swap, which
   re-registered click handlers on any surviving link. Delegating from
   `document` binds exactly once for the life of the page, so a link
   can never fire loadProducts() two or three times in a row (another
   source of the "stuck / not loading" behaviour).
   =================================================================== */
document.addEventListener('click', function (e) {

    // Category tiles, brand cards, breadcrumb links, back button
    const navLink = e.target.closest('.js-product-nav');
    if (navLink) {
        e.preventDefault();
        loadProducts(navLink.getAttribute('href'));
        return;
    }

    // Brand filter checkboxes in the sidebar
    const filterLabel = e.target.closest('#brandFilters .filter-checkbox-label');
    if (filterLabel) {
        e.preventDefault();
        e.stopPropagation();
        closeMobileSidebar();
        loadProducts(filterLabel.getAttribute('data-url'));
        return;
    }

}, false);

window.addEventListener('popstate', function () {
    loadProducts(window.location.pathname + window.location.search, false, true);
});

/* ===================================================================
   Brand filter highlight + centering
   =================================================================== */
function syncBrandFilterState() {
    const activeBrand = new URLSearchParams(window.location.search).get('brand')
                     || document.body.dataset.activeBrand
                     || '';
    const filterContainer = document.getElementById('brandFilters');
    let activeLabel = null;

    document.querySelectorAll('#brandFilters .filter-checkbox-label').forEach(function (label) {
        const input = label.querySelector('input[type="checkbox"]');
        if (!input) return;
        const isActive = input.value === activeBrand;
        input.checked = isActive;
        label.classList.toggle('is-checked', isActive);
        if (isActive) activeLabel = label;
    });

    // Only touch scrollTop when the list genuinely overflows — otherwise
    // the assignment is a no-op that some browsers still treat as a
    // scroll request and bubble upward.
    if (filterContainer && activeLabel &&
        filterContainer.scrollHeight > filterContainer.clientHeight + 1) {
        requestAnimationFrame(function () {
            const containerHeight = filterContainer.clientHeight;
            const labelTop        = activeLabel.offsetTop;
            const labelHeight     = activeLabel.offsetHeight;
            const targetScrollTop = labelTop - (containerHeight / 2) + (labelHeight / 2);
            filterContainer.scrollTop = Math.max(0, targetScrollTop);
        });
    }
}

/* ===================================================================
   IN-STOCK FILTER
   Out-of-stock products are shown by default and rendered at full
   opacity. This checkbox is the opt-in way to hide them.
   =================================================================== */
function applyStockFilter() {
    const box  = document.getElementById('inStockFilter');
    const grid = document.getElementById('productsGrid');
    if (!grid) return;

    const onlyInStock = !!(box && box.checked);
    const cards = grid.querySelectorAll('.product-card[data-stock]');

    if (!cards.length) return; // brands / tiles view — nothing to filter

    let visible = 0;
    cards.forEach(function (card) {
        const hide = onlyInStock && card.dataset.stock === 'out';
        card.style.display = hide ? 'none' : '';
        if (!hide) visible++;
    });

    const countEl = document.getElementById('resultsCount');
    if (!countEl) return;
    if (onlyInStock) {
        countEl.innerHTML = '<span>' + visible + '</span> Product' + (visible !== 1 ? 's' : '') + ' Found';
    } else {
        countEl.innerHTML = lastResultsCountHtml;
    }
}

document.addEventListener('change', function (e) {
    if (e.target && e.target.id === 'inStockFilter') {
        applyStockFilter();
    }
});

/* ===================================================================
   Clear all filters
   =================================================================== */
document.getElementById('clearFilters').addEventListener('click', function (e) {
    e.preventDefault();
    const box = document.getElementById('inStockFilter');
    if (box) box.checked = false;
    closeMobileSidebar();
    loadProducts('all-products.php');
});

/* ===================================================================
   Mobile sidebar
   Locking <body> overflow used to drop the page back to scroll
   position 0/bottom on close. Save and restore it explicitly.
   =================================================================== */
const mobileToggle    = document.getElementById('mobileFilterToggle');
const sidebar         = document.getElementById('productSidebar');
const sidebarClose    = document.getElementById('sidebarCloseBtn');
const sidebarBackdrop = document.getElementById('sidebarBackdrop');
let   savedScrollY    = 0;

function openMobileSidebar() {
    if (!sidebar) return;
    savedScrollY = window.pageYOffset;
    sidebar.classList.add('active');
    if (sidebarBackdrop) sidebarBackdrop.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeMobileSidebar() {
    if (!sidebar) return;
    const wasOpen = sidebar.classList.contains('active');
    sidebar.classList.remove('active');
    if (sidebarBackdrop) sidebarBackdrop.classList.remove('active');
    document.body.style.overflow = '';
    if (wasOpen) {
        window.scrollTo(0, savedScrollY);
    }
}

if (mobileToggle && sidebar) mobileToggle.addEventListener('click', openMobileSidebar);
if (sidebarClose)            sidebarClose.addEventListener('click', closeMobileSidebar);
if (sidebarBackdrop)         sidebarBackdrop.addEventListener('click', closeMobileSidebar);

/* ===================================================================
   Sorting — "default" behaves as Name A→Z
   =================================================================== */
function applySort(sortValue) {
    const productsGrid = document.getElementById('productsGrid');
    if (!productsGrid) return;

    const cards = Array.from(productsGrid.querySelectorAll('.product-card'));
    if (!cards.length) return;

    cards.sort(function (a, b) {
        const an = a.dataset.name || '';
        const bn = b.dataset.name || '';
        if (sortValue === 'name-desc') return bn.localeCompare(an);
        return an.localeCompare(bn);
    });

    // Reorder in place with a fragment instead of wiping innerHTML —
    // wiping destroyed any "no products found" block and forced a full
    // relayout that contributed to the scroll jump.
    const frag = document.createDocumentFragment();
    cards.forEach(function (c) { frag.appendChild(c); });
    productsGrid.appendChild(frag);
}

document.addEventListener('change', function (e) {
    if (e.target && e.target.id === 'sortSelect') {
        applySort(e.target.value);
        applyStockFilter();
    }
});

/* ===================================================================
   Cart controls
   =================================================================== */
document.addEventListener('click', function (e) {

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
            qtyWrap.style.cssText = 'display:none;';
            card.querySelector('.card-cart-add').style.display = '';
            span.textContent = '1'; 
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

/* ===================================================================
   Init
   =================================================================== */
syncBrandFilterState();
applyStockFilter();
if (typeof AOS !== 'undefined') AOS.init({ duration: 800, once: true });
</script>
</body>
</html>