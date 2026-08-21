<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

require_once 'config/db.php';
require_once 'config/hero_slides_schema.php';

// Ensure table exists
ensure_hero_slides_table_exists($pdo);

$success       = '';
$error         = '';
$desktop_error = '';
$mobile_error  = '';
$active_modal  = '';
$form_data     = [];

$uploadDir = __DIR__ . '/../uploads/slides/';
if (!is_dir($uploadDir)) {
    @mkdir($uploadDir, 0755, true);
}

/**
 * Proportional-only resize for Hero Slider Banner Images via PHP GD.
 *
 * NEVER crops or distorts the original content.
 * Only downscales proportionally when the image exceeds the max dimensions.
 * Images smaller than the max are saved as-is (after re-encoding for optimization).
 *
 * @param string $srcPath  Source temporary file path
 * @param int    $maxW     Maximum allowed width in pixels
 * @param int    $maxH     Maximum allowed height in pixels
 * @param string $destPath Destination file path on disk
 * @param string $ext      File extension
 * @return bool True on success, false on failure
 */
function process_and_optimize_hero_image($srcPath, $maxW, $maxH, $destPath, $ext) {
    if (!file_exists($srcPath)) {
        return false;
    }

    $ext = strtolower($ext);

    // SVG vector files — just copy, no GD processing needed
    if ($ext === 'svg') {
        return @copy($srcPath, $destPath);
    }

    $imgInfo = @getimagesize($srcPath);
    if (!$imgInfo) {
        return false;
    }

    $srcW = (int)$imgInfo[0];
    $srcH = (int)$imgInfo[1];
    $mime = $imgInfo['mime'] ?? '';

    // Create source image handle
    $srcImg = null;
    if ($ext === 'jpg' || $ext === 'jpeg' || $mime === 'image/jpeg' || $mime === 'image/pjpeg') {
        $srcImg = @imagecreatefromjpeg($srcPath);
    } elseif ($ext === 'png' || $mime === 'image/png' || $mime === 'image/x-png') {
        $srcImg = @imagecreatefrompng($srcPath);
    } elseif ($ext === 'webp' || $mime === 'image/webp') {
        if (function_exists('imagecreatefromwebp')) {
            $srcImg = @imagecreatefromwebp($srcPath);
        }
    }

    if (!$srcImg) {
        return false;
    }

    // Calculate proportional output dimensions — never exceed maxW x maxH,
    // never crop, never distort.
    $scale  = min(1.0, $maxW / $srcW, $maxH / $srcH); // 1.0 = no upscaling
    $dstW   = (int)round($srcW * $scale);
    $dstH   = (int)round($srcH * $scale);

    // Create truecolor destination canvas at the calculated size
    $dstImg = imagecreatetruecolor($dstW, $dstH);
    if (!$dstImg) {
        imagedestroy($srcImg);
        return false;
    }

    // Preserve PNG / WEBP transparency
    if ($ext === 'png' || $ext === 'webp') {
        imagealphablending($dstImg, false);
        imagesavealpha($dstImg, true);
        $transparent = imagecolorallocatealpha($dstImg, 0, 0, 0, 127);
        imagefilledrectangle($dstImg, 0, 0, $dstW, $dstH, $transparent);
    }

    // High-quality bicubic resampling (full source → full destination, no crop)
    imagecopyresampled($dstImg, $srcImg, 0, 0, 0, 0, $dstW, $dstH, $srcW, $srcH);

    // Enable Progressive JPEG rendering for fast web delivery
    imageinterlace($dstImg, true);

    // Export with optimized quality settings
    $success = false;
    if ($ext === 'jpg' || $ext === 'jpeg') {
        $success = imagejpeg($dstImg, $destPath, 89);
    } elseif ($ext === 'png') {
        $success = imagepng($dstImg, $destPath, 8);
    } elseif ($ext === 'webp') {
        if (function_exists('imagewebp')) {
            $success = imagewebp($dstImg, $destPath, 88);
        } else {
            $success = imagejpeg($dstImg, $destPath, 89);
        }
    } else {
        $success = imagejpeg($dstImg, $destPath, 89);
    }

    // Clean up GD handles
    imagedestroy($srcImg);
    imagedestroy($dstImg);

    return $success;
}

// Process & Validate Slide Image Upload
function validate_and_upload_slide_image($fileArray, $type, $uploadDir, $existingPath = '') {
    // If no file uploaded or UPLOAD_ERR_NO_FILE during edit, retain existing path
    if (empty($fileArray['name']) || $fileArray['error'] === UPLOAD_ERR_NO_FILE) {
        return ['path' => $existingPath, 'error' => null];
    }
    
    if ($fileArray['error'] !== UPLOAD_ERR_OK) {
        return ['path' => $existingPath, 'error' => 'File upload error code: ' . $fileArray['error']];
    }
    
    $ext = strtolower(pathinfo($fileArray['name'], PATHINFO_EXTENSION));
    $allowedExts = ['jpg', 'jpeg', 'png', 'webp', 'svg'];
    
    if (!in_array($ext, $allowedExts, true)) {
        return [
            'path'  => $existingPath,
            'error' => ucfirst($type) . ' banner format not allowed. Allowed formats: JPG, JPEG, PNG, WEBP, SVG.'
        ];
    }
    
    // Max upload file size pre-processing: 10 MB
    $maxBytes = 10 * 1024 * 1024;
    if ($fileArray['size'] > $maxBytes) {
        $uploadedMb = round($fileArray['size'] / (1024 * 1024), 1);
        return [
            'path'  => $existingPath,
            'error' => ucfirst($type) . " banner file size ({$uploadedMb} MB) exceeds maximum allowed limit of 10 MB."
        ];
    }
    
    // MIME type validation
    $allowedMimes = ['image/jpeg', 'image/png', 'image/webp', 'image/svg+xml', 'image/svg', 'image/x-png', 'image/pjpeg'];
    $mime = '';
    if (function_exists('finfo_open')) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime  = finfo_file($finfo, $fileArray['tmp_name']);
        finfo_close($finfo);
    } elseif (function_exists('mime_content_type')) {
        $mime = mime_content_type($fileArray['tmp_name']);
    }
    
    if (!empty($mime) && !in_array($mime, $allowedMimes, true)) {
        return [
            'path'  => $existingPath,
            'error' => ucfirst($type) . " banner invalid MIME type ({$mime}). Allowed formats: JPG, JPEG, PNG, WEBP, SVG."
        ];
    }
    
    // Max output dimensions — proportional resize only, no crop.
    // Desktop: cap at 3000 px wide; mobile: cap at 1600 px wide.
    // Height is capped at 3000 px for both to prevent runaway tall images.
    if ($type === 'desktop') {
        $maxW     = 3000;
        $maxH     = 3000;
        $minWidth = 400;
    } else {
        $maxW     = 1600;
        $maxH     = 3000;
        $minWidth = 400;
    }

    // Minimum width validation (skipped for SVG vector files)
    if ($ext !== 'svg' && $mime !== 'image/svg+xml' && $mime !== 'image/svg') {
        $imgInfo = @getimagesize($fileArray['tmp_name']);
        if (!$imgInfo) {
            return ['path' => $existingPath, 'error' => ucfirst($type) . ' banner image file is invalid or corrupted.'];
        }

        $width  = (int)$imgInfo[0];
        $height = (int)$imgInfo[1];

        if ($width < $minWidth) {
            return [
                'path'  => $existingPath,
                'error' => ucfirst($type) . " banner must be at least {$minWidth} px wide. Uploaded image: {$width} × {$height} px."
            ];
        }
    }

    // Generate output file path
    $outExt   = ($ext === 'svg') ? 'svg' : (($ext === 'png') ? 'png' : 'jpg');
    $filename = 'slide_' . time() . '_' . rand(1000, 9999) . '.' . $outExt;
    $target   = $uploadDir . $filename;

    // Execute proportional resize (no crop)
    $processed = process_and_optimize_hero_image($fileArray['tmp_name'], $maxW, $maxH, $target, $ext);
    
    if ($processed && file_exists($target)) {
        // Clean up old file if replacing
        if (!empty($existingPath) && strpos($existingPath, 'uploads/slides/') === 0) {
            $oldDiskPath = __DIR__ . '/../' . $existingPath;
            if (file_exists($oldDiskPath)) {
                @unlink($oldDiskPath);
            }
        }
        return ['path' => 'uploads/slides/' . $filename, 'error' => null];
    }
    
    return ['path' => $existingPath, 'error' => 'Failed to process and optimize uploaded banner on server.'];
}

