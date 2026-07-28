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

function normalizeCategoryName($name) {
    $name = preg_replace('/\s+/u', ' ', trim((string)$name));
    return mb_strtolower($name, 'UTF-8');
}

function getPreservedStateParams() {
    $params = [];

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['return_qs'])) {
        parse_str(ltrim((string)$_POST['return_qs'], '?'), $params);
    } else {
        $params = $_GET;
    }

    unset($params['action'], $params['id'], $params['success_msg'], $params['products_removed']);
    return $params;
}

function buildRedirectUrl($successMsg, array $extraParams = []) {
    $base   = strtok($_SERVER['REQUEST_URI'], '?');
    $params = getPreservedStateParams();

    foreach ($extraParams as $k => $v) {
        $params[$k] = $v;
    }
    $params['success_msg'] = $successMsg;

    $query = http_build_query($params);
    return $query ? ($base . '?' . $query) : $base;
}

// This page (/admin/categories.php) sits one level below the project root.
// uploads/ is a sibling of admin/, not a child of it — browsers resolve a
// relative <img src="uploads/category/x.jpg"> against the CURRENT page URL
// (/admin/categories.php), which would wrongly point at
// /admin/uploads/category/x.jpg. So the DISPLAY path (for <img src>) uses
// "../uploads/category/..." and the DISK path (for is_file()/unlink()/
// move_uploaded_file()) uses __DIR__ . '/../uploads/category/...'.
$uploadDir     = __DIR__ . '/../uploads/category/';
$uploadUrlBase = '../uploads/category/';

if (!is_dir($uploadDir)) {
    @mkdir($uploadDir, 0755, true);
}

// Some existing DB rows may store cimage/pimage as "uploads/category/x.jpg"
// (no leading "../", root-relative) while others store it as
// "../uploads/category/x.jpg" (admin-page-relative). These three helpers
// normalize both forms so reads/writes are consistent no matter which
// format is already in the DB for a given row:
//   - normalize_upload_relpath() strips any leading "../" and returns the
//     bare root-relative form, e.g. "uploads/category/x.jpg"
//   - upload_disk_path()    -> real physical file path on disk (for unlink)
//   - upload_display_path() -> path to use in <img src="..."> from THIS
//     page (admin/categories.php), always correctly prefixed with "../"
function normalize_upload_relpath($path) {
    $path = trim((string)$path);
    if ($path === '') return '';
    $path = ltrim($path, '/');
    while (strpos($path, '../') === 0) {
        $path = substr($path, 3);
    }
    return $path; // e.g. "uploads/category/cat_xyz.jpg"
}
function upload_disk_path($path) {
    $rel = normalize_upload_relpath($path);
    return $rel === '' ? '' : (__DIR__ . '/../' . $rel);
}
function upload_display_path($path) {
    $rel = normalize_upload_relpath($path);
    return $rel === '' ? '' : ('../' . $rel);
}

// Generate the next Category ID from the HIGHEST existing numeric suffix
// for that brand, not from a COUNT(*) of rows. COUNT(*) breaks as soon as
// any category for that brand has ever been deleted, because the sequence
// then has "gaps" (e.g. B01 has C02..C09 but only 8 rows exist after C01
// was deleted) — COUNT(*)+1 would recompute C09 again and collide with the
// still-existing C09, throwing a false "race condition" error on every
// single save, not just concurrent ones. Scanning for the max "-C##"
// suffix already used guarantees an always-fresh, ever-increasing number.
function nextCategoryId(PDO $pdo, $brandid) {
    $stmt = $pdo->prepare("SELECT cid FROM category WHERE brandid = :brandid");
    $stmt->execute([':brandid' => $brandid]);

    $maxNum = 0;
    foreach ($stmt->fetchAll(PDO::FETCH_COLUMN) as $existingCid) {
        if (preg_match('/-C(\d+)$/i', (string)$existingCid, $m)) {
            $maxNum = max($maxNum, (int)$m[1]);
        }
    }
    $nextNum = $maxNum + 1;
    return strtoupper($brandid) . '-C' . str_pad($nextNum, 2, '0', STR_PAD_LEFT);
}

