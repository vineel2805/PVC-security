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

// Finds an existing category row for this brand matching the given name
// (case/whitespace-insensitive). If none exists for this brand, auto-creates
// one using the same ID pattern as categories.php — and, if some OTHER brand
// already has a category with this same name and an image on file, reuses
// that image path so the new row isn't left without a picture.
function resolveOrCreateCategory($pdo, $brandid, $categoryName) {
    $target = normalizeCategoryName($categoryName);

    // Pull every category once and scan in PHP, since name matching needs
    // whitespace/case normalization that isn't reliable to do purely in SQL.
    $stmt = $pdo->query("SELECT cid, cname, brandid, cimage FROM category");
    $rows = $stmt->fetchAll();

    $reusableImage = '';
    foreach ($rows as $row) {
        if (normalizeCategoryName($row['cname']) !== $target) {
            continue;
        }
        if ((string)$row['brandid'] === (string)$brandid) {
            // Already exists for this exact brand — just reuse it, nothing to insert.
            return $row['cid'];
        }
        // Same category name under a different brand — remember its image
        // (first non-empty one found) in case we need to create a new row.
        if ($reusableImage === '' && !empty($row['cimage'])) {
            $reusableImage = $row['cimage'];
        }
    }

    // Not found for this brand — create it (same ID pattern as categories.php)
    $maxStmt = $pdo->prepare("
        SELECT MAX(CAST(SUBSTRING(cid, LOCATE('-C', cid) + 2) AS UNSIGNED))
        FROM category WHERE brandid = :brandid
    ");
    $maxStmt->execute([':brandid' => $brandid]);
    $nextNum = (int)$maxStmt->fetchColumn() + 1;
    $newCid  = strtoupper($brandid) . '-C' . str_pad($nextNum, 2, '0', STR_PAD_LEFT);

    if (strlen($newCid) > 15) {
        throw new RuntimeException('Auto-generated Category ID exceeds the 15-character schema limit.');
    }

    $insStmt = $pdo->prepare("
        INSERT INTO category (cid, cname, cimage, brandid, status, display_status)
        VALUES (:cid, :cname, :cimage, :brandid, 'Active', 1)
    ");
    $insStmt->execute([
        ':cid'     => $newCid,
        ':cname'   => $categoryName,
        ':cimage'  => $reusableImage, // '' if no other brand had this category with an image
        ':brandid' => $brandid,
    ]);

    return $newCid;
}

// Looks for an existing product with the same NAME (case/whitespace-insensitive,
// same normalization used for categories) already sitting under this exact
// brand + category. Returns that product's pid if found, otherwise null.
// $excludePid lets edit-mode saves ignore the row being edited itself.
function findDuplicateProductPid($pdo, $brandid, $pcat, $pname, $excludePid = '') {
    $target = normalizeCategoryName($pname);

    $stmt = $pdo->prepare("SELECT pid, pname FROM products WHERE brandid = :brandid AND pcat = :pcat");
    $stmt->execute([':brandid' => $brandid, ':pcat' => $pcat]);

    foreach ($stmt->fetchAll() as $row) {
        if ($excludePid !== '' && (string)$row['pid'] === (string)$excludePid) {
            continue; // this is the same product being edited — not a duplicate of itself
        }
        if (normalizeCategoryName($row['pname']) === $target) {
            return $row['pid'];
        }
    }
    return null;
}

// FIX #1: added missing "/" so PHP actually goes UP one directory from /admin
// before appending uploads/products/ — the original string concatenation
// ("admin" . "../uploads/products/") produced "admin../uploads/products/",
// a bogus sibling folder, not a real "up one level" path.
$uploadDir     = __DIR__ . '/../uploads/products/';

// FIX #2: uploads/ is a sibling of admin/, not a child of it. Browsers resolve
// relative src="uploads/products/x.jpg" against the CURRENT page URL
// (/admin/products.php), which wrongly points to /admin/uploads/products/x.jpg.
// Using "../uploads/products/" (page-relative, going UP one level from admin/)
// makes the <img> tag resolve to the real physical location of the files,
// regardless of what your domain root actually is.
$uploadUrlBase = '../uploads/products/';

if (!is_dir($uploadDir)) {
    @mkdir($uploadDir, 0755, true);
}

// 1. HANDLE DELETE
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    try {
        $pid  = $_GET['id'];
        $stmt = $pdo->prepare("SELECT pimage FROM products WHERE pid = :id");
        $stmt->execute([':id' => $pid]);
        $row  = $stmt->fetch();

        $stmt = $pdo->prepare("DELETE FROM products WHERE pid = :id");
        $stmt->execute([':id' => $pid]);

        // pimage stores a page-relative path e.g. ../uploads/products/prod_xyz.jpg
        // __DIR__ . '/' . $row['pimage'] resolves it back to the real file on disk
        // (since __DIR__ is the admin/ folder, "/../uploads/..." lands correctly)
        if ($row && !empty($row['pimage'])) {
            $diskPath = __DIR__ . '/' . $row['pimage'];
            if (is_file($diskPath)) {
                @unlink($diskPath);
            }
        }

        if (isset($_GET['ajax']) || (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')) {
            echo json_encode(['status' => 'success', 'msg' => 'Product deleted successfully.']);
            exit;
        }
        header("Location: " . strtok($_SERVER["REQUEST_URI"], '?') . "?success_msg=deleted");
        exit;
    } catch (PDOException $e) {
        $error = 'Failed to delete record: ' . $e->getMessage();
    }
}

// 2. POST-REDIRECT SUCCESS FLAG
if (isset($_GET['success_msg'])) {
    $success = true;
}

// 3. HANDLE FORM SUBMISSIONS
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $pname          = trim($_POST['pname']        ?? '');
    $pdescription   = trim($_POST['pdescription'] ?? '');
    $brandid        = trim($_POST['brandid']      ?? '');
    $status         = trim($_POST['status']       ?? 'Active');
    $display_status = isset($_POST['display_status']) ? (int)$_POST['display_status'] : 1;
    $edit_mode      = !empty($_POST['edit_mode']);
    $existing_image = trim($_POST['existing_image'] ?? '');
    $pid            = $edit_mode ? trim($_POST['pid'] ?? '') : '';
    $oldPid         = $pid; // preserved for the WHERE clause / comparisons below

    // CATEGORY IS NOW NAME-BASED IN BOTH ADD AND EDIT MODE.
    // The category select always posts a category NAME (unique across brands);
    // resolveOrCreateCategory() below finds-or-creates the matching row for
    // whichever brand is selected, in both add and edit flows.
    $pcat_select = trim($_POST['pcat_select'] ?? '');
    $pcat_new    = trim($_POST['pcat_new']    ?? '');
    $pcatName    = ($pcat_select === '__NEW__') ? $pcat_new : $pcat_select;
    $pcat        = null; // resolved below

    $pcatCheckValue = $pcatName;

    if ($pname === '' || $pcatCheckValue === '' || $brandid === '') {
        $error = 'Please fill in all required fields marked with *.';
    } else {

        try {
            $pcat = resolveOrCreateCategory($pdo, $brandid, $pcatName);
        } catch (Exception $e) {
            $error = 'Failed to resolve/create category: ' . $e->getMessage();
        }

        // Block duplicate product names within the same brand + category.
        // Runs for BOTH add and edit (edit excludes the row being edited itself,
        // via $oldPid, so renaming/re-saving the same product isn't flagged).
        if ($error === '') {
            $dupPid = findDuplicateProductPid($pdo, $brandid, $pcat, $pname, $oldPid);
            if ($dupPid !== null) {
                $error = 'A product named "' . $pname . '" already exists in this brand/category. Please use a different name, or edit the existing product instead.';
            }
        }

        if ($error === '' && !$edit_mode) {
            try {
                $maxStmt = $pdo->prepare("
                    SELECT MAX(CAST(SUBSTRING(pid, LOCATE('-P', pid) + 2) AS UNSIGNED))
                    FROM products WHERE pcat = :pcat
                ");
                $maxStmt->execute([':pcat' => $pcat]);
                $nextNum = (int)$maxStmt->fetchColumn() + 1;
                $pid = strtoupper($pcat) . '-P' . str_pad($nextNum, 2, '0', STR_PAD_LEFT);
            } catch (PDOException $e) {
                $error = 'Failed to automatically compute a unique Product ID: ' . $e->getMessage();
            }
        }

        // EDIT MODE + CATEGORY CHANGED: regenerate the Product ID under the
        // new category, using the same numbering pattern as brand-new products,
        // so the ID prefix always reflects the product's current category.
        if ($error === '' && $edit_mode) {
            $curStmt = $pdo->prepare("SELECT pcat FROM products WHERE pid = :pid");
            $curStmt->execute([':pid' => $oldPid]);
            $oldPcat = $curStmt->fetchColumn();

            if ($oldPcat !== false && (string)$oldPcat !== (string)$pcat) {
                try {
                    $maxStmt = $pdo->prepare("
                        SELECT MAX(CAST(SUBSTRING(pid, LOCATE('-P', pid) + 2) AS UNSIGNED))
                        FROM products WHERE pcat = :pcat
                    ");
                    $maxStmt->execute([':pcat' => $pcat]);
                    $nextNum = (int)$maxStmt->fetchColumn() + 1;
                    $pid = strtoupper($pcat) . '-P' . str_pad($nextNum, 2, '0', STR_PAD_LEFT);
                } catch (PDOException $e) {
                    $error = 'Failed to compute a new Product ID for the new category: ' . $e->getMessage();
                }
            }
        }

        if ($error === '' && strlen($pid) > 25) {
            $error = 'The computed automatic Product ID exceeds valid database schema lengths.';
        }

        if ($error === '') {
            // $existing_image already holds the root-relative path stored in DB
            $pimage = $existing_image;

            if (!empty($_FILES['product_image']['name'])) {
                $fileTmp    = $_FILES['product_image']['tmp_name'];
                $fileName   = $_FILES['product_image']['name'];
                $fileErr    = $_FILES['product_image']['error'];
                $ext        = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                $allowedExt = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

                if ($fileErr !== UPLOAD_ERR_OK) {
                    $error = 'Image upload failed. Please try again.';
                } elseif (!in_array($ext, $allowedExt, true)) {
                    $error = 'Image must be one of: ' . implode(', ', $allowedExt) . '.';
                } else {
                    // Uses $pid, which by this point already reflects any
                    // category-driven regeneration above.
                    $newFileName    = 'prod_' . preg_replace('/[^a-zA-Z0-9_-]/', '', strtolower($pid)) . '_' . time() . '.' . $ext;
                    $fullSavePath   = $uploadDir . $newFileName;
                    // Store root-relative path in DB so it can be used directly as src
                    // from any page, regardless of that page's folder depth.
                    $newRelativePath = $uploadUrlBase . $newFileName;

                    if (move_uploaded_file($fileTmp, $fullSavePath)) {
                        // Delete old image if editing and old path exists on disk
                        if ($edit_mode && $existing_image) {
                            $oldDiskPath = __DIR__ . '/' . $existing_image;
                            if (is_file($oldDiskPath)) {
                                @unlink($oldDiskPath);
                            }
                        }
                        $pimage = $newRelativePath;
                    } else {
                        $error = 'Could not save uploaded image.';
                    }
                }
            } elseif (!$edit_mode && $existing_image === '') {
                $pimage = '';
            }
        }

        if ($error === '') {
            try {
                if ($edit_mode) {
                    // pid may have changed (category switch) — update it too,
                    // keyed off the ORIGINAL pid in the WHERE clause.
                    $stmt = $pdo->prepare("
                        UPDATE products SET
                            pid          = :new_pid,
                            pname        = :pname,
                            pdescription = :pdescription,
                            pcat         = :pcat,
                            brandid      = :brandid,
                            pimage       = :pimage,
                            status       = :status,
                            display_status = :display_status
                        WHERE pid = :old_pid
                    ");
                    $stmt->execute([
                        ':new_pid'      => $pid,
                        ':pname'        => $pname,
                        ':pdescription' => $pdescription,
                        ':pcat'         => $pcat,
                        ':brandid'      => $brandid,
                        ':pimage'       => $pimage,
                        ':status'       => $status,
                        ':display_status'=> $display_status,
                        ':old_pid'      => $oldPid,
                    ]);
                    if (isset($_POST['ajax']) || (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')) {
                        echo json_encode(['status' => 'success', 'msg' => 'Product updated successfully.']);
                        exit;
                    }
                    header("Location: " . strtok($_SERVER["REQUEST_URI"], '?') . "?success_msg=updated");
                    exit;
                } else {
                    $stmt = $pdo->prepare("
                        INSERT INTO products (pid, pname, pdescription, pcat, brandid, pimage, status, display_status)
                        VALUES (:pid, :pname, :pdescription, :pcat, :brandid, :pimage, :status, :display_status)
                    ");
                    $stmt->execute([
                        ':pid'          => $pid,
                        ':pname'        => $pname,
                        ':pdescription' => $pdescription,
                        ':pcat'         => $pcat,
                        ':brandid'      => $brandid,
                        ':pimage'       => $pimage,
                        ':status'       => $status,
                        ':display_status'=> $display_status,
                    ]);
                    if (isset($_POST['ajax']) || (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')) {
                        echo json_encode(['status' => 'success', 'msg' => 'Product inserted successfully.']);
                        exit;
                    }
                    header("Location: " . strtok($_SERVER["REQUEST_URI"], '?') . "?success_msg=inserted");
                    exit;
                }
            } catch (PDOException $e) {
                if ((int)$e->errorInfo[1] === 1062) {
                    // If the optional DB-level uniq_brand_cat_name constraint exists
                    // (see dup_check_and_migration.sql), this is what catches any
                    // duplicate that slipped past the app-level check above due to
                    // a race between two near-simultaneous saves.
                    if (isset($e->errorInfo[2]) && strpos($e->errorInfo[2], 'uniq_brand_cat_name') !== false) {
                        $error = 'A product with this name already exists in this brand/category. Please use a different name, or edit the existing product instead.';
                    } else {
                        $error = 'A transaction race condition occurred. Please resubmit.';
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

// 4. FETCH ALL RECORDS
$products      = [];
$total_records = 0;
try {
    $stmt = $pdo->query("
        SELECT p.*, c.cname, b.brandname
        FROM products p
        LEFT JOIN category c ON c.cid  = p.pcat
        LEFT JOIN brands   b ON b.brandid = p.brandid
        ORDER BY p.pname ASC
    ");
    $products      = $stmt->fetchAll();
    $total_records = count($products);
} catch (PDOException $e) {
    $error = 'Could not load records: ' . $e->getMessage();
}

// 5. FETCH BRANDS
$brandOptions = [];
try {
    $stmt = $pdo->query("SELECT brandid, brandname FROM brands ORDER BY brandname ASC");
    $brandOptions = $stmt->fetchAll();
} catch (PDOException $e) { /* non-fatal */ }

// 6. FETCH DISTINCT CATEGORY NAMES (unique across ALL brands, for the product dropdown)
$distinctCategoryNames = [];
try {
    $stmt = $pdo->query("SELECT DISTINCT cname FROM category WHERE cname != '' ORDER BY cname ASC");
    $distinctCategoryNames = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) { /* non-fatal */ }

// 7. COUNT ACTIVE / INACTIVE
$activeCount   = 0;
$inactiveCount = 0;
foreach ($products as $p) {
    if ($p['status'] === 'Active') $activeCount++;
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
    .product-stat-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.08);
        padding: 0;
        display: flex;
        align-items: stretch;
        overflow: hidden;
        margin-bottom: 24px;
    }
    .psc-icon-wrap {
        background: #4CAF50;
        width: 90px;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .psc-icon-wrap i { font-size: 34px; color: #fff; }
    .psc-body {
        flex: 1;
        padding: 16px 22px 12px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    .psc-label { font-size: 13px; color: #888; font-weight: 500; margin-bottom: 2px; letter-spacing: 0.2px; }
    .psc-number { font-size: 32px; font-weight: 700; color: #1a1a1a; line-height: 1.1; }
    .psc-footer {
        border-top: 1px solid #f0f0f0;
        padding: 8px 22px;
        display: flex;
        align-items: center;
        gap: 16px;
        flex-wrap: wrap;
    }
    .psc-footer-item { display: flex; align-items: center; gap: 6px; font-size: 12px; color: #666; }
    .psc-footer-item .dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
    .dot-active   { background: #4CAF50; }
    .dot-inactive { background: #f44336; }

    /* ── Toolbar row (search + filters + add button) ────────── */
    .products-toolbar {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }
    .products-toolbar .search-wrap {
        position: relative;
        flex: 1;
        min-width: 200px;
        max-width: 340px;
    }
    .products-toolbar .search-wrap i {
        position: absolute;
        left: 11px;
        top: 50%;
        transform: translateY(-50%);
        color: #aaa;
        font-size: 14px;
        pointer-events: none;
    }
    .products-toolbar .search-input {
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
    .products-toolbar .search-input:focus { border-color: #4CAF50; box-shadow: 0 0 0 2px rgba(76,175,80,.12); }
    .products-toolbar .filter-select {
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
    .products-toolbar .filter-select:focus { border-color: #4CAF50; box-shadow: 0 0 0 2px rgba(76,175,80,.12); }
    .products-toolbar .filter-select:disabled { background-color: #f8f9fa; color: #aaa; cursor: not-allowed; }
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
    #modalForm .form-select,
    #modalForm textarea { color: #1a1a1a !important; font-weight: 500; border-color: #cbd5e1; }
    #modalForm .form-control:focus,
    #modalForm .form-select:focus,
    #modalForm textarea:focus { color: #000 !important; border-color: #666; }
    #modalForm .form-select:disabled { background-color: #f1f5f9 !important; color: #94a3b8 !important; cursor: not-allowed; opacity: 0.75; }

    /* ── Table helpers ──────────────────────────────────────── */
    .product-thumb {
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
    .product-thumb:hover { transform: scale(1.08); box-shadow: 0 4px 14px rgba(0,0,0,.12); }
    .current-image-preview {
        width: 60px; height: 60px; object-fit: contain;
        border: 1px solid #cbd5e1; border-radius: 6px; padding: 4px; background: #fff;
    }
    .status-badge { padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 600; }
    .status-active   { background-color: #d4edda; color: #155724; }
    .status-inactive { background-color: #f8d7da; color: #721c24; }
    .description-cell { max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block; }
    #catLoadingSpinner { display: none; font-size: 12px; color: #6c757d; }

    /* ── No-image placeholder ───────────────────────────────── */
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
    .pagination-controls .page-btn:hover { border-color: #4CAF50; color: #4CAF50; }
    .pagination-controls .page-btn.active { background: #4CAF50; border-color: #4CAF50; color: #fff; }
    .pagination-controls .page-btn:disabled { opacity: .45; cursor: not-allowed; pointer-events: none; }
    .per-page-select {
        height: 32px; padding: 0 24px 0 8px; border: 1px solid #dee2e6;
        border-radius: 6px; font-size: 13px; color: #495057;
        background: #fff url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23999' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e") no-repeat right 6px center / 10px;
        appearance: none; cursor: pointer; outline: none;
    }
    .per-page-select:focus { border-color: #4CAF50; }

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

    /* ── Empty / no-results row ─────────────────────────────── */
    .empty-state-row td {
        padding: 40px 0 !important; text-align: center;
    }
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
                 WIDE STAT CARD — Total Products
            ══════════════════════════════════════════════ -->
            <div class="product-stat-card">
                <div class="psc-icon-wrap">
                    <i class="fa-solid fa-boxes-stacked"></i>
                </div>
                <div style="flex:1; display:flex; flex-direction:column;">
                    <div class="psc-body">
                        <div class="psc-label">Total Products</div>
                        <div class="psc-number" id="statTotalCount"><?= $total_records ?></div>
                    </div>
                    <div class="psc-footer">
                        <div class="psc-footer-item">
                            <span class="dot dot-active"></span>
                            In Stock: <strong><?= $activeCount ?></strong>
                        </div>
                        <div class="psc-footer-item">
                            <span class="dot dot-inactive"></span>
                            Out of Stock: <strong><?= $inactiveCount ?></strong>
                        </div>
                        <div class="psc-footer-item ms-auto">
                            <i class="fa fa-clock me-1"></i> Last updated just now
                        </div>
                    </div>
                </div>
            </div>

            <!-- ══════════════════════════════════════════════
                 PRODUCTS TABLE CARD
            ══════════════════════════════════════════════ -->
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <h4 class="card-title mb-0">
                            <i class="fa-solid fa-box me-2 text-primary"></i>Products
                        </h4>

                        <!-- ── Toolbar: search + brand filter + category filter + add btn ── -->
                        <div class="products-toolbar">
                            <!-- Search bar -->
                            <div class="search-wrap">
                                <i class="fa fa-search"></i>
                                <input type="text" id="productSearchInput" class="search-input"
                                       placeholder="Search by ID, name, brand, category…">
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

                            <!-- Category filter (unlocked after brand chosen) -->
                            <select id="filterCategory" class="filter-select" disabled title="Filter by category">
                                <option value="">Select brand first</option>
                            </select>

                            <div class="filter-separator d-none d-sm-block"></div>

                            <!-- Add button -->
                            <button type="button" class="btn btn-success btn-sm text-white"
                                    data-bs-toggle="modal" data-bs-target="#productModal" id="addNewBtn">
                                <i class="fa fa-plus me-1"></i> Add Product
                            </button>
                        </div>
                    </div>

                    <div class="card-body">
                        <!-- Result count indicator -->
                        <div class="d-flex align-items-center mb-2 gap-3 flex-wrap">
                            <p class="mb-0 text-muted small" id="resultsLabel">
                                Showing <strong id="visibleCount"><?= $total_records ?></strong>
                                of <strong><?= $total_records ?></strong> products
                            </p>

                        </div>

                        <div class="table-responsive">
                            <table class="table table-responsive-md vertical-middle" id="productsTable">
                                <thead>
                                    <tr>
                                        <th style="width:23%;">Product Name</th>
                                        <th style="width:25%;">Description</th>
                                        <th style="width:11%;">Category</th>
                                        <th style="width:11%;">Brand</th>
                                        <th style="width:7%;">Image</th>
                                        <th style="width:10%;">Stock Status</th>
                                        <th style="width:10%;">Website Status</th>
                                        <th style="width:13%; text-align:center;">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="productsTableBody">
                                    <?php if (empty($products)): ?>
                                        <tr class="empty-state-row">
                                            <td colspan="8">
                                                <div class="empty-state-icon"><i class="fa-solid fa-box-open"></i></div>
                                                <p class="text-muted mb-0">No products registered yet.</p>
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($products as $p):
                                            // Use pimage directly as stored in DB (root-relative path)
                                            $imgSrc    = !empty($p['pimage']) ? htmlspecialchars($p['pimage']) : '';
                                            $imgExists = $imgSrc !== '';
                                        ?>
                                        <tr class="product-row"
                                            data-pid="<?= strtolower(htmlspecialchars($p['pid'])) ?>"
                                            data-pname="<?= strtolower(htmlspecialchars($p['pname'])) ?>"
                                            data-brandid="<?= htmlspecialchars($p['brandid']) ?>"
                                            data-catid="<?= htmlspecialchars($p['pcat']) ?>"
                                            data-cname="<?= strtolower(htmlspecialchars($p['cname'] ?? '')) ?>"
                                            data-brandname="<?= strtolower(htmlspecialchars($p['brandname'] ?? '')) ?>">
                                            <td>
                                                <span class="text-dark" style="font-size:14px;">
                                                    <?= htmlspecialchars($p['pname']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="text-muted small description-cell"
                                                      title="<?= htmlspecialchars($p['pdescription'] ?? '') ?>">
                                                    <?= !empty($p['pdescription'])
                                                        ? htmlspecialchars($p['pdescription'])
                                                        : '<span class="text-muted fst-italic">— No description —</span>' ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="text-dark small">
                                                    <?= $p['cname']
                                                        ? htmlspecialchars($p['cname'])
                                                        : '<span class="text-muted">— none —</span>' ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="text-dark small">
                                                    <?= $p['brandname']
                                                        ? htmlspecialchars($p['brandname'])
                                                        : '<span class="text-muted">— none —</span>' ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if ($imgExists): ?>
                                                    <img src="<?= $imgSrc ?>"
                                                         alt="<?= htmlspecialchars($p['pname']) ?>"
                                                         class="product-thumb"
                                                         data-lb-src="<?= $imgSrc ?>"
                                                         data-lb-title="<?= htmlspecialchars($p['pname']) ?>"
                                                         title="Click to enlarge">
                                                <?php else: ?>
                                                    <div class="no-img-thumb" title="No image uploaded">
                                                        <i class="fa-regular fa-image"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="status-badge <?= $p['status'] === 'Active' ? 'status-active' : 'status-inactive' ?>">
                                                    <?= $p['status'] === 'Active' ? 'Stock' : 'Out of Stock' ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="status-badge <?= (int)$p['display_status'] === 1 ? 'status-active' : 'status-inactive' ?>">
                                                    <?= (int)$p['display_status'] === 1 ? 'Visible' : 'Hidden' ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex justify-content-center gap-2">
                                                    <button type="button"
                                                            class="btn btn-primary btn-action-large edit-btn"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#productModal"
                                                            data-id="<?= htmlspecialchars($p['pid']) ?>"
                                                            data-name="<?= htmlspecialchars($p['pname']) ?>"
                                                            data-description="<?= htmlspecialchars($p['pdescription'] ?? '') ?>"
                                                            data-category="<?= htmlspecialchars($p['pcat']) ?>"
                                                            data-categoryname="<?= htmlspecialchars($p['cname'] ?? '') ?>"
                                                            data-brandid="<?= htmlspecialchars($p['brandid']) ?>"
                                                            data-status="<?= htmlspecialchars($p['status']) ?>"
                                                            data-display-status="<?= htmlspecialchars($p['display_status']) ?>"
                                                            data-image="<?= htmlspecialchars($p['pimage'] ?? '') ?>"
                                                            data-image-url="<?= $imgSrc ?>"
                                                            title="Edit Record">
                                                        <i class="fa fa-pencil"></i>
                                                    </button>
                                                    <button type="button"
                                                            class="btn btn-danger btn-action-large delete-confirm-trigger"
                                                            data-id="<?= htmlspecialchars($p['pid']) ?>"
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
                            <nav aria-label="Products pagination">
                                <div class="pagination-controls" id="paginationButtons"></div>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    <!-- ══════════════════════════════════════════════════════════
         PRODUCT ADD / EDIT MODAL
    ══════════════════════════════════════════════════════════ -->
    <div class="modal fade" id="productModal" tabindex="-1"
         aria-labelledby="productModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="productModalLabel">Create Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form method="POST" id="modalForm" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" name="edit_mode"       id="modal_edit_mode"       value="0">
                        <input type="hidden" name="existing_image"  id="modal_existing_image"  value="">
                        <input type="hidden" name="pid"             id="modal_pid_hidden"      value="">

                        <div class="row">
                            <div class="col-md-12 mb-3" id="productNameContainer">
                                <label class="form-label form-label-grey">
                                    Product Name <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="pname" id="modal_pname"
                                       class="form-control" placeholder="e.g. Hikvision 2MP Dome Camera"
                                       maxlength="100" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label form-label-grey">Description</label>
                                <textarea name="pdescription" id="modal_pdescription" class="form-control"
                                          rows="3" placeholder="Enter product description here…"
                                          maxlength="1000"></textarea>
                                <small class="text-muted">Optional. Max 1000 characters.</small>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label form-label-grey">
                                    Brand <span class="text-danger">*</span>
                                </label>
                                <select name="brandid" id="modal_brandid" class="form-select" required>
                                    <option value="">— Select Brand —</option>
                                    <?php foreach ($brandOptions as $opt): ?>
                                        <option value="<?= htmlspecialchars($opt['brandid']) ?>">
                                            <?= htmlspecialchars($opt['brandname']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3" id="categorySelectContainer">
                                <label class="form-label form-label-grey">
                                    Category <span class="text-danger">*</span>
                                </label>

                                <!-- Unique category NAMES across all brands. If the chosen name doesn't
                                     already exist under the chosen brand, the backend creates it there
                                     automatically (same as the Categories page "type a new name" flow).
                                     Editable in BOTH add and edit mode — changing this on an existing
                                     product re-files it under the new category and regenerates its ID. -->
                                <select name="pcat_select" id="modal_pcat_select" class="form-select mb-2" required>
                                    <option value="">— Select Category —</option>
                                    <option value="__NEW__">── Type a New Category Name ──</option>
                                    <?php foreach ($distinctCategoryNames as $catName): ?>
                                        <option value="<?= htmlspecialchars($catName) ?>">
                                            <?= htmlspecialchars($catName) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div id="newCategoryNameWrapper" style="display:none;">
                                    <input type="text" name="pcat_new" id="modal_pcat_new"
                                           class="form-control" placeholder="e.g. NVR" maxlength="50">
                                </div>
                                <small class="text-muted" id="catWarningMsg">
                                    Pick an existing category name, or type a new one — it will be created
                                    under the selected brand automatically if it doesn't exist there yet.
                                </small>
                                <small id="catLoadingSpinner"><i class="fa fa-spinner fa-spin me-1"></i>Loading categories…</small>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label form-label-grey">
                                    Status <span class="text-danger">*</span>
                                </label>
                                <select name="status" id="modal_status" class="form-select" required>
                                    <option value="Active">Stock-In</option>
                                    <option value="Inactive">Out of Stock</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label form-label-grey">
                                    Display on Website <span class="text-danger">*</span>
                                </label>
                                <select name="display_status" id="modal_display_status" class="form-select" required>
                                    <option value="1">Visible</option>
                                    <option value="0">Hidden</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label class="form-label form-label-grey">Product Image</label>
                                <input type="file" name="product_image" id="modal_product_image"
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
                            Save Product
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
                    Are you sure you want to permanently delete this product?
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
    <div id="imgLightbox" role="dialog" aria-modal="true" aria-label="Product image viewer">
        <button class="close-lb" id="closeLightbox" aria-label="Close image viewer">&times;</button>
        <img id="lbImage" src="" alt="">
    </div>

<script>
function initProducts() {

    /* ────────────────────────────────────────────────────────
       ELEMENT REFS FOR MODAL POPULATION
    ──────────────────────────────────────────────────────── */
    const modalTitle           = document.getElementById('productModalLabel');
    const modalSubmitBtn       = document.getElementById('modalSubmitBtn');
    const modalForm            = document.getElementById('modalForm');
    // pid is tracked only via this hidden input (never shown to the user);
    // there is no visible Product ID field or container in the modal anymore.
    const prodIdHidden         = document.getElementById('modal_pid_hidden');

    // Category (unique names across brands; auto-created under the chosen brand server-side)
    const catSelect            = document.getElementById('modal_pcat_select');
    const catNewInput          = document.getElementById('modal_pcat_new');
    const catNewWrapper        = document.getElementById('newCategoryNameWrapper');
    const catWarningMsg        = document.getElementById('catWarningMsg');
    const catLoadingSpinner    = document.getElementById('catLoadingSpinner');

    // FIX #1 (empty/stale dropdown bug): snapshot the full, pristine,
    // server-rendered option list ONCE at page load. lockCategorySelectForEdit
    // (below, now setCategorySelectForEdit) used to wipe catSelect.innerHTML
    // down to a single leftover <option> when editing a product, and nothing
    // ever restored the full list afterward — so reopening "Add Product" in
    // the same page session showed just that one stale option. Now every
    // path that needs the full list restores it from this snapshot first.
    const originalCatSelectHTML = catSelect.innerHTML;

    const brandSelect          = document.getElementById('modal_brandid');
    const statusSelect         = document.getElementById('modal_status');
    const currentImageWrap     = document.getElementById('currentImageWrap');
    const currentImagePreview  = document.getElementById('currentImagePreview');

    /* ────────────────────────────────────────────────────────
       IMAGE LIGHTBOX
    ──────────────────────────────────────────────────────── */
    const lightbox   = document.getElementById('imgLightbox');
    const lbImage    = document.getElementById('lbImage');
    const closeLbBtn = document.getElementById('closeLightbox');

    document.addEventListener('click', function (e) {
        const thumb = e.target.closest('.product-thumb[data-lb-src]');
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
       CATEGORY HELPERS (unique-name select, cross-brand,
       editable in BOTH add and edit mode)
    ──────────────────────────────────────────────────────── */
    function toggleCatNewInput() {
        const showNew = catSelect.value === '__NEW__';
        catNewWrapper.style.display = showNew ? 'block' : 'none';
        if (!showNew) catNewInput.value = '';
    }
    catSelect.addEventListener('change', toggleCatNewInput);

    function unlockCategorySelectForAdd() {
        catSelect.innerHTML = originalCatSelectHTML; // restore full list every time
        catSelect.removeAttribute('disabled');
        catLoadingSpinner.style.display = 'none';
        catWarningMsg.textContent = "Pick an existing category name, or type a new one — it will be created under the selected brand automatically if it doesn't exist there yet.";
        catWarningMsg.style.display = 'block';
    }

    // FIX #2 (category now editable on existing products): previously this
    // function replaced catSelect's entire option list with a single locked
    // <option> holding the category CID, and disabled the field entirely.
    // Category changes now flow through the same name-based select used for
    // Add, and the backend regenerates the Product ID under the new category
    // when it detects pcat changed during an edit.
    function setCategorySelectForEdit(cname) {
        catSelect.innerHTML = originalCatSelectHTML; // full list, not a single stale option
        catSelect.removeAttribute('disabled');

        const match = Array.from(catSelect.options).find(o => o.value === cname);
        if (match) {
            catSelect.value = cname;
            catNewWrapper.style.display = 'none';
            catNewInput.value = '';
        } else {
            // Current category name isn't in the distinct list for some reason —
            // fall back to "type a new name", prefilled with the current value.
            catSelect.value = '__NEW__';
            catNewInput.value = cname;
            catNewWrapper.style.display = 'block';
        }

        catLoadingSpinner.style.display = 'none';
        catWarningMsg.textContent = 'Changing category will assign this product a new Product ID.';
        catWarningMsg.style.display = 'block';
    }

    /* ────────────────────────────────────────────────────────
       INITIALIZE CRUD CONTROLLER
    ──────────────────────────────────────────────────────── */
    new AdminCrud({
        endpoint: 'products.php',
        tableSelector: '#productsTableBody',
        rowSelector: '.product-row',
        formSelector: '#modalForm',
        modalSelector: '#productModal',
        deleteModalSelector: '#deleteConfirmationModal',
        deleteExecutionSelector: '#modalDeleteExecutionLink',
        statsSelector: '.brand-stat-card',
        visibleCountSelector: '#visibleCount',
        searchInputSelector: '#productSearchInput',
        brandFilterSelector: '#filterBrand',
        categoryFilterSelector: '#filterCategory',
        activeFilterBadgeSelector: '#activeFilterBadge',
        activeFilterTextSelector: '#activeFilterText',
        perPageSelector: '#perPageSelect',
        paginationSelector: '#paginationButtons',
        emptyStateColspan: 8,
        emptyStateText: 'No products match your search or filters.',
        matchRow: function(row, q, status, brand, category) {
            const haystack = (row.dataset.pid || '') + ' ' + (row.dataset.pname || '') + ' ' + (row.dataset.brandname || '') + ' ' + (row.dataset.cname || '');
            if (q && haystack.toLowerCase().indexOf(q) === -1) return false;
            if (brand && row.dataset.brandid !== brand) return false;
            if (category && row.dataset.catid !== category) return false;
            return true;
        },
        onBrandFilterChange: function(brandid, allRows) {
            const filterCategory = document.getElementById('filterCategory');
            if (!filterCategory) return;
            filterCategory.innerHTML = '<option value="">All Categories</option>';
            filterCategory.disabled = true;

            if (!brandid) {
                filterCategory.innerHTML = '<option value="">Select brand first</option>';
                return;
            }

            const seen = new Set();
            allRows.forEach(function (row) {
                if (row.dataset.brandid === brandid) {
                    const cid = row.dataset.catid;
                    const cname = row.dataset.cname;
                    if (cid && !seen.has(cid)) {
                        seen.add(cid);
                        const opt = document.createElement('option');
                        opt.value = cid;
                        opt.textContent = cname || cid;
                        filterCategory.appendChild(opt);
                    }
                }
            });

            filterCategory.disabled = (seen.size === 0);
        },
        onAddNewClick: function() {
            modalForm.reset();
            document.getElementById('modal_edit_mode').value      = '0';
            document.getElementById('modal_existing_image').value = '';
            prodIdHidden.value  = '';
            brandSelect.value = '';
            brandSelect.removeAttribute('disabled');

            unlockCategorySelectForAdd(); // restores full option list every time
            catSelect.value = '';
            toggleCatNewInput();

            statusSelect.value = 'Active';
            document.getElementById('modal_display_status').value = '1';
            currentImageWrap.style.display = 'none';
            modalTitle.innerText     = 'Product Registration Form';
            modalSubmitBtn.innerText = 'Save Product';
        },
        onEditClick: function(btn) {
            modalTitle.innerText     = 'Edit Product Info';
            modalSubmitBtn.innerText = 'Save Changes';
            document.getElementById('modal_edit_mode').value      = '1';
            document.getElementById('modal_existing_image').value = btn.dataset.image || '';
            prodIdHidden.value  = btn.dataset.id; // tracked internally, never shown to the user
            document.getElementById('modal_pname').value        = btn.dataset.name;
            document.getElementById('modal_pdescription').value = btn.dataset.description || '';
            brandSelect.value = btn.dataset.brandid || '';
            brandSelect.removeAttribute('disabled');

            const savedCname = btn.dataset.categoryname || '';
            setCategorySelectForEdit(savedCname);

            statusSelect.value = btn.dataset.status || 'Active';
            document.getElementById('modal_display_status').value = btn.dataset.displayStatus || '1';

            if (btn.dataset.imageUrl) {
                currentImagePreview.src = btn.dataset.imageUrl;
                currentImageWrap.style.display = 'block';
            } else {
                currentImageWrap.style.display = 'none';
            }
        }
    });

}
if (document.readyState === 'loading') {
    document.addEventListener("DOMContentLoaded", initProducts);
} else {
    initProducts();
}
</script>
    </div>

    <div class="footer">
        <div class="copyright">
            <p>Copyright &copy; Designed &amp; Developed by
                <a href="https://dexignlab.com/" target="_blank">DexignLab</a> 2023
            </p>
        </div>
    </div>
</div>
<?php
if (!isset($_GET['partial'])) {
    include 'footer.php';
}
?>