// ── Handle Actions ──────────────────────────────────────────────────────────

// Toggle Status
if (isset($_GET['action']) && $_GET['action'] === 'toggle_status' && isset($_GET['id'])) {
    $slideId = (int)$_GET['id'];
    $stmt = $pdo->prepare("UPDATE hero_slides SET status = IF(status = 1, 0, 1) WHERE id = ?");
    $stmt->execute([$slideId]);
    header("Location: slides.php?msg=status_updated");
    exit();
}

// Delete Slide
if (isset($_POST['action']) && $_POST['action'] === 'delete' && isset($_POST['id'])) {
    $slideId = (int)$_POST['id'];
    $stmt = $pdo->prepare("SELECT desktop_image, mobile_image FROM hero_slides WHERE id = ?");
    $stmt->execute([$slideId]);
    $slide = $stmt->fetch();
    
    if ($slide) {
        if (!empty($slide['desktop_image']) && strpos($slide['desktop_image'], 'uploads/slides/') === 0) {
            @unlink(__DIR__ . '/../' . $slide['desktop_image']);
        }
        if (!empty($slide['mobile_image']) && strpos($slide['mobile_image'], 'uploads/slides/') === 0) {
            @unlink(__DIR__ . '/../' . $slide['mobile_image']);
        }
        $del = $pdo->prepare("DELETE FROM hero_slides WHERE id = ?");
        $del->execute([$slideId]);
        $success = "Slide deleted successfully!";
    }
}

// Add Slide
if (isset($_POST['action']) && $_POST['action'] === 'add') {
    $title         = trim($_POST['title'] ?? '');
    $subtitle      = trim($_POST['subtitle'] ?? '');
    $description   = trim($_POST['description'] ?? '');
    $button_text   = trim($_POST['button_text'] ?? '');
    $button_link   = trim($_POST['button_link'] ?? '');
    $display_order = (int)($_POST['display_order'] ?? 0);
    $status        = isset($_POST['status']) ? 1 : 0;
    
    $form_data = [
        'title'         => $title,
        'subtitle'      => $subtitle,
        'description'   => $description,
        'button_text'   => $button_text,
        'button_link'   => $button_link,
        'display_order' => $display_order,
        'status'        => $status
    ];
    
    // Validate Desktop Banner
    $dResult = validate_and_upload_slide_image($_FILES['desktop_image'] ?? [], 'desktop', $uploadDir);
    // Validate Mobile Banner
    $mResult = validate_and_upload_slide_image($_FILES['mobile_image'] ?? [], 'mobile', $uploadDir);
    
    $hasError = false;
    if (!empty($dResult['error'])) {
        $desktop_error = $dResult['error'];
        $hasError = true;
    } elseif (empty($dResult['path'])) {
        $desktop_error = "Desktop banner image is required.";
        $hasError = true;
    }
    
    if (!empty($mResult['error'])) {
        $mobile_error = $mResult['error'];
        $hasError = true;
    }
    
    if ($hasError) {
        $active_modal = 'add';
    } else {
        $desktop_image = $dResult['path'];
        $mobile_image  = !empty($mResult['path']) ? $mResult['path'] : $desktop_image;
        
        $stmt = $pdo->prepare("INSERT INTO hero_slides 
            (title, subtitle, description, desktop_image, mobile_image, button_text, button_link, display_order, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$title, $subtitle, $description, $desktop_image, $mobile_image, $button_text, $button_link, $display_order, $status]);
        $success = "New slide added successfully!";
        $form_data = [];
    }
}