// 1. HANDLE DELETE
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    try {
        $cid = $_GET['id'];

        $pdo->beginTransaction();

        // ── Fetch all products in this category first (need image paths to clean up files) ──
        $prodStmt = $pdo->prepare("SELECT pid, pimage FROM products WHERE pcat = :cid");
        $prodStmt->execute([':cid' => $cid]);
        $productsToDelete = $prodStmt->fetchAll();

        // ── Delete those products ──
        $delProdStmt = $pdo->prepare("DELETE FROM products WHERE pcat = :cid");
        $delProdStmt->execute([':cid' => $cid]);

        // ── Fetch the category's own image before deleting it ──
        $stmt = $pdo->prepare("SELECT cimage FROM category WHERE cid = :id");
        $stmt->execute([':id' => $cid]);
        $row = $stmt->fetch();

        // ── Delete the category itself ──
        $stmt = $pdo->prepare("DELETE FROM category WHERE cid = :id");
        $stmt->execute([':id' => $cid]);

        $pdo->commit();

        // ── Clean up files from disk (only after successful commit) ──
        // Resolve the DB-stored cimage/pimage (whatever format each one is in)
        // back to the real physical file on disk, then remove it.
        foreach ($productsToDelete as $p) {
            if (!empty($p['pimage'])) {
                $diskPath = upload_disk_path($p['pimage']);
                if ($diskPath !== '' && is_file($diskPath)) {
                    @unlink($diskPath);
                }
            }
        }
        if ($row && !empty($row['cimage'])) {
            $diskPath = upload_disk_path($row['cimage']);
            if ($diskPath !== '' && is_file($diskPath)) {
                @unlink($diskPath);
            }
        }

        $deletedProductCount = count($productsToDelete);
        if (isset($_GET['ajax']) || (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')) {
            echo json_encode(['status' => 'success', 'msg' => 'Category and its ' . $deletedProductCount . ' products deleted successfully.']);
            exit;
        }
        header("Location: " . buildRedirectUrl('deleted', ['products_removed' => $deletedProductCount]));
        exit;

    } catch (PDOException $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();

        if ((int)$e->errorInfo[1] === 1451) {
            $error = 'Cannot delete this category: it is still linked to other records that could not be automatically removed.';
        } else {
            $error = 'Failed to delete record: ' . $e->getMessage();
        }
    }
}
// 2. POST-REDIRECT SUCCESS FLAG
if (isset($_GET['success_msg'])) {
    $success = true;
}

