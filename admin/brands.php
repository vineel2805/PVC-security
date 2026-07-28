<?php
session_start(); // Always start the session first

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    // If not logged in, redirect them to the index page
    header("Location: index.php");
    exit();
}
?>

<?php
// ── Database & logic ─────────────────────────────────────────────────────────
require_once 'config/db.php';

$success = false;
$error   = '';

// This page (/admin/brands.php) sits one level below the project root.
// uploads/ is a sibling of admin/, not a child of it — browsers resolve a
// relative <img src="uploads/brands/x.jpg"> against the CURRENT page URL
// (/admin/brands.php), which would wrongly point at
// /admin/uploads/brands/x.jpg. So the DISPLAY path (for <img src>) uses
// "../uploads/brands/..." and the DISK path (for is_file()/unlink()/
// move_uploaded_file()) uses __DIR__ . '/../uploads/brands/...'.
$uploadDir     = __DIR__ . '/../uploads/brands/';
$uploadUrlBase = '../uploads/brands/';

if (!is_dir($uploadDir)) {
    @mkdir($uploadDir, 0755, true);
}

// Some existing DB rows may store imagelink as a bare filename
// ("brand_b01_123.jpg"), others as "uploads/brands/x.jpg" (root-relative),
// and others as "../uploads/brands/x.jpg" (admin-page-relative). These
// three helpers normalize ALL of those forms so reads/writes are
// consistent no matter which format is already in the DB for a given row:
//   - normalize_upload_relpath() strips any leading "../" and returns the
//     bare root-relative form, e.g. "uploads/brands/x.jpg". A bare
//     filename with no "/" at all is treated as living directly under
//     uploads/brands/ (matches how older rows were saved).
//   - upload_disk_path()    -> real physical file path on disk (for unlink)
//   - upload_display_path() -> path to use in <img src="..."> from THIS
//     page (admin/brands.php), always correctly prefixed with "../"
function normalize_upload_relpath($path) {
    $path = trim((string)$path);
    if ($path === '') return '';
    $path = ltrim($path, '/');
    while (strpos($path, '../') === 0) {
        $path = substr($path, 3);
    }
    // Bare filename (no "/" anywhere) — assume it lives in uploads/brands/
    if (strpos($path, '/') === false) {
        $path = 'uploads/brands/' . $path;
    }
    return $path; // e.g. "uploads/brands/brand_b01_123.jpg"
}
function upload_disk_path($path) {
    $rel = normalize_upload_relpath($path);
    return $rel === '' ? '' : (__DIR__ . '/../' . $rel);
}
function upload_display_path($path) {
    $rel = normalize_upload_relpath($path);
    return $rel === '' ? '' : ('../' . $rel);
}

// NEW: normalizes a brand name for duplicate-safe comparisons — collapses
// internal whitespace, trims ends, and case-folds via mb_strtolower so
// "Hikvision" and "hikvision " / "Hik vision" are treated consistently.
function normalizeBrandName($name) {
    $name = preg_replace('/\s+/u', ' ', trim((string)$name));
    return mb_strtolower($name, 'UTF-8');
}

// 1. HANDLE DELETE (cascades: brand → categories → products)
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    try {
        $brandid = $_GET['id'];

        $pdo->beginTransaction();

        // Grab categories under this brand (need their image links + ids)
        $catStmt = $pdo->prepare("SELECT cid, cimage FROM category WHERE brandid = :bid");
        $catStmt->execute([':bid' => $brandid]);
        $categories  = $catStmt->fetchAll();
        $categoryIds = array_column($categories, 'cid');

        $productImages = [];

        if (!empty($categoryIds)) {
            $placeholders = implode(',', array_fill(0, count($categoryIds), '?'));

            // Grab product images before deleting, for cleanup after commit
            $prodStmt = $pdo->prepare("SELECT pimage FROM products WHERE pcat IN ($placeholders)");
            $prodStmt->execute($categoryIds);
            foreach ($prodStmt->fetchAll() as $p) {
                if (!empty($p['pimage'])) $productImages[] = $p['pimage'];
            }

            // Delete products first (deepest level, satisfies FK to categories)
            $delProd = $pdo->prepare("DELETE FROM products WHERE pcat IN ($placeholders)");
            $delProd->execute($categoryIds);
        }

        // Delete categories (satisfies FK to brands)
        $delCat = $pdo->prepare("DELETE FROM category WHERE brandid = :bid");
        $delCat->execute([':bid' => $brandid]);

        // Grab brand image, then delete the brand itself
        $brandStmt = $pdo->prepare("SELECT imagelink FROM brands WHERE brandid = :id");
        $brandStmt->execute([':id' => $brandid]);
        $brandRow = $brandStmt->fetch();

        $delBrand = $pdo->prepare("DELETE FROM brands WHERE brandid = :id");
        $delBrand->execute([':id' => $brandid]);

        $pdo->commit();

        // Clean up image files only after the DB commit succeeds. Resolve
        // the DB-stored link (whatever format each one is in) back to the
        // real physical file on disk, then remove it.
        $cleanup = function ($link) {
            if (empty($link)) return;
            $diskPath = upload_disk_path($link);
            if ($diskPath !== '' && is_file($diskPath)) {
                @unlink($diskPath);
            }
        };
        foreach ($categories as $c) $cleanup($c['cimage']);
        foreach ($productImages as $img) $cleanup($img);
        if ($brandRow) $cleanup($brandRow['imagelink']);

        if (isset($_GET['ajax']) || (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')) {
            echo json_encode(['status' => 'success', 'msg' => 'Brand deleted successfully.']);
            exit;
        }
        header("Location: " . strtok($_SERVER["REQUEST_URI"], '?') . "?success_msg=deleted");
        exit;
    } catch (PDOException $e) {
        $pdo->rollBack();
        $error = 'Failed to delete record: ' . $e->getMessage();
    }
}