// Edit Slide
if (isset($_POST['action']) && $_POST['action'] === 'edit' && isset($_POST['id'])) {
    $slideId       = (int)$_POST['id'];
    $title         = trim($_POST['title'] ?? '');
    $subtitle      = trim($_POST['subtitle'] ?? '');
    $description   = trim($_POST['description'] ?? '');
    $button_text   = trim($_POST['button_text'] ?? '');
    $button_link   = trim($_POST['button_link'] ?? '');
    $display_order = (int)($_POST['display_order'] ?? 0);
    $status        = isset($_POST['status']) ? 1 : 0;
    
    $stmt = $pdo->prepare("SELECT desktop_image, mobile_image FROM hero_slides WHERE id = ?");
    $stmt->execute([$slideId]);
    $curr = $stmt->fetch();
    
    $form_data = [
        'id'            => $slideId,
        'title'         => $title,
        'subtitle'      => $subtitle,
        'description'   => $description,
        'button_text'   => $button_text,
        'button_link'   => $button_link,
        'display_order' => $display_order,
        'status'        => $status,
        'desktop_image' => $curr['desktop_image'] ?? '',
        'mobile_image'  => $curr['mobile_image'] ?? ''
    ];
    
    // Validate Desktop Banner
    $dResult = validate_and_upload_slide_image($_FILES['desktop_image'] ?? [], 'desktop', $uploadDir, $curr['desktop_image'] ?? '');
    // Validate Mobile Banner
    $mResult = validate_and_upload_slide_image($_FILES['mobile_image'] ?? [], 'mobile', $uploadDir, $curr['mobile_image'] ?? '');
    
    $hasError = false;
    if (!empty($dResult['error'])) {
        $desktop_error = $dResult['error'];
        $hasError = true;
    }
    
    if (!empty($mResult['error'])) {
        $mobile_error = $mResult['error'];
        $hasError = true;
    }
    
    if ($hasError) {
        $active_modal = 'edit';
        if (isset($_POST['ajax']) || (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')) {
            header('Content-Type: application/json');
            echo json_encode([
                'status' => 'error',
                'error' => !empty($desktop_error) ? $desktop_error : $mobile_error,
                'desktop_error' => $desktop_error,
                'mobile_error' => $mobile_error
            ]);
            exit();
        }
    } else {
        $desktop_image = $dResult['path'];
        $mobile_image  = $mResult['path'];
        
        $up = $pdo->prepare("UPDATE hero_slides SET 
            title = ?, subtitle = ?, description = ?, desktop_image = ?, mobile_image = ?, button_text = ?, button_link = ?, display_order = ?, status = ? 
            WHERE id = ?");
        $up->execute([$title, $subtitle, $description, $desktop_image, $mobile_image, $button_text, $button_link, $display_order, $status, $slideId]);
        $success = "Slide updated successfully!";
        $form_data = [];

        if (isset($_POST['ajax']) || (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')) {
            header('Content-Type: application/json');
            $updatedStmt = $pdo->prepare("SELECT * FROM hero_slides WHERE id = ?");
            $updatedStmt->execute([$slideId]);
            $updatedSlide = $updatedStmt->fetch(PDO::FETCH_ASSOC);

            echo json_encode([
                'status' => 'success',
                'msg' => 'Slide updated successfully!',
                'slide' => $updatedSlide
            ]);
            exit();
        }
    }
}

// Fetch all slides
$slides = $pdo->query("SELECT * FROM hero_slides ORDER BY display_order ASC, id ASC")->fetchAll();

if (!isset($_GET['partial'])) {
    include 'header.php';
    include 'nav_header.php';
    include 'main_header.php';
    include 'sidebar.php';
}
?>

<div class="content-body">
    <div class="container-fluid">
        
        <div class="row page-titles mx-0">
            <div class="col-sm-6 p-md-0">
                <div class="welcome-text">
                    <h4>Homepage Slider Management</h4>
                    <p class="mb-0">Manage carousel slides, desktop & mobile images, titles, and ordering.</p>
                </div>
            </div>
            <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSlideModal">
                    <i class="fas fa-plus mr-2"></i>Add New Slide
                </button>
            </div>
        </div>

        <div id="alertContainer">
        <?php 
        $msg_query = $_GET['msg'] ?? '';
        if ($msg_query === 'status_updated') {
            $success = "Status updated successfully!";
        }
        ?>
        <?php if (!empty($success)): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <button type="button" class="close" data-bs-dismiss="alert">&times;</button>
                <strong>Success!</strong> <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <button type="button" class="close" data-bs-dismiss="alert">&times;</button>
                <strong>Error!</strong> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header border-bottom">
                        <h4 class="card-title mb-0">Configured Slider Items</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-responsive-md table-hover align-middle">
                                <thead class="thead-dark">
                                    <tr>
                                        <th style="width:70px;">Order</th>
                                        <th style="width:140px;">Desktop Banner</th>
                                        <th style="width:120px;">Mobile Banner</th>
                                        <th>Slide Details</th>
                                        <th style="width:100px;">Status</th>
                                        <th style="width:120px;" class="text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($slides)): ?>
                                        <tr>
                                            <td colspan="6" class="text-center py-4 text-muted">No slides found. Click "Add New Slide" to create one.</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($slides as $slide): 
                                            $dImg = '../' . htmlspecialchars($slide['desktop_image']);
                                            $mImg = '../' . htmlspecialchars($slide['mobile_image']);
                                        ?>
                                        <tr id="slide-row-<?php echo $slide['id']; ?>">
                                            <td>
                                                <span class="badge badge-secondary"><?php echo (int)$slide['display_order']; ?></span>
                                            </td>
                                            <td>
                                                <img src="<?php echo $dImg; ?>" alt="Desktop Banner" style="max-width:120px; max-height:50px; object-fit:cover; border-radius:4px; border:1px solid #ddd;">
                                            </td>
                                            <td>
                                                <img src="<?php echo $mImg; ?>" alt="Mobile Banner" style="max-width:80px; max-height:50px; object-fit:cover; border-radius:4px; border:1px solid #ddd;">
                                            </td>
                                            <td>
                                                <strong><?php echo htmlspecialchars($slide['title'] ?: '(No Title)'); ?></strong>
                                                <?php if (!empty($slide['subtitle'])): ?>
                                                    <br><small class="text-info"><?php echo htmlspecialchars($slide['subtitle']); ?></small>
                                                <?php endif; ?>
                                                <?php if (!empty($slide['button_text'])): ?>
                                                    <br><small class="text-muted">Button: <?php echo htmlspecialchars($slide['button_text']); ?> (<?php echo htmlspecialchars($slide['button_link']); ?>)</small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <a href="slides.php?action=toggle_status&id=<?php echo $slide['id']; ?>" 
                                                   class="badge badge-<?php echo $slide['status'] ? 'success' : 'danger'; ?>"
                                                   title="Click to toggle status">
                                                    <?php echo $slide['status'] ? 'Active' : 'Disabled'; ?>
                                                </a>
                                            </td>
                                            <td class="text-right">
                                                <button class="btn btn-warning btn-xs mr-1 btn-edit" data-slide='<?php echo json_encode($slide, JSON_HEX_APOS); ?>'>
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <form action="slides.php" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this slide?');">
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="id" value="<?php echo $slide['id']; ?>">
                                                    <button type="submit" class="btn btn-danger btn-xs">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

<style>
/* ==========================================================================
   SLIDER ITEM MODAL — REDESIGNED UI (MATCHING REFERENCE)
   ========================================================================== */
.slider-item-modal .modal-dialog {
    max-width: 840px;
    margin: 1.75rem auto;
}
.slider-item-modal .modal-content {
    border: none;
    border-radius: 16px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.12), 0 4px 16px rgba(0, 0, 0, 0.06);
    background: #ffffff;
    overflow: hidden;
}
.slider-item-modal .modal-header {
    padding: 22px 28px 18px;
    border-bottom: 1px solid #f0f2f7;
    background: #ffffff;
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
}
.slider-item-modal .modal-header-icon {
    width: 44px;
    height: 44px;
    min-width: 44px;
    border-radius: 12px;
    background: rgba(136, 108, 192, 0.12);
    color: #886CC0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
}
.slider-item-modal .modal-title {
    font-size: 19px;
    font-weight: 700;
    color: #1a1d24;
    line-height: 1.25;
    margin-bottom: 2px;
}
.slider-item-modal .modal-subtitle {
    font-size: 13px;
    color: #737B8B;
    font-weight: 400;
}
.slider-item-modal .btn-close-modal {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    border: 1px solid #edf0f5;
    background: #f8f9fb;
    color: #6c757d;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.2s ease;
    padding: 0;
}
.slider-item-modal .btn-close-modal:hover {
    background: #e9ecef;
    color: #1a1d24;
    border-color: #dee2e6;
}
.slider-item-modal .modal-body {
    padding: 24px 28px 16px;
    background: #ffffff;
}
.slider-item-modal .form-group {
    margin-bottom: 18px;
}
.slider-item-modal .form-label-custom {
    font-size: 13.5px;
    font-weight: 600;
    color: #2b303b;
    margin-bottom: 7px;
    display: block;
}
.slider-item-modal .form-control-custom {
    height: 44px;
    border: 1.5px solid #e2e6ee;
    border-radius: 8px;
    padding: 10px 14px;
    font-size: 14px;
    color: #2b303b;
    background: #ffffff;
    width: 100%;
    transition: border-color 0.2s, box-shadow 0.2s;
}
.slider-item-modal .form-control-custom:focus {
    border-color: #886CC0;
    box-shadow: 0 0 0 3.5px rgba(136, 108, 192, 0.14);
    outline: none;
}
.slider-item-modal .form-control-custom::placeholder {
    color: #9aa1af;
    font-size: 13.5px;
}
.slider-item-modal .textarea-custom {
    height: 72px;
    min-height: 72px;
    resize: vertical;
}

/* Upload Card & Dropzone */
.slider-upload-card {
    background: #ffffff;
    border: 1.5px solid #edf0f6;
    border-radius: 12px;
    padding: 16px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);
    transition: border-color 0.2s;
}
.slider-upload-card:hover {
    border-color: #e0e4ee;
}
.upload-card-header {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 12px;
}
.upload-card-icon {
    width: 32px;
    height: 32px;
    min-width: 32px;
    border-radius: 8px;
    background: rgba(136, 108, 192, 0.12);
    color: #886CC0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
}
.upload-card-title {
    font-size: 13.5px;
    font-weight: 600;
    color: #2b303b;
}
.slider-dropzone {
    border: 1.5px dashed #d5dbe7;
    border-radius: 8px;
    padding: 20px 14px;
    text-align: center;
    background: #fafbfd;
    cursor: pointer;
    transition: all 0.2s ease;
    margin-bottom: 10px;
    position: relative;
}
.slider-dropzone:hover,
.slider-dropzone.dragover {
    border-color: #886CC0;
    background: #f8f6fc;
}
.slider-dropzone .dropzone-icon {
    font-size: 24px;
    color: #886CC0;
    margin-bottom: 6px;
}
.slider-dropzone .dropzone-primary {
    font-size: 13px;
    color: #2b303b;
    display: block;
}
.slider-dropzone .dropzone-primary strong {
    color: #1a1d24;
    font-weight: 600;
}
.slider-dropzone .dropzone-sub {
    font-size: 11.5px;
    color: #8a92a2;
    display: block;
    margin-top: 3px;
}
.dropzone-file-name {
    font-size: 12.5px;
    font-weight: 500;
    color: #553c9a;
    background: rgba(136, 108, 192, 0.1);
    border: 1px solid rgba(136, 108, 192, 0.25);
    border-radius: 6px;
    padding: 6px 12px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    max-width: 100%;
    word-break: break-all;
}
.upload-guidance-pill {
    background: #f7f4fc;
    color: #7251b5;
    font-size: 12px;
    font-weight: 500;
    padding: 8px 12px;
    border-radius: 6px;
    display: flex;
    align-items: center;
    gap: 6px;
}
.current-file-badge {
    font-size: 11.5px;
    color: #5c6270;
    background: #f1f3f7;
    padding: 4px 10px;
    border-radius: 6px;
    display: inline-block;
    word-break: break-all;
}

/* Custom Input Groups */
.custom-input-group {
    display: flex;
    align-items: stretch;
    width: 100%;
}
.custom-input-group .input-group-addon-custom {
    width: 44px;
    min-width: 44px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f8f9fb;
    border: 1.5px solid #e2e6ee;
    border-right: none;
    border-radius: 8px 0 0 8px;
    color: #886CC0;
    font-size: 14px;
}
.custom-input-group .input-group-addon-custom .tx-icon {
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    font-weight: 700;
    font-size: 15px;
    line-height: 1;
}
.custom-input-group .input-group-addon-custom .tx-icon sub {
    font-size: 10px;
    bottom: -0.1em;
}
.custom-input-group .form-control-custom.has-addon {
    border-radius: 0 8px 8px 0 !important;
    border-left: 1.5px solid #e2e6ee;
}
.custom-input-group:focus-within .input-group-addon-custom {
    border-color: #886CC0;
}
.custom-input-group:focus-within .form-control-custom.has-addon {
    border-color: #886CC0;
}

/* Checkbox Card / Styled Checkbox */
.custom-slider-checkbox {
    display: flex;
    align-items: flex-start;
    cursor: pointer;
    user-select: none;
    margin-bottom: 0;
    width: 100%;
}
.custom-slider-checkbox input[type="checkbox"] {
    position: absolute;
    opacity: 0;
    pointer-events: none;
}
.custom-slider-checkbox .checkbox-box {
    width: 20px;
    height: 20px;
    min-width: 20px;
    border-radius: 5px;
    border: 2px solid #d1d5db;
    background: #ffffff;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: transparent;
    transition: all 0.2s ease;
    margin-right: 12px;
    margin-top: 2px;
}
.custom-slider-checkbox .checkbox-box::after {
    content: "\f00c";
    font-family: "Font Awesome 6 Free", "Font Awesome 5 Free", "FontAwesome";
    font-weight: 900;
    font-size: 11px;
}
.custom-slider-checkbox input[type="checkbox"]:checked + .checkbox-box {
    background: #886CC0;
    border-color: #886CC0;
    color: #ffffff;
}
.custom-slider-checkbox .checkbox-title {
    font-size: 14px;
    font-weight: 600;
    color: #1a1d24;
    line-height: 1.2;
}
.custom-slider-checkbox .checkbox-desc {
    font-size: 12px;
    color: #737B8B;
    margin-top: 3px;
    display: block;
}

/* Modal Footer */
.slider-item-modal .modal-footer {
    padding: 16px 28px 22px;
    border-top: 1px solid #f0f2f7;
    background: #ffffff;
    display: flex;
    justify-content: flex-end;
    gap: 12px;
}
.btn-slider-cancel {
    padding: 9px 24px;
    font-size: 13.5px;
    font-weight: 600;
    border-radius: 8px;
    background: #ffffff;
    border: 1.5px solid #e2e6ee;
    color: #495057;
    transition: all 0.2s;
}
.btn-slider-cancel:hover {
    background: #f8f9fb;
    border-color: #d5dbe7;
    color: #212529;
}
.btn-slider-save {
    padding: 9px 26px;
    font-size: 13.5px;
    font-weight: 600;
    border-radius: 8px;
    background: #886CC0;
    border: 1.5px solid #886CC0;
    color: #ffffff;
    box-shadow: 0 4px 12px rgba(136, 108, 192, 0.25);
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.2s;
}
.btn-slider-save:hover,
.btn-slider-save:focus {
    background: #6c4bae;
    border-color: #6c4bae;
    color: #ffffff;
    box-shadow: 0 6px 16px rgba(136, 108, 192, 0.35);
    transform: translateY(-1px);
}
.btn-slider-save:active {
    transform: translateY(0);
}
@media (max-width: 767px) {
    .slider-item-modal .modal-header,
    .slider-item-modal .modal-body,
    .slider-item-modal .modal-footer {
        padding-left: 18px;
        padding-right: 18px;
    }
}
</style>

<!-- Modal: Add Slide -->
<div class="modal fade slider-item-modal" id="addSlideModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <form action="slides.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="add">
                
                <!-- Modal Header -->
                <div class="modal-header">
                    <div class="d-flex align-items-center">
                        <div class="modal-header-icon mr-3">
                            <i class="far fa-images"></i>
                        </div>
                        <div>
                            <h5 class="modal-title">Add New Slider Item</h5>
                            <p class="modal-subtitle mb-0">Create a new hero slider item for the homepage</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close-modal" data-bs-dismiss="modal" aria-label="Close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="modal-body">
                    <div class="row">
                        <!-- Title -->
                        <div class="col-md-6 form-group">
                            <label class="form-label-custom">Title (Banner Text / Heading) <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control form-control-custom" value="<?php echo htmlspecialchars($form_data['title'] ?? ''); ?>" placeholder="Enter banner title">
                        </div>

                        <!-- Subtitle -->
                        <div class="col-md-6 form-group">
                            <label class="form-label-custom">Subtitle</label>
                            <input type="text" name="subtitle" class="form-control form-control-custom" value="<?php echo htmlspecialchars($form_data['subtitle'] ?? ''); ?>" placeholder="Enter subtitle (optional)">
                        </div>

                        <!-- Description -->
                        <div class="col-12 form-group">
                            <label class="form-label-custom">Description</label>
                            <textarea name="description" class="form-control form-control-custom textarea-custom" rows="2" placeholder="Enter description (optional)"><?php echo htmlspecialchars($form_data['description'] ?? ''); ?></textarea>
                        </div>

                        <!-- Desktop Banner Image -->
                        <div class="col-md-6 form-group">
                            <div class="slider-upload-card">
                                <div class="upload-card-header">
                                    <span class="upload-card-icon"><i class="fas fa-desktop"></i></span>
                                    <span class="upload-card-title">Desktop Banner Image <span class="text-danger">*</span></span>
                                </div>
                                <div class="slider-dropzone" data-input-id="add_desktop_file">
                                    <input type="file" id="add_desktop_file" name="desktop_image" class="d-none dropzone-file-input <?php echo (!empty($desktop_error) && $active_modal === 'add') ? 'is-invalid' : ''; ?>" accept="image/*" <?php echo ($active_modal === 'add') ? '' : 'required'; ?>>
                                    <div class="dropzone-content">
                                        <div class="dropzone-icon"><i class="fas fa-arrow-up-from-bracket"></i></div>
                                        <div class="dropzone-text">
                                            <span class="dropzone-primary"><strong>Choose file</strong> or drag &amp; drop</span>
                                            <span class="dropzone-sub">JPG, PNG, WEBP, SVG &bull; Max 10 MB</span>
                                        </div>
                                    </div>
                                    <div class="dropzone-file-name d-none"></div>
                                </div>
                                <div class="upload-guidance-pill">
                                    <i class="fas fa-info-circle mr-1"></i> Recommended: 1920 &times; 560 px
                                </div>
                            </div>
                            <?php if (!empty($desktop_error) && $active_modal === 'add'): ?>
                                <div class="invalid-feedback d-block text-danger font-weight-bold mt-1">
                                    <i class="fas fa-times-circle mr-1"></i>❌ <?php echo htmlspecialchars($desktop_error); ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Mobile Banner Image -->
                        <div class="col-md-6 form-group">
                            <div class="slider-upload-card">
                                <div class="upload-card-header">
                                    <span class="upload-card-icon"><i class="fas fa-mobile-alt"></i></span>
                                    <span class="upload-card-title">Mobile Banner Image</span>
                                </div>
                                <div class="slider-dropzone" data-input-id="add_mobile_file">
                                    <input type="file" id="add_mobile_file" name="mobile_image" class="d-none dropzone-file-input <?php echo (!empty($mobile_error) && $active_modal === 'add') ? 'is-invalid' : ''; ?>" accept="image/*">
                                    <div class="dropzone-content">
                                        <div class="dropzone-icon"><i class="fas fa-arrow-up-from-bracket"></i></div>
                                        <div class="dropzone-text">
                                            <span class="dropzone-primary"><strong>Choose file</strong> or drag &amp; drop</span>
                                            <span class="dropzone-sub">JPG, PNG, WEBP &bull; Max 10 MB</span>
                                        </div>
                                    </div>
                                    <div class="dropzone-file-name d-none"></div>
                                </div>
                                <div class="upload-guidance-pill">
                                    <i class="fas fa-info-circle mr-1"></i> Recommended: 1200 &times; 900 px
                                </div>
                            </div>
                            <?php if (!empty($mobile_error) && $active_modal === 'add'): ?>
                                <div class="invalid-feedback d-block text-danger font-weight-bold mt-1">
                                    <i class="fas fa-times-circle mr-1"></i>❌ <?php echo htmlspecialchars($mobile_error); ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Button Text -->
                        <div class="col-md-6 form-group">
                            <label class="form-label-custom">Button Text</label>
                            <div class="custom-input-group">
                                <span class="input-group-addon-custom"><span class="tx-icon">T<sub>x</sub></span></span>
                                <input type="text" name="button_text" class="form-control form-control-custom has-addon" value="<?php echo htmlspecialchars($form_data['button_text'] ?? ''); ?>" placeholder="Enter button text (optional)">
                            </div>
                        </div>

                        <!-- Button Link URL -->
                        <div class="col-md-6 form-group">
                            <label class="form-label-custom">Button Link URL</label>
                            <div class="custom-input-group">
                                <span class="input-group-addon-custom"><i class="fas fa-link"></i></span>
                                <input type="text" name="button_link" class="form-control form-control-custom has-addon" value="<?php echo htmlspecialchars($form_data['button_link'] ?? ''); ?>" placeholder="Enter button URL (optional)">
                            </div>
                        </div>

                        <!-- Display Order -->
                        <div class="col-md-6 form-group">
                            <label class="form-label-custom">Display Order</label>
                            <div class="custom-input-group">
                                <span class="input-group-addon-custom"><i class="fas fa-hashtag"></i></span>
                                <input type="number" name="display_order" class="form-control form-control-custom has-addon" value="<?php echo htmlspecialchars($form_data['display_order'] ?? '0'); ?>" min="0">
                            </div>
                        </div>

                        <!-- Active & Visible Switch/Checkbox -->
                        <div class="col-md-6 form-group d-flex align-items-center pt-md-4">
                            <label class="custom-slider-checkbox" for="addStatus">
                                <input type="checkbox" name="status" id="addStatus" value="1" <?php echo (!isset($form_data['status']) || $form_data['status'] == 1) ? 'checked' : ''; ?>>
                                <span class="checkbox-box"></span>
                                <span class="checkbox-label-block">
                                    <strong class="d-block checkbox-title">Active &amp; Visible</strong>
                                    <small class="checkbox-desc">Enable this slide to show on website</small>
                                </span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-slider-cancel" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-slider-save"><i class="fas fa-save mr-2"></i>Save Slide</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Edit Slide -->
<div class="modal fade slider-item-modal" id="editSlideModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <form id="editSlideForm" action="slides.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id" id="edit_id" value="<?php echo htmlspecialchars($form_data['id'] ?? ''); ?>">
                
                <!-- Modal Header -->
                <div class="modal-header">
                    <div class="d-flex align-items-center">
                        <div class="modal-header-icon mr-3">
                            <i class="fas fa-pen-to-square"></i>
                        </div>
                        <div>
                            <h5 class="modal-title">Edit Slider Item</h5>
                            <p class="modal-subtitle mb-0">Update hero slider item details, banners and ordering</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close-modal" data-bs-dismiss="modal" aria-label="Close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="modal-body">
                    <div class="row">
                        <!-- Title -->
                        <div class="col-md-6 form-group">
                            <label class="form-label-custom">Title (Banner Text / Heading) <span class="text-danger">*</span></label>
                            <input type="text" name="title" id="edit_title" class="form-control form-control-custom" value="<?php echo htmlspecialchars($form_data['title'] ?? ''); ?>" placeholder="Enter banner title">
                        </div>

                        <!-- Subtitle -->
                        <div class="col-md-6 form-group">
                            <label class="form-label-custom">Subtitle</label>
                            <input type="text" name="subtitle" id="edit_subtitle" class="form-control form-control-custom" value="<?php echo htmlspecialchars($form_data['subtitle'] ?? ''); ?>" placeholder="Enter subtitle (optional)">
                        </div>

                        <!-- Description -->
                        <div class="col-12 form-group">
                            <label class="form-label-custom">Description</label>
                            <textarea name="description" id="edit_description" class="form-control form-control-custom textarea-custom" rows="2" placeholder="Enter description (optional)"><?php echo htmlspecialchars($form_data['description'] ?? ''); ?></textarea>
                        </div>

                        <!-- Replace Desktop Banner -->
                        <div class="col-md-6 form-group">
                            <div class="slider-upload-card">
                                <div class="upload-card-header">
                                    <span class="upload-card-icon"><i class="fas fa-desktop"></i></span>
                                    <span class="upload-card-title">Replace Desktop Banner</span>
                                </div>
                                <div class="current-file-badge mb-2" id="edit_desktop_current">
                                    <?php echo !empty($form_data['desktop_image']) ? 'Current: ' . htmlspecialchars($form_data['desktop_image']) : 'Current: None'; ?>
                                </div>
                                <div class="slider-dropzone" data-input-id="edit_desktop_file">
                                    <input type="file" id="edit_desktop_file" name="desktop_image" class="d-none dropzone-file-input <?php echo (!empty($desktop_error) && $active_modal === 'edit') ? 'is-invalid' : ''; ?>" accept="image/*">
                                    <div class="dropzone-content">
                                        <div class="dropzone-icon"><i class="fas fa-arrow-up-from-bracket"></i></div>
                                        <div class="dropzone-text">
                                            <span class="dropzone-primary"><strong>Choose file</strong> or drag &amp; drop</span>
                                            <span class="dropzone-sub">JPG, PNG, WEBP, SVG &bull; Max 10 MB</span>
                                        </div>
                                    </div>
                                    <div class="dropzone-file-name d-none"></div>
                                </div>
                                <div class="upload-guidance-pill">
                                    <i class="fas fa-info-circle mr-1"></i> Recommended: 1920 &times; 560 px
                                </div>
                            </div>
                            <?php if (!empty($desktop_error) && $active_modal === 'edit'): ?>
                                <div class="invalid-feedback d-block text-danger font-weight-bold mt-1">
                                    <i class="fas fa-times-circle mr-1"></i>❌ <?php echo htmlspecialchars($desktop_error); ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Replace Mobile Banner -->
                        <div class="col-md-6 form-group">
                            <div class="slider-upload-card">
                                <div class="upload-card-header">
                                    <span class="upload-card-icon"><i class="fas fa-mobile-alt"></i></span>
                                    <span class="upload-card-title">Replace Mobile Banner</span>
                                </div>
                                <div class="current-file-badge mb-2" id="edit_mobile_current">
                                    <?php echo !empty($form_data['mobile_image']) ? 'Current: ' . htmlspecialchars($form_data['mobile_image']) : 'Current: None'; ?>
                                </div>
                                <div class="slider-dropzone" data-input-id="edit_mobile_file">
                                    <input type="file" id="edit_mobile_file" name="mobile_image" class="d-none dropzone-file-input <?php echo (!empty($mobile_error) && $active_modal === 'edit') ? 'is-invalid' : ''; ?>" accept="image/*">
                                    <div class="dropzone-content">
                                        <div class="dropzone-icon"><i class="fas fa-arrow-up-from-bracket"></i></div>
                                        <div class="dropzone-text">
                                            <span class="dropzone-primary"><strong>Choose file</strong> or drag &amp; drop</span>
                                            <span class="dropzone-sub">JPG, PNG, WEBP &bull; Max 10 MB</span>
                                        </div>
                                    </div>
                                    <div class="dropzone-file-name d-none"></div>
                                </div>
                                <div class="upload-guidance-pill">
                                    <i class="fas fa-info-circle mr-1"></i> Recommended: 412 &times; 309 px
                                </div>
                            </div>
                            <?php if (!empty($mobile_error) && $active_modal === 'edit'): ?>
                                <div class="invalid-feedback d-block text-danger font-weight-bold mt-1">
                                    <i class="fas fa-times-circle mr-1"></i>❌ <?php echo htmlspecialchars($mobile_error); ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Button Text -->
                        <div class="col-md-6 form-group">
                            <label class="form-label-custom">Button Text</label>
                            <div class="custom-input-group">
                                <span class="input-group-addon-custom"><span class="tx-icon">T<sub>x</sub></span></span>
                                <input type="text" name="button_text" id="edit_button_text" class="form-control form-control-custom has-addon" value="<?php echo htmlspecialchars($form_data['button_text'] ?? ''); ?>" placeholder="Enter button text (optional)">
                            </div>
                        </div>

                        <!-- Button Link URL -->
                        <div class="col-md-6 form-group">
                            <label class="form-label-custom">Button Link URL</label>
                            <div class="custom-input-group">
                                <span class="input-group-addon-custom"><i class="fas fa-link"></i></span>
                                <input type="text" name="button_link" id="edit_button_link" class="form-control form-control-custom has-addon" value="<?php echo htmlspecialchars($form_data['button_link'] ?? ''); ?>" placeholder="Enter button URL (optional)">
                            </div>
                        </div>

                        <!-- Display Order -->
                        <div class="col-md-6 form-group">
                            <label class="form-label-custom">Display Order</label>
                            <div class="custom-input-group">
                                <span class="input-group-addon-custom"><i class="fas fa-hashtag"></i></span>
                                <input type="number" name="display_order" id="edit_display_order" class="form-control form-control-custom has-addon" min="0" value="<?php echo htmlspecialchars($form_data['display_order'] ?? '0'); ?>">
                            </div>
                        </div>

                        <!-- Active & Visible Switch/Checkbox -->
                        <div class="col-md-6 form-group d-flex align-items-center pt-md-4">
                            <label class="custom-slider-checkbox" for="edit_status">
                                <input type="checkbox" name="status" id="edit_status" value="1" <?php echo (!isset($form_data['status']) || $form_data['status'] == 1) ? 'checked' : ''; ?>>
                                <span class="checkbox-box"></span>
                                <span class="checkbox-label-block">
                                    <strong class="d-block checkbox-title">Active &amp; Visible</strong>
                                    <small class="checkbox-desc">Enable this slide to show on website</small>
                                </span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-slider-cancel" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-slider-save"><i class="fas fa-save mr-2"></i>Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
window.AdminPageInits = window.AdminPageInits || {};
window.initSlidesPage = function initSlidesPage() {
    if (!document.getElementById('addSlideModal') && !document.getElementById('editSlideModal')) {
        return;
    }

    function initDropzones() {
        document.querySelectorAll('.slider-dropzone').forEach(function (dropzone) {
            const inputId = dropzone.getAttribute('data-input-id');
            if (!inputId) return;
            const fileInput = document.getElementById(inputId);
            if (!fileInput) return;

            const contentEl = dropzone.querySelector('.dropzone-content');
            const nameEl = dropzone.querySelector('.dropzone-file-name');

            function updateDisplay(file) {
                if (file) {
                    const sizeMb = (file.size / (1024 * 1024)).toFixed(2);
                    if (nameEl) {
                        nameEl.innerHTML = '<i class="fas fa-check-circle text-success mr-1"></i> ' + escapeHtml(file.name) + ' (' + sizeMb + ' MB) <span class="badge badge-light ml-2">Change</span>';
                        nameEl.classList.remove('d-none');
                    }
                    if (contentEl) contentEl.classList.add('d-none');
                } else {
                    if (nameEl) {
                        nameEl.classList.add('d-none');
                        nameEl.innerHTML = '';
                    }
                    if (contentEl) contentEl.classList.remove('d-none');
                }
            }

            dropzone.onclick = function (e) {
                if (e.target !== fileInput) {
                    fileInput.click();
                }
            };

            fileInput.onchange = function () {
                if (fileInput.files && fileInput.files[0]) {
                    updateDisplay(fileInput.files[0]);
                } else {
                    updateDisplay(null);
                }
            };

            dropzone.ondragover = function (e) {
                e.preventDefault();
                dropzone.classList.add('dragover');
            };

            dropzone.ondragleave = function (e) {
                e.preventDefault();
                dropzone.classList.remove('dragover');
            };

            dropzone.ondrop = function (e) {
                e.preventDefault();
                dropzone.classList.remove('dragover');
                if (e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files.length > 0) {
                    fileInput.files = e.dataTransfer.files;
                    updateDisplay(e.dataTransfer.files[0]);
                    $(fileInput).trigger('change');
                }
            };
        });
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.innerText = text;
        return div.innerHTML;
    }

    initDropzones();

    // Reset dropzones on modal close
    ['addSlideModal', 'editSlideModal'].forEach(function (modalId) {
        const el = document.getElementById(modalId);
        if (el) {
            el.addEventListener('hidden.bs.modal', function () {
                el.querySelectorAll('.dropzone-file-name').forEach(function (nameEl) {
                    nameEl.classList.add('d-none');
                    nameEl.innerHTML = '';
                });
                el.querySelectorAll('.dropzone-content').forEach(function (contentEl) {
                    contentEl.classList.remove('d-none');
                });
                el.querySelectorAll('.dropzone-file-input').forEach(function (input) {
                    input.value = '';
                });
            });
        }
    });

    <?php if ($active_modal === 'add'): ?>
    const addModalEl = document.getElementById('addSlideModal');
    if (addModalEl) {
        bootstrap.Modal.getOrCreateInstance(addModalEl).show();
    }
    <?php endif; ?>

    <?php if ($active_modal === 'edit'): ?>
    const editModalEl = document.getElementById('editSlideModal');
    if (editModalEl) {
        bootstrap.Modal.getOrCreateInstance(editModalEl).show();
    }
    <?php endif; ?>

    $(document)
        .off('click.slidesEdit', '.btn-edit[data-slide]')
        .on('click.slidesEdit', '.btn-edit[data-slide]', function (e) {
            e.preventDefault();
            e.stopPropagation();

            const dataAttribute = this.getAttribute('data-slide');
            if (!dataAttribute) {
                return;
            }

            let data;
            try {
                data = JSON.parse(dataAttribute);
            } catch (error) {
                console.error('Invalid slide data:', error);
                return;
            }

            const editId = document.getElementById('edit_id');
            const editTitle = document.getElementById('edit_title');
            const editSubtitle = document.getElementById('edit_subtitle');
            const editDescription = document.getElementById('edit_description');
            const editButtonText = document.getElementById('edit_button_text');
            const editButtonLink = document.getElementById('edit_button_link');
            const editDisplayOrder = document.getElementById('edit_display_order');
            const editStatus = document.getElementById('edit_status');
            const editDesktopCurrent = document.getElementById('edit_desktop_current');
            const editMobileCurrent = document.getElementById('edit_mobile_current');
            const editModalEl = document.getElementById('editSlideModal');

            if (!editModalEl) {
                console.error('Edit modal not found.');
                return;
            }

            if (editId) editId.value = data.id || '';
            if (editTitle) editTitle.value = data.title || '';
            if (editSubtitle) editSubtitle.value = data.subtitle || '';
            if (editDescription) editDescription.value = data.description || '';
            if (editButtonText) editButtonText.value = data.button_text || '';
            if (editButtonLink) editButtonLink.value = data.button_link || '';
            if (editDisplayOrder) editDisplayOrder.value = data.display_order || 0;

            if (editStatus) {
                editStatus.checked = parseInt(data.status, 10) === 1;
            }

            if (editDesktopCurrent) {
                editDesktopCurrent.innerText = 'Current: ' + (data.desktop_image || 'None');
            }

            if (editMobileCurrent) {
                editMobileCurrent.innerText = 'Current: ' + (data.mobile_image || 'None');
            }

            // Reset dropzone files
            if (editModalEl) {
                editModalEl.querySelectorAll('.dropzone-file-name').forEach(function (nameEl) {
                    nameEl.classList.add('d-none');
                    nameEl.innerHTML = '';
                });
                editModalEl.querySelectorAll('.dropzone-content').forEach(function (contentEl) {
                    contentEl.classList.remove('d-none');
                });
                editModalEl.querySelectorAll('.dropzone-file-input').forEach(function (input) {
                    input.value = '';
                });
            }

            bootstrap.Modal.getOrCreateInstance(editModalEl).show();
        });

    $(document)
        .off('submit.slidesEdit', '#editSlideForm')
        .on('submit.slidesEdit', '#editSlideForm', function (e) {
            e.preventDefault();

            const editForm = this;
            const submitBtn = editForm.querySelector('button[type="submit"]');
            const originalBtnHtml = submitBtn ? submitBtn.innerHTML : 'Save Changes';

            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Saving...';
            }

            const formData = new FormData(editForm);
            formData.append('ajax', '1');

            fetch('slides.php', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnHtml;
                }

                if (data.status === 'success') {
                    const modalEl = document.getElementById('editSlideModal');
                    if (modalEl) {
                        const modal = bootstrap.Modal.getInstance(modalEl);
                        if (modal) {
                            modal.hide();
                        }
                    }

                    const alertContainer = document.getElementById('alertContainer');
                    if (alertContainer) {
                        alertContainer.innerHTML = `
                            <div class="alert alert-success alert-dismissible fade show">
                                <button type="button" class="close" data-bs-dismiss="alert">&times;</button>
                                <strong>Success!</strong>
                                ${data.msg || 'Slide updated successfully!'}
                            </div>
                        `;
                    }

                    if (data.slide) {
                        const row = document.getElementById('slide-row-' + data.slide.id);
                        if (row) {
                            const cells = row.cells;

                            if (cells[0]) {
                                cells[0].innerHTML =
                                    `<span class="badge badge-secondary">${parseInt(data.slide.display_order, 10)}</span>`;
                            }

                            if (cells[1]) {
                                cells[1].innerHTML =
                                    `<img src="../${data.slide.desktop_image}" alt="Desktop Banner" style="max-width:120px; max-height:50px; object-fit:cover; border-radius:4px; border:1px solid #ddd;">`;
                            }

                            if (cells[2]) {
                                cells[2].innerHTML =
                                    `<img src="../${data.slide.mobile_image}" alt="Mobile Banner" style="max-width:80px; max-height:50px; object-fit:cover; border-radius:4px; border:1px solid #ddd;">`;
                            }

                            let detailsHtml = `<strong>${data.slide.title ? data.slide.title : '(No Title)'}</strong>`;
                            if (data.slide.subtitle) {
                                detailsHtml += `<br><small class="text-info">${data.slide.subtitle}</small>`;
                            }
                            if (data.slide.button_text) {
                                detailsHtml += `<br><small class="text-muted">Button: ${data.slide.button_text} (${data.slide.button_link || ''})</small>`;
                            }
                            if (cells[3]) {
                                cells[3].innerHTML = detailsHtml;
                            }

                            const isStatusActive = parseInt(data.slide.status, 10) === 1;
                            if (cells[4]) {
                                cells[4].innerHTML =
                                    `<a href="slides.php?action=toggle_status&id=${data.slide.id}" class="badge badge-${isStatusActive ? 'success' : 'danger'}" title="Click to toggle status">${isStatusActive ? 'Active' : 'Disabled'}</a>`;
                            }

                            const editBtn = row.querySelector('.btn-edit');
                            if (editBtn) {
                                editBtn.setAttribute('data-slide', JSON.stringify(data.slide));
                            }
                        }
                    }
                } else {
                    alert(data.error || 'Failed to update slide.');
                }
            })
            .catch(err => {
                console.error('Edit slide AJAX error:', err);
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnHtml;
                }
                alert('An error occurred while saving changes.');
            });
        });
};
window.AdminPageInits['slides.php'] = window.initSlidesPage;
</script>
</div><!-- /.content-body -->
<?php
if (!isset($_GET['partial'])) {
    include 'footer.php';
}
?>