// 3. HANDLE FORM SUBMISSIONS
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $cname_select   = trim($_POST['cname_select'] ?? '__NEW__');
    $cname_new      = trim($_POST['cname_new']    ?? '');
    $cname          = ($cname_select === '__NEW__') ? $cname_new : $cname_select;

    $brandid        = trim($_POST['brandid'] ?? '');
    $status         = trim($_POST['status']  ?? 'Active');
    $display_status = isset($_POST['display_status']) ? (int)$_POST['display_status'] : 1;
    $edit_mode      = !empty($_POST['edit_mode']);
    $existing_image = trim($_POST['existing_image'] ?? '');
    $cid            = $edit_mode ? trim($_POST['cid'] ?? '') : '';

    if ($cname === '') {
        $error = 'Please fill in all required fields marked with *.';
    } elseif ($brandid === '') {
        $error = 'Please select a Brand.';
    } else {

        if (!$edit_mode) {
            try {
                $cid = nextCategoryId($pdo, $brandid);
            } catch (PDOException $e) {
                $error = 'Failed to automatically compute a unique Category ID: ' . $e->getMessage();
            }
        }

        if ($error === '' && strlen($cid) > 15) {
            $error = 'The computed automatic Category ID exceeds system schema limits (15 chars).';
        }

        if ($error === '') {
            $duplicateSql = "SELECT cid, cname FROM category WHERE brandid = :brandid";
            $params = [':brandid' => $brandid];

            if ($edit_mode) {
                $duplicateSql .= " AND cid <> :cid";
                $params[':cid'] = $cid;
            }

            $dupStmt = $pdo->prepare($duplicateSql);
            $dupStmt->execute($params);
            $normalizedIncomingName = normalizeCategoryName($cname);

            while ($existing = $dupStmt->fetch()) {
                if (normalizeCategoryName($existing['cname']) === $normalizedIncomingName) {
                    $error = 'This category already exists under the selected brand.';
                    break;
                }
            }
        }

        if ($error === '') {
            // $existing_image already holds whatever form was stored in the DB
            $cimage = $existing_image;

            if (!empty($_FILES['category_image']['name'])) {
                $fileTmp    = $_FILES['category_image']['tmp_name'];
                $fileName   = $_FILES['category_image']['name'];
                $fileErr    = $_FILES['category_image']['error'];
                $ext        = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                $allowedExt = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

                if ($fileErr !== UPLOAD_ERR_OK) {
                    $error = 'Image upload failed. Please try again.';
                } elseif (!in_array($ext, $allowedExt, true)) {
                    $error = 'Image must be one of: ' . implode(', ', $allowedExt) . '.';
                } else {
                    $cleanCid       = preg_replace('/[^a-zA-Z0-9_-]/', '', strtolower($cid));
                    $newFileName    = 'cat_' . $cleanCid . '_' . time() . '.' . $ext;
                    $fullSavePath   = $uploadDir . $newFileName;
                    // Every new upload is ALWAYS saved to disk at
                    // ../uploads/category/ (physical) and ALWAYS stored in the
                    // DB using that same "../uploads/category/..." form, so
                    // future reads never need to guess the format.
                    $newRelativePath = $uploadUrlBase . $newFileName;

                    if (move_uploaded_file($fileTmp, $fullSavePath)) {
                        // Remove old image from disk if editing and old path exists
                        // (normalized, so it works whether the old DB value had
                        // the "../" prefix or not).
                        if ($edit_mode && $existing_image) {
                            $oldDiskPath = upload_disk_path($existing_image);
                            if ($oldDiskPath !== '' && is_file($oldDiskPath)) {
                                @unlink($oldDiskPath);
                            }
                        }
                        $cimage = $newRelativePath;
                    } else {
                        $error = 'Could not save uploaded image.';
                    }
                }
            } elseif (!$edit_mode && $existing_image === '') {
                $cimage = '';
            }
        }

        if ($error === '') {
            try {
                if ($edit_mode) {
                    $stmt = $pdo->prepare("
                        UPDATE category SET
                            cname   = :cname,
                            cimage  = :cimage,
                            brandid = :brandid,
                            status  = :status,
                            display_status = :display_status
                        WHERE cid = :cid
                    ");
                    $stmt->execute([
                        ':cname'   => $cname,
                        ':cimage'  => $cimage,
                        ':brandid' => $brandid,
                        ':status'  => $status,
                        ':display_status' => $display_status,
                        ':cid'     => $cid,
                    ]);
                    if (isset($_POST['ajax']) || (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')) {
                        echo json_encode(['status' => 'success', 'msg' => 'Category updated successfully.']);
                        exit;
                    }
                    header("Location: " . buildRedirectUrl('updated'));
                    exit;
                } else {
                    // If a genuine simultaneous submission still manages to
                    // collide on the primary key (error 1062), retry once
                    // with a freshly recomputed ID instead of just bouncing
                    // the error back to the user.
                    $attempts = 0;
                    while (true) {
                        try {
                            $stmt = $pdo->prepare("
                                INSERT INTO category (cid, cname, cimage, brandid, status, display_status)
                                VALUES (:cid, :cname, :cimage, :brandid, :status, :display_status)
                            ");
                            $stmt->execute([
                                ':cid'     => $cid,
                                ':cname'   => $cname,
                                ':cimage'  => $cimage,
                                ':brandid' => $brandid,
                                ':status'  => $status,
                                ':display_status' => $display_status,
                            ]);
                            break;
                        } catch (PDOException $e) {
                            $attempts++;
                            if ((int)$e->errorInfo[1] === 1062 && $attempts < 3) {
                                $cid = nextCategoryId($pdo, $brandid);
                                continue;
                            }
                            throw $e;
                        }
                    }
                    if (isset($_POST['ajax']) || (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')) {
                        echo json_encode(['status' => 'success', 'msg' => 'Category inserted successfully.']);
                        exit;
                    }
                    header("Location: " . buildRedirectUrl('inserted'));
                    exit;
                }
            } catch (PDOException $e) {
                if ((int)$e->errorInfo[1] === 1062) {
                    $error = 'A system race condition occurred: Generated Category ID already exists. Please submit again.';
                } elseif ((int)$e->errorInfo[1] === 1452) {
                    $error = 'Selected brand context no longer exists.';
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

// 4. FETCH ALL RECORDS (no server-side pagination — JS handles it)
$categories    = [];
$total_records = 0;
try {
    $stmt = $pdo->query("
        SELECT c.*, b.brandname
        FROM category c
        LEFT JOIN brands b ON b.brandid = c.brandid
        ORDER BY c.cid ASC
    ");
    $categories    = $stmt->fetchAll();
    $total_records = count($categories);
} catch (PDOException $e) {
    $error = 'Could not load records: ' . $e->getMessage();
}

// 5. FETCH BRANDS
$brandOptions = [];
try {
    $stmt = $pdo->query("SELECT brandid, brandname FROM brands ORDER BY brandname ASC");
    $brandOptions = $stmt->fetchAll();
} catch (PDOException $e) { /* non-fatal */ }

// 6. FETCH DISTINCT CATEGORY NAMES for select dropdown
$distinctCategories = [];
try {
    $stmt = $pdo->query("SELECT DISTINCT cname FROM category WHERE cname != '' ORDER BY cname ASC");
    $distinctCategories = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) { /* non-fatal */ }

// 7. COUNT ACTIVE / INACTIVE
$activeCount   = 0;
$inactiveCount = 0;
foreach ($categories as $c) {
    if ($c['status'] === 'Active') $activeCount++;
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
    .category-stat-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.08);
        padding: 0;
        display: flex;
        align-items: stretch;
        overflow: hidden;
        margin-bottom: 24px;
    }
    .csc-icon-wrap {
        background: #2196F3;
        width: 90px;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .csc-icon-wrap i { font-size: 34px; color: #fff; }
    .csc-body {
        flex: 1;
        padding: 16px 22px 12px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    .csc-label { font-size: 13px; color: #888; font-weight: 500; margin-bottom: 2px; letter-spacing: 0.2px; }
    .csc-number { font-size: 32px; font-weight: 700; color: #1a1a1a; line-height: 1.1; }
    .csc-footer {
        border-top: 1px solid #f0f0f0;
        padding: 8px 22px;
        display: flex;
        align-items: center;
        gap: 16px;
        flex-wrap: wrap;
    }
    .csc-footer-item { display: flex; align-items: center; gap: 6px; font-size: 12px; color: #666; }
    .csc-footer-item .dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
    .dot-active   { background: #4CAF50; }
    .dot-inactive { background: #f44336; }

    /* ── Toolbar ─────────────────────────────────────────────── */
    .categories-toolbar {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }
    .categories-toolbar .search-wrap {
        position: relative;
        flex: 1;
        min-width: 200px;
        max-width: 340px;
    }
    .categories-toolbar .search-wrap i {
        position: absolute;
        left: 11px;
        top: 50%;
        transform: translateY(-50%);
        color: #aaa;
        font-size: 14px;
        pointer-events: none;
    }
    .categories-toolbar .search-input {
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
    .categories-toolbar .search-input:focus { border-color: #2196F3; box-shadow: 0 0 0 2px rgba(33,150,243,.12); }
    .categories-toolbar .filter-select {
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
    .categories-toolbar .filter-select:focus { border-color: #2196F3; box-shadow: 0 0 0 2px rgba(33,150,243,.12); }
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
    .cat-thumb {
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
    .cat-thumb:hover { transform: scale(1.08); box-shadow: 0 4px 14px rgba(0,0,0,.12); }
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
    .pagination-controls .page-btn:hover { border-color: #2196F3; color: #2196F3; }
    .pagination-controls .page-btn.active { background: #2196F3; border-color: #2196F3; color: #fff; }
    .pagination-controls .page-btn:disabled { opacity: .45; cursor: not-allowed; pointer-events: none; }
    .per-page-select {
        height: 32px; padding: 0 24px 0 8px; border: 1px solid #dee2e6;
        border-radius: 6px; font-size: 13px; color: #495057;
        background: #fff url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23999' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e") no-repeat right 6px center / 10px;
        appearance: none; cursor: pointer; outline: none;
    }
    .per-page-select:focus { border-color: #2196F3; }

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
            <div class="category-stat-card">
                <div class="csc-icon-wrap">
                    <i class="fa-solid fa-layer-group"></i>
                </div>
                <div style="flex:1; display:flex; flex-direction:column;">
                    <div class="csc-body">
                        <div class="csc-label">Total Categories</div>
                        <div class="csc-number" id="statTotalCount"><?= $total_records ?></div>
                    </div>
                    <div class="csc-footer">
                        <div class="csc-footer-item">
                            <span class="dot dot-active"></span>
                            In Stock: <strong><?= $activeCount ?></strong>
                        </div>
                        <div class="csc-footer-item">
                            <span class="dot dot-inactive"></span>
                            Out of Stock: <strong><?= $inactiveCount ?></strong>
                        </div>
                        <div class="csc-footer-item ms-auto">
                            <i class="fa fa-clock me-1"></i> Last updated just now
                        </div>
                    </div>
                </div>
            </div>

            <!-- ══════════════════════════════════════════════
                 CATEGORIES TABLE CARD
            ══════════════════════════════════════════════ -->
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <h4 class="card-title mb-0">
                            <i class="fa-solid fa-layer-group me-2 text-primary"></i>Categories
                        </h4>

                        <!-- ── Toolbar ── -->
                        <div class="categories-toolbar">
                            <!-- Search bar -->
                            <div class="search-wrap">
                                <i class="fa fa-search"></i>
                                <input type="text" id="categorySearchInput" class="search-input"
                                       placeholder="Search by ID, name, brand…">
                            </div>

                            <div class="filter-separator d-none d-sm-block"></div>

                            <!-- Brand filter -->
                            <select id="filterBrand" class="filter-select" title="Filter by brand">
                                <option value="">All Brands</option>
                                <?php foreach ($brandOptions as $opt): ?>
                                    <option value="<?= htmlspecialchars($opt['brandid']) ?>">
                                        <?= htmlspecialchars($opt['brandname']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>

                            <!-- Status filter -->
                            <select id="filterStatus" class="filter-select" title="Filter by status">
                                <option value="">All Statuses</option>
                                <option value="Active">In Stock</option>
                                <option value="Inactive">Out of Stock</option>
                            </select>

                            <div class="filter-separator d-none d-sm-block"></div>

                            <!-- Add button — no data-bs-toggle/target here. AdminCrud's
                                 onAddNewClick is responsible for opening the modal via
                                 getCategoryModal().show() below, avoiding a double
                                 backdrop stack. -->
                            <button type="button" class="btn btn-success btn-sm text-white" id="addNewBtn">
                                <i class="fa fa-plus me-1"></i> Add Category
                            </button>
                        </div>
                    </div>

                    <div class="card-body">
                        <!-- Result count -->
                        <div class="d-flex align-items-center mb-2 gap-3 flex-wrap">
                            <p class="mb-0 text-muted small">
                                Showing <strong id="visibleCount"><?= $total_records ?></strong>
                                of <strong><?= $total_records ?></strong> categories
                            </p>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-responsive-md vertical-middle" id="categoriesTable">
                                <thead>
                                    <tr>
                                        <th style="width:37%;">Category Name</th>
                                        <th style="width:18%;">Brand</th>
                                        <th style="width:8%;">Image</th>
                                        <th style="width:13%;">Stock Status</th>
                                        <th style="width:13%;">Website Status</th>
                                        <th style="width:24%; text-align:center;">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="categoryTableBody">
                                    <?php if (empty($categories)): ?>
                                        <tr class="empty-state-row">
                                            <td colspan="6">
                                                <div class="empty-state-icon"><i class="fa-solid fa-layer-group"></i></div>
                                                <p class="text-muted mb-0">No categories registered yet.</p>
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($categories as $c):
                                            // Always normalize to the correct "../uploads/category/..." form
                                            // for this admin page, regardless of how it's stored in the DB.
                                            $imgSrc    = !empty($c['cimage']) ? htmlspecialchars(upload_display_path($c['cimage'])) : '';
                                            $imgExists = $imgSrc !== '';
                                        ?>
                                        <tr class="category-row"
                                            data-cid="<?= strtolower(htmlspecialchars($c['cid'])) ?>"
                                            data-cname="<?= strtolower(htmlspecialchars($c['cname'])) ?>"
                                            data-brandid="<?= htmlspecialchars($c['brandid'] ?? '') ?>"
                                            data-brandname="<?= strtolower(htmlspecialchars($c['brandname'] ?? '')) ?>"
                                            data-status="<?= htmlspecialchars($c['status']) ?>">
                                            <td>
                                                <span class="text-dark" style="font-size:14px;">
                                                    <?= htmlspecialchars($c['cname']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="text-dark small">
                                                    <?= $c['brandname']
                                                        ? htmlspecialchars($c['brandname'])
                                                        : '<span class="text-muted">— none —</span>' ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if ($imgExists): ?>
                                                    <img src="<?= $imgSrc ?>"
                                                         alt="<?= htmlspecialchars($c['cname']) ?>"
                                                         class="cat-thumb"
                                                         data-lb-src="<?= $imgSrc ?>"
                                                         data-lb-title="<?= htmlspecialchars($c['cname']) ?>"
                                                         title="Click to enlarge">
                                                <?php else: ?>
                                                    <div class="no-img-thumb" title="No image uploaded">
                                                        <i class="fa-regular fa-image"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="status-badge <?= $c['status'] === 'Active' ? 'status-active' : 'status-inactive' ?>">
                                                    <?= $c['status'] === 'Active' ? 'In Stock' : 'Out of Stock' ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="status-badge <?= (int)$c['display_status'] === 1 ? 'status-active' : 'status-inactive' ?>">
                                                    <?= (int)$c['display_status'] === 1 ? 'Visible' : 'Hidden' ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex justify-content-center gap-2">
                                                    <!-- No data-bs-toggle/target here either — same reasoning
                                                         as the Add button above. -->
                                                    <button type="button"
                                                            class="btn btn-primary btn-action-large edit-btn"
                                                            data-id="<?= htmlspecialchars($c['cid']) ?>"
                                                            data-name="<?= htmlspecialchars($c['cname']) ?>"
                                                            data-brandid="<?= htmlspecialchars($c['brandid'] ?? '') ?>"
                                                            data-status="<?= htmlspecialchars($c['status']) ?>"
                                                            data-display-status="<?= htmlspecialchars($c['display_status']) ?>"
                                                            data-image="<?= htmlspecialchars($c['cimage'] ?? '') ?>"
                                                            data-image-url="<?= $imgSrc ?>"
                                                            title="Edit Record">
                                                        <i class="fa fa-pencil"></i>
                                                    </button>
                                                    <button type="button"
                                                            class="btn btn-danger btn-action-large delete-confirm-trigger"
                                                            data-id="<?= htmlspecialchars($c['cid']) ?>"
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
                        <div class="d-flex align-items-center justify-content-between flex-wrap mt-3 px-1 gap-2" id="paginationWrapper">
                            <div class="d-flex align-items-center gap-2">
                                <label class="text-muted small mb-0" for="perPageSelect">Rows per page:</label>
                                <select id="perPageSelect" class="per-page-select">
                                    <option value="5" selected>5</option>
                                    <option value="10">10</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                            </div>
                            <nav aria-label="Categories pagination">
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
    <div class="modal fade" id="categoryModal" tabindex="-1"
         aria-labelledby="categoryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="categoryModalLabel">Create Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form method="POST" id="modalForm" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" name="return_qs"      id="modal_return_qs"      value="">
                        <input type="hidden" name="edit_mode"      id="modal_edit_mode"      value="0">
                        <input type="hidden" name="existing_image" id="modal_existing_image" value="">
                        <input type="hidden" name="cid"            id="modal_cid_hidden"     value="">

                        <div class="row">
                            <div class="col-md-4 mb-3" id="categoryIdContainer" style="display:none;">
                                <label class="form-label form-label-grey">Category ID</label>
                                <input type="text" id="modal_cid_display" class="form-control bg-light" readonly>
                            </div>
                            <div class="col-md-12 mb-3" id="categoryNameContainer">
                                <label class="form-label form-label-grey">
                                    Category Name <span class="text-danger">*</span>
                                </label>
                                <select name="cname_select" id="modal_cname_select" class="form-select mb-2">
                                    <option value="__NEW__">── Type a New Category Name ──</option>
                                    <?php foreach ($distinctCategories as $catName): ?>
                                        <option value="<?= htmlspecialchars($catName) ?>">
                                            <?= htmlspecialchars($catName) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div id="newCategoryNameWrapper">
                                    <input type="text" name="cname_new" id="modal_cname_new"
                                           class="form-control" placeholder="e.g. CCTV Cameras" maxlength="50">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label form-label-grey">Brand <span class="text-danger">*</span></label>
                                <select name="brandid" id="modal_brandid" class="form-select" required>
                                    <option value="" disabled selected>— Select Brand —</option>
                                    <?php foreach ($brandOptions as $opt): ?>
                                        <option value="<?= htmlspecialchars($opt['brandid']) ?>">
                                            <?= htmlspecialchars($opt['brandname']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <small class="text-muted" id="brandWarningMsg">Brand is mandatory.</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label form-label-grey">Status <span class="text-danger">*</span></label>
                                <select name="status" id="modal_status" class="form-select" required>
                                    <option value="Active">Stock-In</option>
                                    <option value="Inactive">Stock-Out</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
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
                                <label class="form-label form-label-grey">Category Image</label>
                                <input type="file" name="category_image" id="modal_category_image"
                                       class="form-control" accept=".jpg,.jpeg,.png,.gif,.webp">
                                <small class="text-muted">Leave empty to keep the current image when editing.</small>
                            </div>
                            <div class="col-md-4 mb-3" id="currentImageWrap" style="display:none;">
                                <label class="form-label form-label-grey">Current Image</label>
                                <div>
                                    <img id="currentImagePreview" src="" alt="Current image" class="current-image-preview">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success text-white" id="modalSubmitBtn">
                            Save Category
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
                    Are you sure you want to permanently delete this category? This cannot be undone.
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
    <div id="imgLightbox" role="dialog" aria-modal="true" aria-label="Category image viewer">
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
function initCategories() {

    /* ────────────────────────────────────────────────────────
       ELEMENT REFS FOR MODAL POPULATION
    ──────────────────────────────────────────────────────── */
    const modalTitle          = document.getElementById('categoryModalLabel');
    const modalSubmitBtn      = document.getElementById('modalSubmitBtn');
    const modalForm           = document.getElementById('modalForm');
    const catIdContainer      = document.getElementById('categoryIdContainer');
    const catIdDisplay        = document.getElementById('modal_cid_display');
    const catIdHidden         = document.getElementById('modal_cid_hidden');
    const brandSelect         = document.getElementById('modal_brandid');
    const statusSelect        = document.getElementById('modal_status');
    const cnameSelect         = document.getElementById('modal_cname_select');
    const cnameNew            = document.getElementById('modal_cname_new');
    const currentImageWrap    = document.getElementById('currentImageWrap');
    const currentImagePreview = document.getElementById('currentImagePreview');
    const categoryImageInput  = document.getElementById('modal_category_image');

    /* ────────────────────────────────────────────────────────
       MODAL INSTANCE ACCESS
       IMPORTANT: do NOT cache a single Modal instance for the page's
       lifetime. AdminCrud.closeModalSafely() disposes the Bootstrap
       Modal instance once it's confirmed hidden (see admin-crud.js),
       so any long-lived cached reference here would go stale and
       reproduce the "page dims, no dialog shown, only a refresh
       fixes it" bug. Always fetch/create the instance fresh at the
       point of use via getOrCreateInstance().
    ──────────────────────────────────────────────────────── */
    const categoryModalEl = document.getElementById('categoryModal');
    function getCategoryModal() {
        return bootstrap.Modal.getOrCreateInstance(categoryModalEl);
    }

    /* ────────────────────────────────────────────────────────
       CAPTURE-PHASE BLUR (primary focus-retention fix)
       Bootstrap's own dismiss handler for [data-bs-dismiss="modal"]
       is attached via delegated event listener on `document`, in the
       BUBBLE phase. Blurring on hide.bs.modal (fired synchronously
       from inside that same handler, once Bootstrap has already
       started processing the click) is too late in some cases —
       there's still a window where the just-clicked button is the
       focused element when aria-hidden gets applied moments later,
       which is exactly what triggers Chrome's "Blocked aria-hidden on
       an element because its descendant retained focus" warning.
       The robust fix is to intercept the SAME click event in the
       CAPTURE phase, on `document`, which always runs before any
       bubble-phase listener — including Bootstrap's own — gets a
       chance to run. By the time Bootstrap's handler (or ours below)
       even sees the click, focus has already left the button. No
       setTimeout, no race, no dependency on event ordering between
       Bootstrap and our own code — it fires first by construction,
       regardless of which dismiss control (header close button,
       footer Close/Cancel button, etc.) triggered it.
    ──────────────────────────────────────────────────────── */
    document.addEventListener('click', function (e) {
        const dismissEl = e.target.closest('[data-bs-dismiss="modal"]');
        if (dismissEl) {
            dismissEl.blur();
        }
    }, true); // capture = true

    /* ────────────────────────────────────────────────────────
       INERT-BASED MODAL FOCUS FIX (secondary safety net)
       The capture-phase blur above handles clicks on elements with
       data-bs-dismiss="modal". This inert layer stays as a backstop
       for any OTHER path that can hide a modal without going through
       that click handler at all — e.g. a JS-driven .hide() call,
       Escape-key dismissal, or backdrop clicks — where focus could
       still be resting on some element inside the modal when it
       closes. Setting `inert` the instant hide.bs.modal fires
       (synchronously, no setTimeout) makes the browser itself refuse
       to let any element inside the modal hold or receive focus, so
       by the time aria-hidden is applied later, nothing focused can
       be in there. inert is cleared again on shown.bs.modal so the
       modal is fully interactive the next time it opens.
    ──────────────────────────────────────────────────────── */
    ['#categoryModal', '#deleteConfirmationModal'].forEach(function (sel) {
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
       ORPHAN BACKDROP GUARD
       Defensive cleanup only — this no longer does its own DOM
       surgery on Bootstrap's backdrop node. Ripping a
       .modal-backdrop out of the DOM directly (outside Bootstrap's
       own hide()/dispose() lifecycle) leaves any live Modal instance
       holding a stale reference to a node that no longer exists,
       which corrupts its internal state for the next .show() call.
       admin-crud.js's closeModalSafely() now handles the real fix
       (blur-before-hide + dispose-after-hidden); this function just
       mops up anything that still slips through.
    ──────────────────────────────────────────────────────── */
    function cleanupOrphanBackdrops() {
        if (document.querySelector('.modal.show')) return;

        document.querySelectorAll('.modal-backdrop').forEach(function (el) { el.remove(); });
        document.body.classList.remove('modal-open');
        document.body.style.removeProperty('padding-right');
        document.body.style.removeProperty('overflow');

        // If any Modal instance still exists for these elements, dispose it
        // so it can't hand out corrupted state on the next getOrCreateInstance().
        [categoryModalEl, document.getElementById('deleteConfirmationModal')].forEach(function (el) {
            if (!el) return;
            const inst = bootstrap.Modal.getInstance(el);
            if (inst) inst.dispose();
        });
    }
    document.addEventListener('hidden.bs.modal', cleanupOrphanBackdrops);

    /* ────────────────────────────────────────────────────────
       IMAGE LIGHTBOX
    ──────────────────────────────────────────────────────── */
    const lightbox   = document.getElementById('imgLightbox');
    const lbImage    = document.getElementById('lbImage');
    const closeLbBtn = document.getElementById('closeLightbox');

    document.addEventListener('click', function (e) {
        const thumb = e.target.closest('.cat-thumb[data-lb-src]');
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
       FILE INPUT STATE GUARD
    ──────────────────────────────────────────────────────── */
    function clearCategoryImageInput() {
        categoryImageInput.value = '';
    }
    categoryModalEl.addEventListener('hidden.bs.modal', clearCategoryImageInput);

    /* ────────────────────────────────────────────────────────
       INITIALIZE CRUD CONTROLLER
    ──────────────────────────────────────────────────────── */
    new AdminCrud({
        endpoint: 'categories.php',
        tableSelector: '#categoryTableBody',
        rowSelector: '.category-row',
        formSelector: '#modalForm',
        modalSelector: '#categoryModal',
        deleteModalSelector: '#deleteConfirmationModal',
        deleteExecutionSelector: '#modalDeleteExecutionLink',
        statsSelector: '.brand-stat-card',
        visibleCountSelector: '#visibleCount',
        searchInputSelector: '#categorySearchInput',
        brandFilterSelector: '#filterBrand',
        statusFilterSelector: '#filterStatus',
        perPageSelector: '#perPageSelect',
        paginationSelector: '#paginationButtons',
        emptyStateColspan: 6,
        emptyStateText: 'No categories match your search or filters.',
        matchRow: function(row, q, status, brand) {
            const haystack = (row.dataset.cid || '') + ' ' + (row.dataset.cname || '') + ' ' + (row.dataset.brandname || '');
            if (q && haystack.toLowerCase().indexOf(q) === -1) return false;
            if (brand && row.dataset.brandid !== brand) return false;
            if (status && row.dataset.status !== status) return false;
            return true;
        },
        onAddNewClick: function() {
            cleanupOrphanBackdrops();
            modalForm.reset();
            clearCategoryImageInput();
            document.getElementById('modal_edit_mode').value      = '0';
            document.getElementById('modal_existing_image').value = '';
            catIdHidden.value  = '';
            catIdDisplay.value = '';
            catIdContainer.style.display   = 'none';
            cnameSelect.value              = '__NEW__';
            cnameNew.value                 = '';
            brandSelect.value              = '';
            statusSelect.value             = 'Active';
            document.getElementById('modal_display_status').value = '1';
            currentImageWrap.style.display = 'none';
            modalTitle.innerText           = 'Create Category';
            modalSubmitBtn.innerText       = 'Save Category';

            getCategoryModal().show();
        },
        onEditClick: function(btn) {
            cleanupOrphanBackdrops();
            modalTitle.innerText     = 'Edit Category';
            modalSubmitBtn.innerText = 'Save Changes';
            document.getElementById('modal_edit_mode').value      = '1';
            document.getElementById('modal_existing_image').value = btn.dataset.image || '';

            clearCategoryImageInput();

            catIdHidden.value  = btn.dataset.id;
            catIdDisplay.value = btn.dataset.id;
            catIdContainer.style.display = 'block';

            const existingOpt = Array.from(cnameSelect.options).find(function (o) {
                return o.value === btn.dataset.name;
            });
            cnameSelect.value = existingOpt ? btn.dataset.name : '__NEW__';
            cnameNew.value    = btn.dataset.name;

            brandSelect.value  = btn.dataset.brandid || '';
            statusSelect.value = btn.dataset.status || 'Active';
            document.getElementById('modal_display_status').value = btn.dataset.displayStatus || '1';

            if (btn.dataset.imageUrl) {
                currentImagePreview.src        = btn.dataset.imageUrl;
                currentImageWrap.style.display = 'block';
            } else {
                currentImageWrap.style.display = 'none';
            }

            getCategoryModal().show();
        }
    });

}
if (document.readyState === 'loading') {
    document.addEventListener("DOMContentLoaded", initCategories);
} else {
    initCategories();
}
</script>
</div><!-- /.content-body -->
<?php
if (!isset($_GET['partial'])) {
    include 'footer.php';
}
?>