// 2. POST-REDIRECT SUCCESS FLAG
if (isset($_GET['success_msg'])) {
    $success = true;
}

// 3. HANDLE FORM SUBMISSIONS
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $brandname      = trim($_POST['brandname']      ?? '');
    $status         = trim($_POST['status']         ?? 'Active');
    $display_status = isset($_POST['display_status']) ? (int)$_POST['display_status'] : 1;
    $edit_mode      = !empty($_POST['edit_mode']);
    $existing_image = trim($_POST['existing_image'] ?? '');
    $brandid        = $edit_mode ? trim($_POST['brandid'] ?? '') : '';

    if ($brandname === '') {
        $error = 'Please fill in all required fields marked with *.';
    } else {

        if (!$edit_mode) {
            try {
                // Derive the next Brand ID from the highest numeric suffix
                // actually present in brandid (not COUNT(*), which falls out
                // of sync with the real max ID once any brand is deleted and
                // regenerates an ID that still exists on another row).
                $maxStmt = $pdo->query("SELECT MAX(CAST(SUBSTRING(brandid, 2) AS UNSIGNED)) FROM brands");
                $maxNum  = (int)$maxStmt->fetchColumn(); // 0 if table is empty
                $nextNum = $maxNum + 1;
                $brandid = 'B' . str_pad($nextNum, 3, '0', STR_PAD_LEFT);
            } catch (PDOException $e) {
                $error = 'Failed to process unique Brand ID: ' . $e->getMessage();
            }
        }

        if ($error === '' && strlen($brandid) > 4) {
            $error = 'Generated Brand ID exceeds the 4-character system limit.';
        }

        // ── Prevent duplicate brand names ──
        // Brands are global (no parent scope like category/brandid), so this
        // checks across ALL brands, excluding the current record when editing.
        if ($error === '') {
            $dupSql    = "SELECT brandid, brandname FROM brands";
            $dupParams = [];

            if ($edit_mode) {
                $dupSql .= " WHERE brandid <> :brandid";
                $dupParams[':brandid'] = $brandid;
            }

            $dupStmt = $pdo->prepare($dupSql);
            $dupStmt->execute($dupParams);
            $normalizedIncomingName = normalizeBrandName($brandname);

            while ($existingBrand = $dupStmt->fetch()) {
                if (normalizeBrandName($existingBrand['brandname']) === $normalizedIncomingName) {
                    $error = 'A brand with this name already exists.';
                    break;
                }
            }
        }

        if ($error === '') {
            // $existing_image already holds whatever form was stored in the DB
            $imagelink = $existing_image;

            if (!empty($_FILES['brand_image']['name'])) {
                $fileTmp    = $_FILES['brand_image']['tmp_name'];
                $fileName   = $_FILES['brand_image']['name'];
                $fileErr    = $_FILES['brand_image']['error'];
                $ext        = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                $allowedExt = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

                if ($fileErr !== UPLOAD_ERR_OK) {
                    $error = 'Image upload failed. Please try again.';
                } elseif (!in_array($ext, $allowedExt, true)) {
                    $error = 'Image must be one of: ' . implode(', ', $allowedExt) . '.';
                } else {
                    $newFileName     = 'brand_' . preg_replace('/[^a-zA-Z0-9_-]/', '', strtolower($brandid)) . '_' . time() . '.' . $ext;
                    $fullSavePath    = $uploadDir . $newFileName;
                    // Every new upload is ALWAYS saved to disk at
                    // ../uploads/brands/ (physical) and ALWAYS stored in the
                    // DB using that same "../uploads/brands/..." form, so
                    // future reads never need to guess the format.
                    $newRelativePath = $uploadUrlBase . $newFileName;

                    if (move_uploaded_file($fileTmp, $fullSavePath)) {
                        // Remove old image from disk if editing and old path
                        // exists (normalized, so it works whether the old DB
                        // value was a bare filename, root-relative, or
                        // admin-page-relative).
                        if ($edit_mode && $existing_image) {
                            $oldDiskPath = upload_disk_path($existing_image);
                            if ($oldDiskPath !== '' && is_file($oldDiskPath)) {
                                @unlink($oldDiskPath);
                            }
                        }
                        $imagelink = $newRelativePath;
                    } else {
                        $error = 'Could not save uploaded image.';
                    }
                }
            } elseif (!$edit_mode && $existing_image === '') {
                $imagelink = '';
            }
        }

        if ($error === '') {
            try {
                if ($edit_mode) {
                    $stmt = $pdo->prepare("
                        UPDATE brands SET
                            brandname = :brandname,
                            imagelink = :imagelink,
                            status    = :status,
                            display_status = :display_status
                        WHERE brandid = :brandid
                    ");
                    $stmt->execute([
                        ':brandname' => $brandname,
                        ':imagelink' => $imagelink,
                        ':status'    => $status,
                        ':display_status' => $display_status,
                        ':brandid'   => $brandid,
                    ]);
                    if (isset($_POST['ajax']) || (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')) {
                        echo json_encode(['status' => 'success', 'msg' => 'Brand updated successfully.']);
                        exit;
                    }
                    header("Location: " . strtok($_SERVER["REQUEST_URI"], '?') . "?success_msg=updated");
                    exit;
                } else {
                    $stmt = $pdo->prepare("
                        INSERT INTO brands (brandid, brandname, imagelink, status, display_status)
                        VALUES (:brandid, :brandname, :imagelink, :status, :display_status)
                    ");
                    $stmt->execute([
                        ':brandid'   => $brandid,
                        ':brandname' => $brandname,
                        ':imagelink' => $imagelink,
                        ':status'    => $status,
                        ':display_status' => $display_status,
                    ]);
                    if (isset($_POST['ajax']) || (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')) {
                        echo json_encode(['status' => 'success', 'msg' => 'Brand inserted successfully.']);
                        exit;
                    }
                    header("Location: " . strtok($_SERVER["REQUEST_URI"], '?') . "?success_msg=inserted");
                    exit;
                }
            } catch (PDOException $e) {
                if ((int)$e->errorInfo[1] === 1062) {
                    // Distinguish the duplicate-name safety-net constraint
                    // (uniq_brandname) from the brandid race-condition case, so
                    // the user gets the right message either way the duplicate
                    // slips through (app-level check above, or a near-simultaneous
                    // submit that beat it to the database).
                    if (strpos($e->getMessage(), 'uniq_brandname') !== false) {
                        $error = 'A brand with this name already exists.';
                    } else {
                        $error = 'A brand with that generated Brand ID already exists. Please try again.';
                    }
                } else {
                    $error = 'Database error: ' . $e->getMessage();
                }
            }
        }
    }
}
if ($error !== '' && (isset($_POST['ajax']) || (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'))) {
    echo json_encode(['status' => 'error', 'msg' => $error]);
    exit;
}

// 4. FETCH ALL RECORDS (JS handles filtering + pagination)
$brands        = [];
$total_records = 0;
try {
    $stmt = $pdo->query("
        SELECT * FROM brands
        ORDER BY CAST(SUBSTRING(brandid, 2) AS UNSIGNED) ASC, brandid ASC
    ");
    $brands        = $stmt->fetchAll();
    $total_records = count($brands);
} catch (PDOException $e) {
    $error = 'Could not load records: ' . $e->getMessage();
}

// 5. COUNT ACTIVE / INACTIVE
$activeCount   = 0;
$inactiveCount = 0;
foreach ($brands as $b) {
    if ($b['status'] === 'Active') $activeCount++;
    else $inactiveCount++;
}
?>
<?php
if (!isset($_GET['partial'])) {
    include 'header.php';
    include 'nav_header.php';
    include 'main_header.php';
    include 'sidebar.php';
}
?>

<style>
    /* ── Wide stat card ─────────────────────────────────────── */
    .brand-stat-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.08);
        padding: 0;
        display: flex;
        align-items: stretch;
        overflow: hidden;
        margin-bottom: 24px;
    }
    .bsc-icon-wrap {
        background: #9C27B0;
        width: 90px;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .bsc-icon-wrap i { font-size: 34px; color: #fff; }
    .bsc-body {
        flex: 1;
        padding: 16px 22px 12px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    .bsc-label { font-size: 13px; color: #888; font-weight: 500; margin-bottom: 2px; letter-spacing: 0.2px; }
    .bsc-number { font-size: 32px; font-weight: 700; color: #1a1a1a; line-height: 1.1; }
    .bsc-footer {
        border-top: 1px solid #f0f0f0;
        padding: 8px 22px;
        display: flex;
        align-items: center;
        gap: 16px;
        flex-wrap: wrap;
    }
    .bsc-footer-item { display: flex; align-items: center; gap: 6px; font-size: 12px; color: #666; }
    .bsc-footer-item .dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
    .dot-active   { background: #4CAF50; }
    .dot-inactive { background: #f44336; }

    /* ── Toolbar ─────────────────────────────────────────────── */
    .brands-toolbar {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }
    .brands-toolbar .search-wrap {
        position: relative;
        flex: 1;
        min-width: 200px;
        max-width: 340px;
    }
    .brands-toolbar .search-wrap i {
        position: absolute;
        left: 11px;
        top: 50%;
        transform: translateY(-50%);
        color: #aaa;
        font-size: 14px;
        pointer-events: none;
    }
    .brands-toolbar .search-input {
        width: 100%;
        height: 36px;
        padding: 0 12px 0 34px;
        border: 1px solid #dee2e6;
        border-radius: 6px;
        font-size: 13px;
        color: #333;
        outline: none;
        transition: border-color .15s;
        background: #fff;
    }
    .brands-toolbar .search-input:focus { border-color: #9C27B0; box-shadow: 0 0 0 2px rgba(156,39,176,.12); }
    .brands-toolbar .filter-select {
        height: 36px;
        padding: 0 30px 0 10px;
        border: 1px solid #dee2e6;
        border-radius: 6px;
        font-size: 13px;
        color: #333;
        background: #fff url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23999' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e") no-repeat right 8px center / 12px;
        appearance: none;
        outline: none;
        cursor: pointer;
        transition: border-color .15s;
        min-width: 150px;
    }
    .brands-toolbar .filter-select:focus { border-color: #9C27B0; box-shadow: 0 0 0 2px rgba(156,39,176,.12); }
    .filter-separator { width: 1px; height: 24px; background: #e0e0e0; flex-shrink: 0; margin: 0 2px; }

    /* ── Action buttons ─────────────────────────────────────── */
    .btn-action-large {
        width: 34px !important;
        height: 34px !important;
        padding: 0 !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        border-radius: 6px !important;
        box-shadow: 0 4px 6px rgba(0,0,0,0.08) !important;
    }
    .btn-action-large i { font-size: 14px !important; }

    /* ── Modal form ─────────────────────────────────────────── */
    #modalForm .form-label-grey { color: #666 !important; font-weight: 500; }
    #modalForm .form-control,
    #modalForm .form-select { color: #1a1a1a !important; font-weight: 500; border-color: #cbd5e1; }
    #modalForm .form-control:focus,
    #modalForm .form-select:focus { color: #000 !important; border-color: #666; }

    /* ── Table helpers ──────────────────────────────────────── */
    .brand-thumb {
        width: 44px;
        height: 44px;
        object-fit: contain;
        border: 1px solid #e5e7eb;
        border-radius: 6px;
        background: #fff;
        padding: 3px;
        cursor: pointer;
        transition: transform .15s, box-shadow .15s;
    }
    .brand-thumb:hover { transform: scale(1.08); box-shadow: 0 4px 14px rgba(0,0,0,.12); }
    .current-image-preview {
        width: 60px; height: 60px; object-fit: contain;
        border: 1px solid #cbd5e1; border-radius: 6px; padding: 4px; background: #fff;
    }
    .status-badge { padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 600; }
    .status-active   { background-color: #d4edda; color: #155724; }
    .status-inactive { background-color: #f8d7da; color: #721c24; }
    .no-img-thumb {
        width: 44px; height: 44px; border-radius: 6px;
        border: 1px dashed #d1d5db;
        display: flex; align-items: center; justify-content: center;
        background: #f9fafb; color: #c4c4c4;
    }
    .no-img-thumb i { font-size: 18px; }

    /* ── Pagination ─────────────────────────────────────────── */
    .pagination-controls {
        display: flex;
        align-items: center;
        gap: 6px;
        flex-wrap: wrap;
    }
    .pagination-controls .page-btn {
        width: 32px; height: 32px; border-radius: 6px;
        border: 1px solid #dee2e6; background: #fff;
        font-size: 13px; font-weight: 500; color: #495057;
        cursor: pointer; display: flex; align-items: center; justify-content: center;
        transition: all .15s;
    }
    .pagination-controls .page-btn:hover { border-color: #9C27B0; color: #9C27B0; }
    .pagination-controls .page-btn.active { background: #9C27B0; border-color: #9C27B0; color: #fff; }
    .pagination-controls .page-btn:disabled { opacity: .45; cursor: not-allowed; pointer-events: none; }
    .per-page-select {
        height: 32px; padding: 0 24px 0 8px; border: 1px solid #dee2e6;
        border-radius: 6px; font-size: 13px; color: #495057;
        background: #fff url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23999' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e") no-repeat right 6px center / 10px;
        appearance: none; cursor: pointer; outline: none;
    }
    .per-page-select:focus { border-color: #9C27B0; }

    /* ── Image lightbox overlay ─────────────────────────────── */
    #imgLightbox {
        display: none; position: fixed; inset: 0; z-index: 9999;
        background: rgba(0,0,0,.72); align-items: center; justify-content: center;
    }
    #imgLightbox.open { display: flex; }
    #imgLightbox img { max-width: 90vw; max-height: 88vh; border-radius: 10px; box-shadow: 0 8px 40px rgba(0,0,0,.5); }
    #imgLightbox .close-lb {
        position: absolute; top: 20px; right: 24px;
        color: #fff; font-size: 30px; cursor: pointer;
        line-height: 1; background: none; border: none;
    }

    /* ── Empty state ────────────────────────────────────────── */
    .empty-state-row td { padding: 40px 0 !important; text-align: center; }
    .empty-state-icon { font-size: 36px; color: #d1d5db; margin-bottom: 8px; }

    /* Fix table container card height to allow natural expansion */
    .card {
        height: auto !important;
    }
</style>


    <div class="content-body default-height">
        <div class="container-fluid">
            <h1>Manage Brands</h1>

         

            <?php if ($success): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fa fa-check-circle me-2"></i>
                <strong>Success!</strong> Action processed successfully.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <?php if ($error !== ''): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fa fa-exclamation-circle me-2"></i>
                <strong>Error:</strong> <?= htmlspecialchars($error) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <!-- ══════════════════════════════════════════════
                 WIDE STAT CARD
            ══════════════════════════════════════════════ -->
            <div class="brand-stat-card">
                <div class="bsc-icon-wrap">
                    <i class="fa-solid fa-tags"></i>
                </div>
                <div style="flex:1; display:flex; flex-direction:column;">
                    <div class="bsc-body">
                        <div class="bsc-label">Total Brands</div>
                        <div class="bsc-number"><?= $total_records ?></div>
                    </div>
                    <div class="bsc-footer">
                        <div class="bsc-footer-item">
                            <span class="dot dot-active"></span>
                            In Stock: <strong><?= $activeCount ?></strong>
                        </div>
                        <div class="bsc-footer-item">
                            <span class="dot dot-inactive"></span>
                            Out of Stock: <strong><?= $inactiveCount ?></strong>
                        </div>
                        <div class="bsc-footer-item ms-auto">
                            <i class="fa fa-clock me-1"></i> Last updated just now
                        </div>
                    </div>
                </div>
            </div>

            <!-- ══════════════════════════════════════════════
                 BRANDS TABLE CARD
            ══════════════════════════════════════════════ -->
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <h4 class="card-title mb-0">
                            <i class="fa-solid fa-tags me-2 text-primary"></i>Brands
                        </h4>

                        <!-- ── Toolbar ── -->
                        <div class="brands-toolbar">
                            <!-- Search bar -->
                            <div class="search-wrap">
                                <i class="fa fa-search"></i>
                                <input type="text" id="brandSearchInput" class="search-input"
                                       placeholder="Search by ID or brand name…">
                            </div>

                            <div class="filter-separator d-none d-sm-block"></div>

                            <!-- Status filter -->
                            <select id="filterStatus" class="filter-select" title="Filter by status">
                                <option value="">All Statuses</option>
                                <option value="Active">In Stock</option>
                                <option value="Inactive">Out of Stock</option>
                            </select>

                            <div class="filter-separator d-none d-sm-block"></div>

                            <!-- Add button -->
                            <button type="button" class="btn btn-success btn-sm text-white"
                                    data-bs-toggle="modal" data-bs-target="#brandModal" id="addNewBtn">
                                <i class="fa fa-plus me-1"></i> Add Brand
                            </button>
                        </div>
                    </div>

                    <div class="card-body">
                        <!-- Result count -->
                        <div class="d-flex align-items-center mb-2 gap-3 flex-wrap">
                            <p class="mb-0 text-muted small">
                                Showing <strong id="visibleCount"><?= $total_records ?></strong>
                                of <strong><?= $total_records ?></strong> brands
                            </p>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-responsive-md vertical-middle" id="brandsTable">
                                <thead>
                                    <tr>
                                        <th style="width:45%;">Brand Name</th>
                                        <th style="width:15%;">Logo</th>
                                        <th style="width:20%;">Stock Status</th>
                                        <th style="width:20%;">Website Status</th>
                                        <th style="width:20%; text-align:center;">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="brandsTableBody">
                                    <?php if (empty($brands)): ?>
                                        <tr class="empty-state-row">
                                            <td colspan="5">
                                                <div class="empty-state-icon"><i class="fa-solid fa-tags"></i></div>
                                                <p class="text-muted mb-0">No brands added yet.</p>
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($brands as $b):
                                            // Always normalize to the correct "../uploads/brands/..." form
                                            // for this admin page, regardless of how it's stored in the DB
                                            // (bare filename, root-relative, or admin-page-relative).
                                            $imgSrc    = !empty($b['imagelink']) ? htmlspecialchars(upload_display_path($b['imagelink'])) : '';
                                            $imgExists = $imgSrc !== '';
                                        ?>
                                        <tr class="brand-row"
                                            data-bid="<?= strtolower(htmlspecialchars($b['brandid'])) ?>"
                                            data-bname="<?= strtolower(htmlspecialchars($b['brandname'])) ?>"
                                            data-status="<?= htmlspecialchars($b['status']) ?>">
                                            <td>
                                                <span class="text-dark" style="font-size:14px;">
                                                    <?= htmlspecialchars($b['brandname']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if ($imgExists): ?>
                                                    <img src="<?= $imgSrc ?>"
                                                         alt="<?= htmlspecialchars($b['brandname']) ?>"
                                                         class="brand-thumb"
                                                         data-lb-src="<?= $imgSrc ?>"
                                                         data-lb-title="<?= htmlspecialchars($b['brandname']) ?>"
                                                         title="Click to enlarge"
                                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                    <div class="no-img-thumb" style="display:none;" title="Image not found">
                                                        <i class="fa-regular fa-image"></i>
                                                    </div>
                                                <?php else: ?>
                                                    <div class="no-img-thumb" title="No image uploaded">
                                                        <i class="fa-regular fa-image"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="status-badge <?= $b['status'] === 'Active' ? 'status-active' : 'status-inactive' ?>">
                                                    <?= $b['status'] === 'Active' ? 'In Stock' : 'Out of Stock' ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="status-badge <?= (int)$b['display_status'] === 1 ? 'status-active' : 'status-inactive' ?>">
                                                    <?= (int)$b['display_status'] === 1 ? 'Visible' : 'Hidden' ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex justify-content-center gap-2">
                                                    <button type="button"
                                                            class="btn btn-primary btn-action-large edit-btn"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#brandModal"
                                                            data-id="<?= htmlspecialchars($b['brandid']) ?>"
                                                            data-name="<?= htmlspecialchars($b['brandname']) ?>"
                                                            data-status="<?= htmlspecialchars($b['status']) ?>"
                                                            data-display-status="<?= htmlspecialchars($b['display_status']) ?>"
                                                            data-image="<?= htmlspecialchars($b['imagelink'] ?? '') ?>"
                                                            data-image-url="<?= $imgSrc ?>"
                                                            title="Edit Record">
                                                        <i class="fa fa-pencil"></i>
                                                    </button>
                                                    <button type="button"
                                                            class="btn btn-danger btn-action-large delete-confirm-trigger"
                                                            data-id="<?= htmlspecialchars($b['brandid']) ?>"
                                                            title="Delete Record">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- ── Pagination footer ── -->
                        <div class="d-flex align-items-center justify-content-between flex-wrap mt-3 px-1 gap-2">
                            <div class="d-flex align-items-center gap-2">
                                <label class="text-muted small mb-0" for="perPageSelect">Rows per page:</label>
                                <select id="perPageSelect" class="per-page-select">
                                    <option value="5" selected>5</option>
                                    <option value="10">10</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                </select>
                            </div>
                            <nav aria-label="Brands pagination">
                                <div class="pagination-controls" id="paginationButtons"></div>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    <!-- ══════════════════════════════════════════════════════════
         ADD / EDIT MODAL
         (kept INSIDE .content-body so it survives AJAX partial swaps —
         see notes in table-ajax.js: only descendants of .content-body
         are extracted and re-inserted on sidebar navigation)
    ══════════════════════════════════════════════════════════ -->
    <div class="modal fade" id="brandModal" tabindex="-1"
         aria-labelledby="brandModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="brandModalLabel">Create Brand</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form method="POST" id="modalForm" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" name="edit_mode"      id="modal_edit_mode"      value="0">
                        <input type="hidden" name="existing_image" id="modal_existing_image" value="">
                        <input type="hidden" name="brandid"        id="modal_brandid_hidden" value="">

                        <div class="row">
                            <div class="col-md-4 mb-3" id="brandIdContainer" style="display:none;">
                                <label class="form-label form-label-grey">Brand ID</label>
                                <input type="text" id="modal_brandid_display" class="form-control bg-light" readonly>
                            </div>
                            <div class="col-md-12 mb-3" id="brandNameContainer">
                                <label class="form-label form-label-grey">
                                    Brand Name <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="brandname" id="modal_brandname"
                                       class="form-control" placeholder="e.g. Hikvision"
                                       maxlength="50" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label form-label-grey">
                                    Status <span class="text-danger">*</span>
                                </label>
                                <select name="status" id="modal_status" class="form-select" required>
                                    <option value="Active">Stock-In</option>
                                    <option value="Inactive">Stock-Out</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label form-label-grey">Display on Website <span class="text-danger">*</span></label>
                                <select name="display_status" id="modal_display_status" class="form-select" required>
                                    <option value="1">Visible</option>
                                    <option value="0">Hidden</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label class="form-label form-label-grey">Brand Logo</label>
                                <input type="file" name="brand_image" id="modal_brand_image"
                                       class="form-control" accept=".jpg,.jpeg,.png,.gif,.webp">
                                <small class="text-muted">Leave empty to keep the current image when editing.</small>
                            </div>
                            <div class="col-md-4 mb-3" id="currentImageWrap" style="display:none;">
                                <label class="form-label form-label-grey">Current Image</label>
                                <div>
                                    <img id="currentImagePreview" src="" alt="Current logo" class="current-image-preview">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success text-white" id="modalSubmitBtn">
                            Save Brand
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ══════════════════════════════════════════════════════════
         DELETE CONFIRMATION MODAL
    ══════════════════════════════════════════════════════════ -->
    <div class="modal fade" id="deleteConfirmationModal" tabindex="-1"
         aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title text-dark" id="deleteModalLabel">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-2 text-dark">
                    Are you sure you want to permanently delete this brand?
                    This cannot be undone, and will fail if categories still reference it.
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Cancel</button>
                    <a href="#" id="modalDeleteExecutionLink" class="btn btn-sm btn-danger text-white">
                        Delete Record
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- ══════════════════════════════════════════════════════════
         IMAGE LIGHTBOX
    ══════════════════════════════════════════════════════════ -->
    <div id="imgLightbox" role="dialog" aria-modal="true" aria-label="Brand logo viewer">
        <button class="close-lb" id="closeLightbox" aria-label="Close image viewer">&times;</button>
        <img id="lbImage" src="" alt="">
    </div>

    <div class="footer">
        <div class="copyright">
            <p>Copyright &copy; Designed &amp; Developed by
                <a href="https://dexignlab.com/" target="_blank">DexignLab</a> 2023
            </p>
        </div>
    </div>



<script>
function initBrands() {

    /* ────────────────────────────────────────────────────────
       ELEMENT REFS FOR MODAL POPULATION
    ──────────────────────────────────────────────────────── */
    const modalTitle          = document.getElementById('brandModalLabel');
    const modalSubmitBtn      = document.getElementById('modalSubmitBtn');
    const modalForm           = document.getElementById('modalForm');
    const brandIdContainer    = document.getElementById('brandIdContainer');
    const brandIdDisplay      = document.getElementById('modal_brandid_display');
    const brandIdHidden       = document.getElementById('modal_brandid_hidden');
    const brandNameCont       = document.getElementById('brandNameContainer');
    const statusSelect        = document.getElementById('modal_status');
    const currentImageWrap    = document.getElementById('currentImageWrap');
    const currentImagePreview = document.getElementById('currentImagePreview');
    const brandImageInput     = document.getElementById('modal_brand_image');

    /* ────────────────────────────────────────────────────────
       IMAGE LIGHTBOX
    ──────────────────────────────────────────────────────── */
    const lightbox   = document.getElementById('imgLightbox');
    const lbImage    = document.getElementById('lbImage');
    const closeLbBtn = document.getElementById('closeLightbox');

    document.addEventListener('click', function (e) {
        const thumb = e.target.closest('.brand-thumb[data-lb-src]');
        if (thumb) {
            lbImage.src = thumb.dataset.lbSrc;
            lbImage.alt = thumb.dataset.lbTitle || '';
            lightbox.classList.add('open');
        }
    });
    closeLbBtn.addEventListener('click', function () { lightbox.classList.remove('open'); });
    lightbox.addEventListener('click', function (e) {
        if (e.target === lightbox) lightbox.classList.remove('open');
    });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') lightbox.classList.remove('open');
    });

    /* ────────────────────────────────────────────────────────
       INERT-BASED MODAL FOCUS FIX
       Chrome devtools flags "Blocked aria-hidden on an element
       because its descendant retained focus" whenever a Bootstrap
       modal finishes closing (aria-hidden="true" gets set on it)
       while the button that was just clicked (e.g. the header
       .btn-close, or the footer "Close"/"Cancel" button) is still
       the focused element inside it. admin-crud.js already patches
       this for AJAX-driven closes via a deferred blur() timed after
       Bootstrap's focus trap deactivates — but that timing trick is
       inherently a race. Chrome's own warning recommends a more
       robust fix: `inert`. Setting it the INSTANT hide.bs.modal
       fires (synchronously, no setTimeout race at all) makes the
       browser itself refuse to let any element inside the modal
       hold or receive focus, so by the time aria-hidden is applied
       later, nothing focused can be in there — the warning can't
       fire, regardless of which button (header X, footer Close/
       Cancel, or a JS-driven hide()) triggered the close. inert is
       cleared again on shown.bs.modal so the modal is fully
       interactive the next time it opens.
    ──────────────────────────────────────────────────────── */
    ['#brandModal', '#deleteConfirmationModal'].forEach(function (sel) {
        const modalEl = document.querySelector(sel);
        if (!modalEl) return;

        modalEl.addEventListener('hide.bs.modal', function () {
            modalEl.inert = true;
        });
        modalEl.addEventListener('shown.bs.modal', function () {
            modalEl.inert = false;
        });
    });

    /* ────────────────────────────────────────────────────────
       FILE INPUT STATE GUARD
       Opening the Edit modal never cleared a stale file selection
       left over from a prior Add/Edit session. Because the browser
       keeps whatever file was last chosen in the <input type="file">
       across modal open/close, an old logo could get silently
       resubmitted and overwrite the current brand's logo even when
       the user never touched the file field this time (despite
       "Leave empty to keep the current image" being followed). We now
       explicitly clear this input every time the modal opens (both
       Add and Edit paths) AND whenever the modal is dismissed, so a
       leftover selection can never leak into an unrelated save.
    ──────────────────────────────────────────────────────── */
    function clearBrandImageInput() {
        brandImageInput.value = '';
    }
    document.getElementById('brandModal').addEventListener('hidden.bs.modal', clearBrandImageInput);

    /* ────────────────────────────────────────────────────────
       INITIALIZE CRUD CONTROLLER
    ──────────────────────────────────────────────────────── */
    new AdminCrud({
        endpoint: 'brands.php',
        tableSelector: '#brandsTableBody',
        rowSelector: '.brand-row',
        formSelector: '#modalForm',
        modalSelector: '#brandModal',
        deleteModalSelector: '#deleteConfirmationModal',
        deleteExecutionSelector: '#modalDeleteExecutionLink',
        statsSelector: '.brand-stat-card',
        visibleCountSelector: '#visibleCount',
        searchInputSelector: '#brandSearchInput',
        statusFilterSelector: '#filterStatus',
        perPageSelector: '#perPageSelect',
        paginationSelector: '#paginationButtons',
        emptyStateColspan: 5,
        emptyStateText: 'No brands match your search or filters.',
        matchRow: function(row, q, status) {
            const haystack = (row.dataset.bid || '') + ' ' + (row.dataset.bname || '');
            if (q && haystack.toLowerCase().indexOf(q) === -1) return false;
            if (status && row.dataset.status !== status) return false;
            return true;
        },
        onAddNewClick: function() {
            modalForm.reset();
            clearBrandImageInput(); // explicit clear, don't rely solely on form.reset()
            document.getElementById('modal_edit_mode').value      = '0';
            document.getElementById('modal_existing_image').value = '';
            brandIdHidden.value  = '';
            brandIdDisplay.value = '';
            brandIdContainer.style.display = 'none';
            brandNameCont.className        = 'col-md-12 mb-3';
            statusSelect.value             = 'Active';
            document.getElementById('modal_display_status').value = '1';
            currentImageWrap.style.display = 'none';
            modalTitle.innerText           = 'Brand Registration Form';
            modalSubmitBtn.innerText       = 'Save Brand';
        },
        onEditClick: function(btn) {
            modalTitle.innerText     = 'Edit Brand Info';
            modalSubmitBtn.innerText = 'Save Changes';
            document.getElementById('modal_edit_mode').value      = '1';
            document.getElementById('modal_existing_image').value = btn.dataset.image || '';

            // Clear any stale file selection left over from a previous
            // Add/Edit session BEFORE populating this edit's data. Without
            // this, a previously-chosen file could still be sitting in the
            // input and get silently resubmitted as if the user had picked
            // a new logo for THIS brand, overwriting its real image even
            // though "Leave empty to keep the current image" was followed.
            clearBrandImageInput();

            brandIdHidden.value  = btn.dataset.id;
            brandIdDisplay.value = btn.dataset.id;
            brandIdContainer.style.display = 'block';
            brandNameCont.className        = 'col-md-8 mb-3';
            document.getElementById('modal_brandname').value = btn.dataset.name;
            statusSelect.value = btn.dataset.status || 'Active';
            document.getElementById('modal_display_status').value = btn.dataset.displayStatus || '1';

            if (btn.dataset.imageUrl) {
                currentImagePreview.src        = btn.dataset.imageUrl;
                currentImageWrap.style.display = 'block';
            } else {
                currentImageWrap.style.display = 'none';
            }
        }
    });

}
if (document.readyState === 'loading') {
    document.addEventListener("DOMContentLoaded", initBrands);
} else {
    initBrands();
}
</script>
</div><!-- /.content-body -->
<?php
if (!isset($_GET['partial'])) {
    include 'footer.php';
}
?>