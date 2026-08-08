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
</div>

<!-- Modal: Add Partner -->
<div class="modal fade" id="addPartnerModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form action="partners.php" method="POST" enctype="multipart/form-data" id="addPartnerForm">
                <input type="hidden" name="action" value="add">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Strategic Partner</h5>
                    <button type="button" class="close" data-bs-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Partner Name <span class="text-danger">*</span></label>
                            <input type="text" name="partner_name" class="form-control <?php echo (!empty($name_error) && $active_modal === 'add') ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($form_data['partner_name'] ?? ''); ?>" placeholder="e.g. Hikvision" required>
                            <?php if (!empty($name_error) && $active_modal === 'add'): ?>
                                <div class="invalid-feedback d-block text-danger font-weight-bold mt-1">
                                    <i class="fas fa-times-circle mr-1"></i><?php echo htmlspecialchars($name_error); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Display Order</label>
                            <input type="number" name="display_order" class="form-control" value="<?php echo htmlspecialchars($form_data['display_order'] ?? '0'); ?>" min="0">
                        </div>
                        <div class="col-12 form-group">
                            <label>Partner Logo / Image <span class="text-danger">*</span></label>
                            <div class="alert alert-info py-2 px-3 mb-2 small">
                                <i class="fas fa-info-circle mr-1"></i> <strong>Maximum file size: 1.5 MB</strong> | <strong>Recommended: 1 MB or less</strong> | <strong>Formats:</strong> JPG, JPEG, PNG, WEBP, SVG
                            </div>
                            <input type="file" name="image" id="add_partner_image" class="form-control-file <?php echo (!empty($image_error) && $active_modal === 'add') ? 'is-invalid' : ''; ?>" accept="image/*" <?php echo ($active_modal === 'add') ? '' : 'required'; ?>>
                            <div id="add_image_js_error" class="text-danger font-weight-bold mt-1" style="display:none;"></div>
                            <?php if (!empty($image_error) && $active_modal === 'add'): ?>
                                <div class="invalid-feedback d-block text-danger font-weight-bold mt-1">
                                    <i class="fas fa-times-circle mr-1"></i>❌ <?php echo htmlspecialchars($image_error); ?>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Image Preview Box -->
                            <div class="mt-3" id="add_preview_container" style="display:none;">
                                <label class="d-block small text-muted">Selected Image Preview:</label>
                                <div style="max-width:280px; max-height:140px; background:#f8f9fa; border:2px dashed #007bff; border-radius:6px; display:flex; align-items:center; justify-content:center; padding:8px; overflow:hidden;">
                                    <img id="add_preview_img" src="" alt="Preview" style="max-width:100%; max-height:120px; object-fit:contain;">
                                </div>
                                <small id="add_preview_dims" class="form-text text-muted mt-1"></small>
                            </div>
                        </div>
                        <div class="col-md-6 form-group d-flex align-items-center mt-2">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" name="is_active" class="custom-control-input" id="addIsActive" value="1" <?php echo (!isset($form_data['is_active']) || $form_data['is_active'] == 1) ? 'checked' : ''; ?>>
                                <label class="custom-control-label" for="addIsActive">Active & Visible on Homepage</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="addSubmitBtn">Save Partner</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Edit Partner -->
