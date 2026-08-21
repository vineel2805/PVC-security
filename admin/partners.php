<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

require_once 'config/db.php';
require_once 'config/partners_schema.php';

// Ensure strategic_partners table exists and is seeded if empty
ensure_strategic_partners_table_exists($pdo);

$success      = '';
$error        = '';
$image_error  = '';
$name_error   = '';
$active_modal = '';
$form_data    = [];

$uploadDir = __DIR__ . '/../uploads/partners/';
if (!is_dir($uploadDir)) {
    @mkdir($uploadDir, 0755, true);
}

/**
 * Validate and process strategic partner logo upload
 * Max file size: 1.5 MB (1,572,864 bytes). No fixed dimension restrictions.
 */
function validate_and_upload_partner_image($fileArray, $uploadDir, $existingPath = '') {
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
            'error' => 'Partner logo format not allowed. Allowed formats: JPG, JPEG, PNG, WEBP, SVG.'
        ];
    }
    
    // Max upload file size: 1.5 MB (1,572,864 bytes)
    $maxBytes = (int)(1.5 * 1024 * 1024);
    if ($fileArray['size'] > $maxBytes) {
        return [
            'path'  => $existingPath,
            'error' => 'Partner logo file size exceeds maximum allowed limit of 1.5 MB.'
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
            'error' => "Partner logo invalid MIME type ({$mime}). Allowed formats: JPG, JPEG, PNG, WEBP, SVG."
        ];
    }
    
    // Save output file path preserving natural proportions and aspect ratio
    $filename = 'partner_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
    $target   = $uploadDir . $filename;
    
    if (@move_uploaded_file($fileArray['tmp_name'], $target)) {
        // Clean up old file if replacing an uploaded partner logo
        if (!empty($existingPath) && strpos($existingPath, 'uploads/partners/') === 0) {
            $oldDiskPath = __DIR__ . '/../' . $existingPath;
            if (file_exists($oldDiskPath)) {
                @unlink($oldDiskPath);
            }
        }
        return ['path' => 'uploads/partners/' . $filename, 'error' => null];
    }
    
    return ['path' => $existingPath, 'error' => 'Failed to save uploaded partner logo on server.'];
}

// ── Handle Actions ──────────────────────────────────────────────────────────

// Toggle Status
if (isset($_GET['action']) && $_GET['action'] === 'toggle_status' && isset($_GET['id'])) {
    $partnerId = (int)$_GET['id'];
    $stmt = $pdo->prepare("UPDATE strategic_partners SET is_active = IF(is_active = 1, 0, 1) WHERE id = ?");
    $stmt->execute([$partnerId]);
    header("Location: partners.php?msg=status_updated");
    exit();
}

// Delete Partner
if (isset($_POST['action']) && $_POST['action'] === 'delete' && isset($_POST['id'])) {
    $partnerId = (int)$_POST['id'];
    $stmt = $pdo->prepare("SELECT image FROM strategic_partners WHERE id = ?");
    $stmt->execute([$partnerId]);
    $partner = $stmt->fetch();
    
    if ($partner) {
        if (!empty($partner['image']) && strpos($partner['image'], 'uploads/partners/') === 0) {
            $diskPath = __DIR__ . '/../' . $partner['image'];
            if (file_exists($diskPath)) {
                @unlink($diskPath);
            }
        }
        $del = $pdo->prepare("DELETE FROM strategic_partners WHERE id = ?");
        $del->execute([$partnerId]);
        $success = "Partner deleted successfully!";
    }
}

