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
 * Smart Center Crop & Resample Hero Slider Banner Image via PHP GD
 *
 * @param string $srcPath  Source temporary file path
 * @param int    $targetW  Target width (1920 or 768)
 * @param int    $targetH  Target height (420 or 500)
 * @param string $destPath Destination file path on disk
 * @param string $ext      File extension
 * @return bool True on success, false on failure
 */
function process_and_optimize_hero_image($srcPath, $targetW, $targetH, $destPath, $ext) {
    if (!file_exists($srcPath)) {
        return false;
    }

    $ext = strtolower($ext);

    // SVG vector files do not require GD raster processing
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

    // Calculate smart center crop coordinates
    $srcRatio = $srcW / $srcH;
    $dstRatio = $targetW / $targetH;

    if ($srcRatio > $dstRatio) {
        // Source is wider than target: crop left & right equally
        $cropH = $srcH;
        $cropW = (int)round($srcH * $dstRatio);
        $cropX = (int)round(($srcW - $cropW) / 2);
        $cropY = 0;
    } else {
        // Source is taller than target: crop top & bottom equally
        $cropW = $srcW;
        $cropH = (int)round($srcW / $dstRatio);
        $cropX = 0;
        $cropY = (int)round(($srcH - $cropH) / 2);
    }

    // Create truecolor destination canvas
    $dstImg = imagecreatetruecolor($targetW, $targetH);
    if (!$dstImg) {
        imagedestroy($srcImg);
        return false;
    }

    // Preserve PNG / WEBP transparency
    if ($ext === 'png' || $ext === 'webp') {
        imagealphablending($dstImg, false);
        imagesavealpha($dstImg, true);
        $transparent = imagecolorallocatealpha($dstImg, 0, 0, 0, 127);
        imagefilledrectangle($dstImg, 0, 0, $targetW, $targetH, $transparent);
    }

    // High quality bicubic resampling
    imagecopyresampled($dstImg, $srcImg, 0, 0, $cropX, $cropY, $targetW, $targetH, $cropW, $cropH);

    // Enable Progressive JPEG rendering for fast web delivery
    imageinterlace($dstImg, true);

    // Export optimized image with tuned quality settings
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
    
    // Specifications per banner type
    if ($type === 'desktop') {
        $targetW  = 1920;
        $targetH  = 420;
        $minWidth = 1200;
    } else {
        $targetW  = 768;
        $targetH  = 500;
        $minWidth = 600;
    }

    // Minimum width validation (Skipped for SVG vector files)
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
                'error' => ucfirst($type) . " banner width must be at least {$minWidth} pixels for high-quality display. Uploaded image: {$width} × {$height} pixels."
            ];
        }
    }
    
    // Generate output file path
    $outExt   = ($ext === 'svg') ? 'svg' : (($ext === 'png') ? 'png' : 'jpg');
    $filename = 'slide_' . time() . '_' . rand(1000, 9999) . '.' . $outExt;
    $target   = $uploadDir . $filename;
    
    // Execute Smart Center Crop & Resample
    $processed = process_and_optimize_hero_image($fileArray['tmp_name'], $targetW, $targetH, $target, $ext);
    
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
    } else {
        $desktop_image = $dResult['path'];
        $mobile_image  = $mResult['path'];
        
        $up = $pdo->prepare("UPDATE hero_slides SET 
            title = ?, subtitle = ?, description = ?, desktop_image = ?, mobile_image = ?, button_text = ?, button_link = ?, display_order = ?, status = ? 
            WHERE id = ?");
        $up->execute([$title, $subtitle, $description, $desktop_image, $mobile_image, $button_text, $button_link, $display_order, $status, $slideId]);
        $success = "Slide updated successfully!";
        $form_data = [];
    }
}

// Fetch all slides
$slides = $pdo->query("SELECT * FROM hero_slides ORDER BY display_order ASC, id ASC")->fetchAll();

include 'header.php';
include 'nav_header.php';
include 'main_header.php';
include 'sidebar.php';
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
                                        <tr>
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
</div>