<div class="modal fade" id="editPartnerModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form action="partners.php" method="POST" enctype="multipart/form-data" id="editPartnerForm">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id" id="edit_id" value="<?php echo htmlspecialchars($form_data['id'] ?? ''); ?>">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Strategic Partner</h5>
                    <button type="button" class="close" data-bs-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Partner Name <span class="text-danger">*</span></label>
                            <input type="text" name="partner_name" id="edit_partner_name" class="form-control <?php echo (!empty($name_error) && $active_modal === 'edit') ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($form_data['partner_name'] ?? ''); ?>" required>
                            <?php if (!empty($name_error) && $active_modal === 'edit'): ?>
                                <div class="invalid-feedback d-block text-danger font-weight-bold mt-1">
                                    <i class="fas fa-times-circle mr-1"></i><?php echo htmlspecialchars($name_error); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Display Order</label>
                            <input type="number" name="display_order" id="edit_display_order" class="form-control" min="0" value="<?php echo htmlspecialchars($form_data['display_order'] ?? '0'); ?>">
                        </div>
                        <div class="col-12 form-group">
                            <label>Replace Partner Logo</label>
                            <div class="alert alert-info py-2 px-3 mb-2 small">
                                <i class="fas fa-info-circle mr-1"></i> <strong>Maximum file size: 1.5 MB</strong> | <strong>Recommended: 1 MB or less</strong> | <strong>Formats:</strong> JPG, JPEG, PNG, WEBP, SVG
                            </div>
                            <input type="file" name="image" id="edit_partner_image" class="form-control-file <?php echo (!empty($image_error) && $active_modal === 'edit') ? 'is-invalid' : ''; ?>" accept="image/*">
                            <div id="edit_image_js_error" class="text-danger font-weight-bold mt-1" style="display:none;"></div>
                            <small class="form-text text-muted d-block mt-1" id="edit_image_current">
                                <?php echo !empty($form_data['image']) ? 'Current: ' . htmlspecialchars($form_data['image']) : ''; ?>
                            </small>
                            <?php if (!empty($image_error) && $active_modal === 'edit'): ?>
                                <div class="invalid-feedback d-block text-danger font-weight-bold mt-1">
                                    <i class="fas fa-times-circle mr-1"></i>❌ <?php echo htmlspecialchars($image_error); ?>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Image Preview Box -->
                            <div class="mt-3" id="edit_preview_container">
                                <label class="d-block small text-muted">Current / New Preview:</label>
                                <div style="max-width:280px; max-height:140px; background:#f8f9fa; border:2px dashed #6c757d; border-radius:6px; display:flex; align-items:center; justify-content:center; padding:8px; overflow:hidden;">
                                    <img id="edit_preview_img" src="<?php echo !empty($form_data['image']) ? '../' . htmlspecialchars($form_data['image']) : ''; ?>" alt="Preview" style="max-width:100%; max-height:120px; object-fit:contain;">
                                </div>
                                <small id="edit_preview_dims" class="form-text text-muted mt-1"></small>
                            </div>
                        </div>
                        <div class="col-md-6 form-group d-flex align-items-center mt-2">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" name="is_active" class="custom-control-input" id="edit_is_active" value="1" <?php echo (!isset($form_data['is_active']) || $form_data['is_active'] == 1) ? 'checked' : ''; ?>>
                                <label class="custom-control-label" for="edit_is_active">Active & Visible on Homepage</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="editSubmitBtn">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    <?php if ($active_modal === 'add'): ?>
    const addModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('addPartnerModal'));
    addModal.show();
    <?php endif; ?>

    <?php if ($active_modal === 'edit'): ?>
    const editModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('editPartnerModal'));
    editModal.show();
    <?php endif; ?>

    // Edit button handler
    document.querySelectorAll('.btn-edit').forEach(function(btn) {
        btn.addEventListener('click', function() {
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
            }
            document.getElementById('edit_image_js_error').style.display = 'none';

            const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('editPartnerModal'));
            modal.show();
        });
    });

    // Client-side image preview and max file size (1.5 MB) validation
    function setupImageValidation(fileInputId, previewContainerId, previewImgId, dimsId, errorDivId, submitBtnId) {
        const fileInput = document.getElementById(fileInputId);
        const container = document.getElementById(previewContainerId);
        const img = document.getElementById(previewImgId);
        const dims = document.getElementById(dimsId);
        const errorDiv = document.getElementById(errorDivId);
        const submitBtn = document.getElementById(submitBtnId);

        if (!fileInput) return;

        fileInput.addEventListener('change', function() {
            errorDiv.style.display = 'none';
            errorDiv.innerHTML = '';
            if (submitBtn) submitBtn.disabled = false;

            if (this.files && this.files[0]) {
                const file = this.files[0];
                const maxBytes = 1.5 * 1024 * 1024; // 1,572,864 bytes

                // Validate maximum file size: 1.5 MB
                if (file.size > maxBytes) {
                    const uploadedMb = (file.size / (1024 * 1024)).toFixed(2);
                    errorDiv.innerHTML = `<i class="fas fa-times-circle mr-1"></i>❌ Partner logo file size (${uploadedMb} MB) exceeds maximum allowed limit of 1.5 MB.`;
                    errorDiv.style.display = 'block';
                    if (submitBtn) submitBtn.disabled = true;
                    return;
                }

                const isSvg = file.name.toLowerCase().endsWith('.svg') || file.type === 'image/svg+xml';

                const reader = new FileReader();
                reader.onload = function(e) {
                    img.src = e.target.result;
                    container.style.display = 'block';

                    if (isSvg) {
                        dims.innerText = 'SVG Vector Image selected';
                    } else {
                        const tempImg = new Image();
                        tempImg.onload = function() {
                            dims.innerText = `Detected dimensions: ${tempImg.width} × ${tempImg.height} px`;
                        };
                        tempImg.src = e.target.result;
                    }
                };
                reader.readAsDataURL(file);
            }
        });
    }

    setupImageValidation('add_partner_image', 'add_preview_container', 'add_preview_img', 'add_preview_dims', 'add_image_js_error', 'addSubmitBtn');
    setupImageValidation('edit_partner_image', 'edit_preview_container', 'edit_preview_img', 'edit_preview_dims', 'edit_image_js_error', 'editSubmitBtn');
});
</script>