// Add Partner
if (isset($_POST['action']) && $_POST['action'] === 'add') {
    $partner_name  = trim($_POST['partner_name'] ?? '');
    $display_order = (int)($_POST['display_order'] ?? 0);
    $is_active     = isset($_POST['is_active']) ? 1 : 0;
    
    $form_data = [
        'partner_name'  => $partner_name,
        'display_order' => $display_order,
        'is_active'     => $is_active
    ];
    
    $hasError = false;
    if (empty($partner_name)) {
        $name_error = "Partner name is required.";
        $hasError = true;
    }
    
    $imgResult = validate_and_upload_partner_image($_FILES['image'] ?? [], $uploadDir);
    if (!empty($imgResult['error'])) {
        $image_error = $imgResult['error'];
        $hasError = true;
    } elseif (empty($imgResult['path'])) {
        $image_error = "Partner logo image is required.";
        $hasError = true;
    }
    
    if ($hasError) {
        $active_modal = 'add';
    } else {
        $image = $imgResult['path'];
        $stmt = $pdo->prepare("INSERT INTO strategic_partners 
            (partner_name, image, display_order, is_active) 
            VALUES (?, ?, ?, ?)");
        $stmt->execute([$partner_name, $image, $display_order, $is_active]);
        $success = "New strategic partner added successfully!";
        $form_data = [];
    }
}

// Edit Partner
if (isset($_POST['action']) && $_POST['action'] === 'edit' && isset($_POST['id'])) {
    $partnerId     = (int)$_POST['id'];
    $partner_name  = trim($_POST['partner_name'] ?? '');
    $display_order = (int)($_POST['display_order'] ?? 0);
    $is_active     = isset($_POST['is_active']) ? 1 : 0;
    
    $stmt = $pdo->prepare("SELECT image FROM strategic_partners WHERE id = ?");
    $stmt->execute([$partnerId]);
    $curr = $stmt->fetch();
    
    $form_data = [
        'id'            => $partnerId,
        'partner_name'  => $partner_name,
        'display_order' => $display_order,
        'is_active'     => $is_active,
        'image'         => $curr['image'] ?? ''
    ];
    
    $hasError = false;
    if (empty($partner_name)) {
        $name_error = "Partner name is required.";
        $hasError = true;
    }
    
    $imgResult = validate_and_upload_partner_image($_FILES['image'] ?? [], $uploadDir, $curr['image'] ?? '');
    if (!empty($imgResult['error'])) {
        $image_error = $imgResult['error'];
        $hasError = true;
    }
    
    if ($hasError) {
        $active_modal = 'edit';
    } else {
        $image = $imgResult['path'];
        $up = $pdo->prepare("UPDATE strategic_partners SET 
            partner_name = ?, image = ?, display_order = ?, is_active = ? 
            WHERE id = ?");
        $up->execute([$partner_name, $image, $display_order, $is_active, $partnerId]);
        $success = "Strategic partner updated successfully!";
        $form_data = [];
    }
}

// Fetch all partners
$partners = $pdo->query("SELECT * FROM strategic_partners ORDER BY display_order ASC, id ASC")->fetchAll();

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
                    <h4>Strategic Partners Management</h4>
                    <p class="mb-0">Manage strategic partner company logos, names, visibility, and display ordering.</p>
                </div>
            </div>
            <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPartnerModal">
                    <i class="fas fa-plus mr-2"></i>Add New Partner
                </button>
            </div>
        </div>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <button type="button" class="close" data-bs-dismiss="alert">&times;</button>
                <strong>Success!</strong> <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['msg']) && $_GET['msg'] === 'status_updated'): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <button type="button" class="close" data-bs-dismiss="alert">&times;</button>
                <strong>Success!</strong> Partner status updated successfully.
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
                        <h4 class="card-title mb-0">Configured Strategic Partners</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-responsive-md table-hover align-middle">
                                <thead class="thead-dark">
                                    <tr>
                                        <th style="width:70px;">Order</th>
                                        <th style="width:160px;">Partner Logo</th>
                                        <th>Partner Name</th>
                                        <th style="width:100px;">Status</th>
                                        <th style="width:120px;" class="text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($partners)): ?>
                                        <tr>
                                            <td colspan="5" class="text-center py-4 text-muted">No strategic partners found. Click "Add New Partner" to create one.</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($partners as $partner): 
                                            $imgSrc = '../' . htmlspecialchars($partner['image']);
                                        ?>
                                        <tr>
                                            <td>
                                                <span class="badge badge-secondary"><?php echo (int)$partner['display_order']; ?></span>
                                            </td>
                                            <td>
                                                <div style="width:120px; height:60px; display:flex; align-items:center; justify-content:center; background:#f8f9fa; border:1px solid #e9ecef; border-radius:4px; padding:4px;">
                                                    <img src="<?php echo $imgSrc; ?>" alt="<?php echo htmlspecialchars($partner['partner_name']); ?>" style="max-width:100%; max-height:100%; object-fit:contain;">
                                                </div>
                                            </td>
                                            <td>
                                                <strong><?php echo htmlspecialchars($partner['partner_name']); ?></strong>
                                            </td>
                                            <td>
                                                <a href="partners.php?action=toggle_status&id=<?php echo $partner['id']; ?>" 
                                                   class="badge badge-<?php echo $partner['is_active'] ? 'success' : 'danger'; ?>"
                                                   title="Click to toggle status">
                                                    <?php echo $partner['is_active'] ? 'Active' : 'Disabled'; ?>
                                                </a>
                                            </td>
                                            <td class="text-right">
                                                <button class="btn btn-warning btn-xs mr-1 btn-edit" data-partner='<?php echo json_encode($partner, JSON_HEX_APOS); ?>'>
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <form action="partners.php" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this partner?');">
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="id" value="<?php echo $partner['id']; ?>">
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
   STRATEGIC PARTNERS MODAL — REDESIGNED UI (MATCHING REFERENCE)
   ========================================================================== */