<!-- Modal: Add Slide -->
<div class="modal fade" id="addSlideModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form action="slides.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="add">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Slider Item</h5>
                    <button type="button" class="close" data-bs-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Title (Banner Text / Heading)</label>
                            <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($form_data['title'] ?? ''); ?>" placeholder="Optional title overlay">
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Subtitle</label>
                            <input type="text" name="subtitle" class="form-control" value="<?php echo htmlspecialchars($form_data['subtitle'] ?? ''); ?>" placeholder="Optional subtitle">
                        </div>
                        <div class="col-12 form-group">
                            <label>Description</label>
                            <textarea name="description" class="form-control" rows="2" placeholder="Optional description"><?php echo htmlspecialchars($form_data['description'] ?? ''); ?></textarea>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Desktop Banner Image <span class="text-danger">*</span></label>
                            <div class="small text-muted mb-1">
                                <strong>Auto-Processed Output:</strong> 1920 × 420 px | <strong>Min Width:</strong> 1200 px | <strong>Formats:</strong> JPG, JPEG, PNG, WEBP, SVG | <strong>Max Upload:</strong> 10 MB
                            </div>
                            <input type="file" name="desktop_image" class="form-control-file <?php echo (!empty($desktop_error) && $active_modal === 'add') ? 'is-invalid' : ''; ?>" accept="image/*" <?php echo ($active_modal === 'add') ? '' : 'required'; ?>>
                            <?php if (!empty($desktop_error) && $active_modal === 'add'): ?>
                                <div class="invalid-feedback d-block text-danger font-weight-bold mt-1">
                                    <i class="fas fa-times-circle mr-1"></i>❌ <?php echo htmlspecialchars($desktop_error); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Mobile Banner Image</label>
                            <div class="small text-muted mb-1">
                                <strong>Auto-Processed Output:</strong> 768 × 500 px | <strong>Min Width:</strong> 600 px | <strong>Formats:</strong> JPG, JPEG, PNG, WEBP, SVG | <strong>Max Upload:</strong> 10 MB
                            </div>
                            <input type="file" name="mobile_image" class="form-control-file <?php echo (!empty($mobile_error) && $active_modal === 'add') ? 'is-invalid' : ''; ?>" accept="image/*">
                            <?php if (!empty($mobile_error) && $active_modal === 'add'): ?>
                                <div class="invalid-feedback d-block text-danger font-weight-bold mt-1">
                                    <i class="fas fa-times-circle mr-1"></i>❌ <?php echo htmlspecialchars($mobile_error); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Button Text</label>
                            <input type="text" name="button_text" class="form-control" value="<?php echo htmlspecialchars($form_data['button_text'] ?? ''); ?>" placeholder="Optional button text">
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Button Link URL</label>
                            <input type="text" name="button_link" class="form-control" value="<?php echo htmlspecialchars($form_data['button_link'] ?? ''); ?>" placeholder="Optional button URL">
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Display Order</label>
                            <input type="number" name="display_order" class="form-control" value="<?php echo htmlspecialchars($form_data['display_order'] ?? '0'); ?>" min="0">
                        </div>
                        <div class="col-md-6 form-group d-flex align-items-center mt-4">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" name="status" class="custom-control-input" id="addStatus" value="1" <?php echo (!isset($form_data['status']) || $form_data['status'] == 1) ? 'checked' : ''; ?>>
                                <label class="custom-control-label" for="addStatus">Active & Visible</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Slide</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Edit Slide -->