.partner-item-modal .modal-dialog {
    max-width: 780px;
    margin: 1.75rem auto;
}
.partner-item-modal .modal-content {
    border: none;
    border-radius: 16px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.12), 0 4px 16px rgba(0, 0, 0, 0.06);
    background: #ffffff;
    overflow: hidden;
}
.partner-item-modal .modal-header {
    padding: 22px 28px 18px;
    border-bottom: 1px solid #f0f2f7;
    background: #ffffff;
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
}
.partner-item-modal .modal-header-icon {
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
.partner-item-modal .modal-title {
    font-size: 19px;
    font-weight: 700;
    color: #1a1d24;
    line-height: 1.25;
    margin-bottom: 2px;
}
.partner-item-modal .modal-subtitle {
    font-size: 13px;
    color: #737B8B;
    font-weight: 400;
}
.partner-item-modal .btn-close-modal {
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
.partner-item-modal .btn-close-modal:hover {
    background: #e9ecef;
    color: #1a1d24;
    border-color: #dee2e6;
}
.partner-item-modal .modal-body {
    padding: 24px 28px 16px;
    background: #ffffff;
}
.partner-item-modal .form-group {
    margin-bottom: 20px;
}
.partner-item-modal .form-label-custom {
    font-size: 13.5px;
    font-weight: 600;
    color: #2b303b;
    margin-bottom: 7px;
    display: block;
}
.partner-item-modal .form-control-custom {
    height: 46px;
    border: 1.5px solid #e2e6ee;
    border-radius: 8px;
    padding: 10px 14px;
    font-size: 14px;
    color: #2b303b;
    background: #ffffff;
    width: 100%;
    transition: border-color 0.2s, box-shadow 0.2s;
}
.partner-item-modal .form-control-custom:focus {
    border-color: #886CC0;
    box-shadow: 0 0 0 3.5px rgba(136, 108, 192, 0.14);
    outline: none;
}
.partner-item-modal .form-control-custom::placeholder {
    color: #9aa1af;
    font-size: 13.5px;
}

/* Custom Input Groups with Left Addon */
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
    font-size: 15px;
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

/* Upload Dropzone Box */
.partner-dropzone {
    border: 1.5px dashed #bba4df;
    border-radius: 10px;
    padding: 24px 16px;
    text-align: center;
    background: #fafbfd;
    cursor: pointer;
    transition: all 0.2s ease;
    margin-bottom: 12px;
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 16px;
}
.partner-dropzone:hover,
.partner-dropzone.dragover {
    border-color: #886CC0;
    background: #f8f6fc;
}
.partner-dropzone .dropzone-icon-circle {
    width: 48px;
    height: 48px;
    min-width: 48px;
    border-radius: 50%;
    background: rgba(136, 108, 192, 0.12);
    color: #886CC0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    transition: transform 0.2s ease;
}
.partner-dropzone:hover .dropzone-icon-circle {
    transform: translateY(-2px);
}
.partner-dropzone .dropzone-text-group {
    text-align: left;
}
.partner-dropzone .dropzone-primary {
    font-size: 13.5px;
    color: #2b303b;
    display: block;
}
.partner-dropzone .dropzone-primary strong {
    color: #1a1d24;
    font-weight: 600;
}
.partner-dropzone .dropzone-sub {
    font-size: 11.5px;
    color: #8a92a2;
    display: block;
    margin-top: 2px;
}
.partner-dropzone .dropzone-file-name {
    font-size: 13px;
    font-weight: 500;
    color: #553c9a;
    background: rgba(136, 108, 192, 0.1);
    border: 1px solid rgba(136, 108, 192, 0.25);
    border-radius: 6px;
    padding: 8px 14px;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    max-width: 100%;
    word-break: break-all;
}

/* Guidance Pink Pill Banner */
.partner-guidance-pill {
    background: #fff0f5;
    border: 1px solid #ffe1ec;
    color: #d63384;
    font-size: 12px;
    font-weight: 500;
    padding: 10px 16px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 8px;
    line-height: 1.4;
}
.partner-guidance-pill .pill-icon {
    font-size: 15px;
    color: #e83e8c;
    min-width: 16px;
}
.partner-guidance-pill .pill-divider {
    color: #f3a6c8;
    margin: 0 4px;
}

/* Checkbox Card / Styled Checkbox */
.custom-partner-checkbox {
    display: flex;
    align-items: flex-start;
    cursor: pointer;
    user-select: none;
    margin-bottom: 0;
    width: 100%;
}
.custom-partner-checkbox input[type="checkbox"] {
    position: absolute;
    opacity: 0;
    pointer-events: none;
}
.custom-partner-checkbox .checkbox-box {
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
.custom-partner-checkbox .checkbox-box::after {
    content: "\f00c";
    font-family: "Font Awesome 6 Free", "Font Awesome 5 Free", "FontAwesome";
    font-weight: 900;
    font-size: 11px;
}
.custom-partner-checkbox input[type="checkbox"]:checked + .checkbox-box {
    background: #886CC0;
    border-color: #886CC0;
    color: #ffffff;
}
.custom-partner-checkbox .checkbox-title {
    font-size: 14px;
    font-weight: 600;
    color: #1a1d24;
    line-height: 1.2;
}
.custom-partner-checkbox .checkbox-desc {
    font-size: 12px;
    color: #737B8B;
    margin-top: 3px;
    display: block;
}

/* Modal Footer */
.partner-item-modal .modal-footer {
    padding: 16px 28px 22px;
    border-top: 1px solid #f0f2f7;
    background: #ffffff;
    display: flex;
    justify-content: flex-end;
    gap: 12px;
}
.btn-partner-cancel {
    padding: 9px 24px;
    font-size: 13.5px;
    font-weight: 600;
    border-radius: 8px;
    background: #ffffff;
    border: 1.5px solid #e2e6ee;
    color: #495057;
    transition: all 0.2s;
}
.btn-partner-cancel:hover {
    background: #f8f9fb;
    border-color: #d5dbe7;
    color: #212529;
}
.btn-partner-save {
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
.btn-partner-save:hover,
.btn-partner-save:focus {
    background: #6c4bae;
    border-color: #6c4bae;
    color: #ffffff;
    box-shadow: 0 6px 16px rgba(136, 108, 192, 0.35);
    transform: translateY(-1px);
}
.btn-partner-save:active {
    transform: translateY(0);
}
@media (max-width: 767px) {
    .partner-item-modal .modal-header,
    .partner-item-modal .modal-body,
    .partner-item-modal .modal-footer {
        padding-left: 18px;
        padding-right: 18px;
    }
    .partner-dropzone {
        flex-direction: column;
        gap: 8px;
        text-align: center;
    }
    .partner-dropzone .dropzone-text-group {
        text-align: center;
    }
    .partner-guidance-pill {
        flex-direction: column;
        text-align: center;
        gap: 4px;
    }
    .partner-guidance-pill .pill-divider {
        display: none;
    }
}
</style>

<!-- Modal: Add Partner -->
<div class="modal fade partner-item-modal" id="addPartnerModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <form action="partners.php" method="POST" enctype="multipart/form-data" id="addPartnerForm">
                <input type="hidden" name="action" value="add">
                
                <!-- Modal Header -->
                <div class="modal-header">
                    <div class="d-flex align-items-center">
                        <div class="modal-header-icon mr-3">
                            <i class="fas fa-users"></i>
                        </div>
                        <div>
                            <h5 class="modal-title">Add New Strategic Partner</h5>
                            <p class="modal-subtitle mb-0">Add a new partner to showcase on the homepage</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close-modal" data-bs-dismiss="modal" aria-label="Close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="modal-body">
                    <div class="row">
                        <!-- Partner Name -->
                        <div class="col-md-6 form-group">
                            <label class="form-label-custom">Partner Name <span class="text-danger">*</span></label>
                            <div class="custom-input-group">
                                <span class="input-group-addon-custom"><i class="far fa-user"></i></span>
                                <input type="text" name="partner_name" class="form-control form-control-custom has-addon <?php echo (!empty($name_error) && $active_modal === 'add') ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($form_data['partner_name'] ?? ''); ?>" placeholder="e.g. Hikvision" required>
                            </div>
                            <?php if (!empty($name_error) && $active_modal === 'add'): ?>
                                <div class="invalid-feedback d-block text-danger font-weight-bold mt-1">
                                    <i class="fas fa-times-circle mr-1"></i><?php echo htmlspecialchars($name_error); ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Display Order -->
                        <div class="col-md-6 form-group">
                            <label class="form-label-custom">Display Order</label>
                            <div class="custom-input-group">
                                <span class="input-group-addon-custom"><i class="fas fa-hashtag"></i></span>
                                <input type="number" name="display_order" class="form-control form-control-custom has-addon" value="<?php echo htmlspecialchars($form_data['display_order'] ?? '0'); ?>" min="0">
                            </div>
                        </div>

                        <!-- Partner Logo / Image -->
                        <div class="col-12 form-group">
                            <label class="form-label-custom">Partner Logo / Image <span class="text-danger">*</span></label>
                            
                            <div class="partner-dropzone" data-input-id="add_partner_image">
                                <input type="file" name="image" id="add_partner_image" class="d-none dropzone-file-input <?php echo (!empty($image_error) && $active_modal === 'add') ? 'is-invalid' : ''; ?>" accept="image/*" <?php echo ($active_modal === 'add') ? '' : 'required'; ?>>
                                <div class="dropzone-content d-flex align-items-center justify-content-center">
                                    <div class="dropzone-icon-circle mr-3">
                                        <i class="fas fa-cloud-arrow-up"></i>
                                    </div>
                                    <div class="dropzone-text-group">
                                        <span class="dropzone-primary"><strong>Choose file</strong> or drag &amp; drop</span>
                                        <span class="dropzone-sub">JPG, PNG, WEBP, SVG</span>
                                    </div>
                                </div>
                                <div class="dropzone-file-name d-none"></div>
                            </div>

                            <div class="partner-guidance-pill">
                                <i class="fas fa-info-circle pill-icon"></i>
                                <span><strong>Maximum file size: 1.5 MB</strong></span>
                                <span class="pill-divider">|</span>
                                <span><strong>Recommended: 1 MB or less</strong></span>
                                <span class="pill-divider">|</span>
                                <span><strong>Formats:</strong> JPG, JPEG, PNG, WEBP, SVG</span>
                            </div>

                            <div id="add_image_js_error" class="text-danger font-weight-bold mt-1" style="display:none;"></div>
                            <?php if (!empty($image_error) && $active_modal === 'add'): ?>
                                <div class="invalid-feedback d-block text-danger font-weight-bold mt-1">
                                    <i class="fas fa-times-circle mr-1"></i>❌ <?php echo htmlspecialchars($image_error); ?>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Image Preview Box -->
                            <div class="mt-2" id="add_preview_container" style="display:none;">
                                <div style="max-width:240px; max-height:100px; background:#f8f9fa; border:1.5px dashed #886CC0; border-radius:8px; display:flex; align-items:center; justify-content:center; padding:6px; overflow:hidden;">
                                    <img id="add_preview_img" src="" alt="Preview" style="max-width:100%; max-height:88px; object-fit:contain;">
                                </div>
                                <small id="add_preview_dims" class="form-text text-muted mt-1"></small>
                            </div>
                        </div>

                        <!-- Active & Visible Checkbox -->
                        <div class="col-12 form-group pt-1">
                            <label class="custom-partner-checkbox" for="addIsActive">
                                <input type="checkbox" name="is_active" id="addIsActive" value="1" <?php echo (!isset($form_data['is_active']) || $form_data['is_active'] == 1) ? 'checked' : ''; ?>>
                                <span class="checkbox-box"></span>
                                <span class="checkbox-label-block">
                                    <strong class="d-block checkbox-title">Active &amp; Visible on Homepage</strong>
                                    <small class="checkbox-desc">Enable this partner to show on the website</small>
                                </span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-partner-cancel" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-partner-save" id="addSubmitBtn"><i class="fas fa-floppy-disk mr-2"></i>Save Partner</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Edit Partner -->
<div class="modal fade partner-item-modal" id="editPartnerModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <form action="partners.php" method="POST" enctype="multipart/form-data" id="editPartnerForm">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id" id="edit_id" value="<?php echo htmlspecialchars($form_data['id'] ?? ''); ?>">
                
                <!-- Modal Header -->
                <div class="modal-header">
                    <div class="d-flex align-items-center">
                        <div class="modal-header-icon mr-3">
                            <i class="fas fa-pen-to-square"></i>
                        </div>
                        <div>
                            <h5 class="modal-title">Edit Strategic Partner</h5>
                            <p class="modal-subtitle mb-0">Update partner details, logo and homepage visibility</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close-modal" data-bs-dismiss="modal" aria-label="Close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="modal-body">
                    <div class="row">
                        <!-- Partner Name -->
                        <div class="col-md-6 form-group">
                            <label class="form-label-custom">Partner Name <span class="text-danger">*</span></label>
                            <div class="custom-input-group">
                                <span class="input-group-addon-custom"><i class="far fa-user"></i></span>
                                <input type="text" name="partner_name" id="edit_partner_name" class="form-control form-control-custom has-addon <?php echo (!empty($name_error) && $active_modal === 'edit') ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($form_data['partner_name'] ?? ''); ?>" required>
                            </div>
                            <?php if (!empty($name_error) && $active_modal === 'edit'): ?>
                                <div class="invalid-feedback d-block text-danger font-weight-bold mt-1">
                                    <i class="fas fa-times-circle mr-1"></i><?php echo htmlspecialchars($name_error); ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Display Order -->
                        <div class="col-md-6 form-group">
                            <label class="form-label-custom">Display Order</label>
                            <div class="custom-input-group">
                                <span class="input-group-addon-custom"><i class="fas fa-hashtag"></i></span>
                                <input type="number" name="display_order" id="edit_display_order" class="form-control form-control-custom has-addon" min="0" value="<?php echo htmlspecialchars($form_data['display_order'] ?? '0'); ?>">
                            </div>
                        </div>

                        <!-- Replace Partner Logo -->
                        <div class="col-12 form-group">
                            <label class="form-label-custom">Replace Partner Logo</label>
                            
                            <div class="partner-dropzone" data-input-id="edit_partner_image">
                                <input type="file" name="image" id="edit_partner_image" class="d-none dropzone-file-input <?php echo (!empty($image_error) && $active_modal === 'edit') ? 'is-invalid' : ''; ?>" accept="image/*">
                                <div class="dropzone-content d-flex align-items-center justify-content-center">
                                    <div class="dropzone-icon-circle mr-3">
                                        <i class="fas fa-cloud-arrow-up"></i>
                                    </div>
                                    <div class="dropzone-text-group">
                                        <span class="dropzone-primary"><strong>Choose file</strong> or drag &amp; drop</span>
                                        <span class="dropzone-sub">JPG, PNG, WEBP, SVG &bull; Max 1.5 MB</span>
                                    </div>
                                </div>
                                <div class="dropzone-file-name d-none"></div>
                            </div>

                            <div class="partner-guidance-pill">
                                <i class="fas fa-info-circle pill-icon"></i>
                                <span><strong>Maximum file size: 1.5 MB</strong></span>
                                <span class="pill-divider">|</span>
                                <span><strong>Recommended: 1 MB or less</strong></span>
                                <span class="pill-divider">|</span>
                                <span><strong>Formats:</strong> JPG, JPEG, PNG, WEBP, SVG</span>
                            </div>

                            <small class="form-text text-muted d-block mt-1" id="edit_image_current">
                                <?php echo !empty($form_data['image']) ? 'Current: ' . htmlspecialchars($form_data['image']) : ''; ?>
                            </small>

                            <div id="edit_image_js_error" class="text-danger font-weight-bold mt-1" style="display:none;"></div>
                            <?php if (!empty($image_error) && $active_modal === 'edit'): ?>
                                <div class="invalid-feedback d-block text-danger font-weight-bold mt-1">
                                    <i class="fas fa-times-circle mr-1"></i>❌ <?php echo htmlspecialchars($image_error); ?>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Image Preview Box -->
                            <div class="mt-2" id="edit_preview_container">
                                <div style="max-width:240px; max-height:100px; background:#f8f9fa; border:1.5px dashed #886CC0; border-radius:8px; display:flex; align-items:center; justify-content:center; padding:6px; overflow:hidden;">
                                    <img id="edit_preview_img" src="<?php echo !empty($form_data['image']) ? '../' . htmlspecialchars($form_data['image']) : ''; ?>" alt="Preview" style="max-width:100%; max-height:88px; object-fit:contain;">
                                </div>
                                <small id="edit_preview_dims" class="form-text text-muted mt-1"></small>
                            </div>
                        </div>

                        <!-- Active & Visible Checkbox -->
                        <div class="col-12 form-group pt-1">
                            <label class="custom-partner-checkbox" for="edit_is_active">
                                <input type="checkbox" name="is_active" id="edit_is_active" value="1" <?php echo (!isset($form_data['is_active']) || $form_data['is_active'] == 1) ? 'checked' : ''; ?>>
                                <span class="checkbox-box"></span>
                                <span class="checkbox-label-block">
                                    <strong class="d-block checkbox-title">Active &amp; Visible on Homepage</strong>
                                    <small class="checkbox-desc">Enable this partner to show on the website</small>
                                </span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-partner-cancel" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-partner-save" id="editSubmitBtn"><i class="fas fa-floppy-disk mr-2"></i>Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
window.AdminPageInits = window.AdminPageInits || {};
window.initPartnersPage = function initPartnersPage() {
    if (!document.getElementById('addPartnerModal') && !document.getElementById('editPartnerModal')) {
        return;
    }

    function initPartnerDropzones() {
        document.querySelectorAll('.partner-dropzone').forEach(function (dropzone) {
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

    initPartnerDropzones();

    // Reset dropzones on modal close
    ['addPartnerModal', 'editPartnerModal'].forEach(function (modalId) {
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
    const addModalEl = document.getElementById('addPartnerModal');
    if (addModalEl) {
        bootstrap.Modal.getOrCreateInstance(addModalEl).show();
    }
    <?php endif; ?>

    <?php if ($active_modal === 'edit'): ?>
    const editModalEl = document.getElementById('editPartnerModal');
    if (editModalEl) {
        bootstrap.Modal.getOrCreateInstance(editModalEl).show();
    }
    <?php endif; ?>

    $(document)
        .off('click.partnersEdit', '.btn-edit[data-partner]')
        .on('click.partnersEdit', '.btn-edit[data-partner]', function () {
            var data = JSON.parse(this.getAttribute('data-partner'));
            document.getElementById('edit_id').value = data.id;
            document.getElementById('edit_partner_name').value = data.partner_name || '';
            document.getElementById('edit_display_order').value = data.display_order || 0;
            document.getElementById('edit_is_active').checked = (parseInt(data.is_active) === 1);

            document.getElementById('edit_image_current').innerText = 'Current: ' + (data.image || 'None');
            if (data.image) {
                document.getElementById('edit_preview_img').src = '../' + data.image;
                document.getElementById('edit_preview_container').style.display = 'block';
            } else {
                document.getElementById('edit_preview_img').src = '';
                document.getElementById('edit_preview_container').style.display = 'none';
            }
            document.getElementById('edit_image_js_error').style.display = 'none';

            // Reset dropzone files in edit modal
            const editModalEl = document.getElementById('editPartnerModal');
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
                bootstrap.Modal.getOrCreateInstance(editModalEl).show();
            }
        });

    function bindPartnerImageValidation(fileInputId, previewContainerId, previewImgId, dimsId, errorDivId, submitBtnId, ns) {
        $(document).off('change.' + ns, '#' + fileInputId).on('change.' + ns, '#' + fileInputId, function () {
            const container = document.getElementById(previewContainerId);
            const img = document.getElementById(previewImgId);
            const dims = document.getElementById(dimsId);
            const errorDiv = document.getElementById(errorDivId);
            const submitBtn = document.getElementById(submitBtnId);

            if (!errorDiv || !img || !container) {
                return;
            }

            errorDiv.style.display = 'none';
            errorDiv.innerHTML = '';
            if (submitBtn) submitBtn.disabled = false;

            if (this.files && this.files[0]) {
                const file = this.files[0];
                const maxBytes = 1.5 * 1024 * 1024;

                if (file.size > maxBytes) {
                    const uploadedMb = (file.size / (1024 * 1024)).toFixed(2);
                    errorDiv.innerHTML = `<i class="fas fa-times-circle mr-1"></i>❌ Partner logo file size (${uploadedMb} MB) exceeds maximum allowed limit of 1.5 MB.`;
                    errorDiv.style.display = 'block';
                    if (submitBtn) submitBtn.disabled = true;
                    return;
                }

                const isSvg = file.name.toLowerCase().endsWith('.svg') || file.type === 'image/svg+xml';

                const reader = new FileReader();
                reader.onload = function (e) {
                    img.src = e.target.result;
                    container.style.display = 'block';

                    if (dims) {
                        if (isSvg) {
                            dims.innerText = 'SVG Vector Image selected';
                        } else {
                            const tempImg = new Image();
                            tempImg.onload = function () {
                                dims.innerText = `Detected dimensions: ${tempImg.width} × ${tempImg.height} px`;
                            };
                            tempImg.src = e.target.result;
                        }
                    }
                };
                reader.readAsDataURL(file);
            }
        });
    }

    bindPartnerImageValidation('add_partner_image', 'add_preview_container', 'add_preview_img', 'add_preview_dims', 'add_image_js_error', 'addSubmitBtn', 'partnersAddImage');
    bindPartnerImageValidation('edit_partner_image', 'edit_preview_container', 'edit_preview_img', 'edit_preview_dims', 'edit_image_js_error', 'editSubmitBtn', 'partnersEditImage');
};
window.AdminPageInits['partners.php'] = window.initPartnersPage;
</script>
</div><!-- /.content-body -->
<?php
if (!isset($_GET['partial'])) {
    include 'footer.php';
}
?>