<div class="modal fade" id="editSlideModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form action="slides.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id" id="edit_id" value="<?php echo htmlspecialchars($form_data['id'] ?? ''); ?>">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Slider Item</h5>
                    <button type="button" class="close" data-bs-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Title (Banner Text / Heading)</label>
                            <input type="text" name="title" id="edit_title" class="form-control" value="<?php echo htmlspecialchars($form_data['title'] ?? ''); ?>">
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Subtitle</label>
                            <input type="text" name="subtitle" id="edit_subtitle" class="form-control" value="<?php echo htmlspecialchars($form_data['subtitle'] ?? ''); ?>">
                        </div>
                        <div class="col-12 form-group">
                            <label>Description</label>
                            <textarea name="description" id="edit_description" class="form-control" rows="2"><?php echo htmlspecialchars($form_data['description'] ?? ''); ?></textarea>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Replace Desktop Banner</label>
                            <div class="small text-muted mb-1">
                                <strong>Auto-Processed Output:</strong> 1920 × 420 px | <strong>Min Width:</strong> 1200 px | <strong>Formats:</strong> JPG, JPEG, PNG, WEBP, SVG | <strong>Max Upload:</strong> 10 MB
                            </div>
                            <input type="file" name="desktop_image" class="form-control-file <?php echo (!empty($desktop_error) && $active_modal === 'edit') ? 'is-invalid' : ''; ?>" accept="image/*">
                            <small class="form-text text-muted d-block mt-1" id="edit_desktop_current">
                                <?php echo !empty($form_data['desktop_image']) ? 'Current: ' . htmlspecialchars($form_data['desktop_image']) : ''; ?>
                            </small>
                            <?php if (!empty($desktop_error) && $active_modal === 'edit'): ?>
                                <div class="invalid-feedback d-block text-danger font-weight-bold mt-1">
                                    <i class="fas fa-times-circle mr-1"></i>❌ <?php echo htmlspecialchars($desktop_error); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Replace Mobile Banner</label>
                            <div class="small text-muted mb-1">
                                <strong>Auto-Processed Output:</strong> 768 × 500 px | <strong>Min Width:</strong> 600 px | <strong>Formats:</strong> JPG, JPEG, PNG, WEBP, SVG | <strong>Max Upload:</strong> 10 MB
                            </div>
                            <input type="file" name="mobile_image" class="form-control-file <?php echo (!empty($mobile_error) && $active_modal === 'edit') ? 'is-invalid' : ''; ?>" accept="image/*">
                            <small class="form-text text-muted d-block mt-1" id="edit_mobile_current">
                                <?php echo !empty($form_data['mobile_image']) ? 'Current: ' . htmlspecialchars($form_data['mobile_image']) : ''; ?>
                            </small>
                            <?php if (!empty($mobile_error) && $active_modal === 'edit'): ?>
                                <div class="invalid-feedback d-block text-danger font-weight-bold mt-1">
                                    <i class="fas fa-times-circle mr-1"></i>❌ <?php echo htmlspecialchars($mobile_error); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Button Text</label>
                            <input type="text" name="button_text" id="edit_button_text" class="form-control" value="<?php echo htmlspecialchars($form_data['button_text'] ?? ''); ?>">
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Button Link URL</label>
                            <input type="text" name="button_link" id="edit_button_link" class="form-control" value="<?php echo htmlspecialchars($form_data['button_link'] ?? ''); ?>">
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Display Order</label>
                            <input type="number" name="display_order" id="edit_display_order" class="form-control" min="0" value="<?php echo htmlspecialchars($form_data['display_order'] ?? '0'); ?>">
                        </div>
                        <div class="col-md-6 form-group d-flex align-items-center mt-4">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" name="status" class="custom-control-input" id="edit_status" value="1" <?php echo (!isset($form_data['status']) || $form_data['status'] == 1) ? 'checked' : ''; ?>>
                                <label class="custom-control-label" for="edit_status">Active & Visible</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    <?php if ($active_modal === 'add'): ?>
    const addModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('addSlideModal'));
    addModal.show();
    <?php endif; ?>

    <?php if ($active_modal === 'edit'): ?>
    const editModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('editSlideModal'));
    editModal.show();
    <?php endif; ?>

    document.querySelectorAll('.btn-edit').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var data = JSON.parse(this.getAttribute('data-slide'));
            document.getElementById('edit_id').value = data.id;
            document.getElementById('edit_title').value = data.title || '';
            document.getElementById('edit_subtitle').value = data.subtitle || '';
            document.getElementById('edit_description').value = data.description || '';
            document.getElementById('edit_button_text').value = data.button_text || '';
            document.getElementById('edit_button_link').value = data.button_link || '';
            document.getElementById('edit_display_order').value = data.display_order || 0;
            document.getElementById('edit_status').checked = (parseInt(data.status) === 1);

            document.getElementById('edit_desktop_current').innerText = 'Current: ' + (data.desktop_image || 'None');
            document.getElementById('edit_mobile_current').innerText = 'Current: ' + (data.mobile_image || 'None');

            const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('editSlideModal'));
            modal.show();
        });
    });
});
</script>

