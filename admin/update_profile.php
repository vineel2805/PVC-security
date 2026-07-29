<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

require_once 'config/db.php';

// Fetch the current admin user details
$stmt = $pdo->prepare("SELECT id, username, profile_image FROM users WHERE username = ?");
$stmt->execute([$_SESSION['username']]);
$user = $stmt->fetch();

if (!$user) {
    die("User session invalid or user not found.");
}

$success = '';
$error = '';

// Handle file upload or delete actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'upload') {
        if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK) {
            $fileTmp    = $_FILES['profile_pic']['tmp_name'];
            $fileName   = $_FILES['profile_pic']['name'];
            $fileSize   = $_FILES['profile_pic']['size'];
            $ext        = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            $allowedExt = ['jpg', 'jpeg', 'png', 'webp'];
            
            // Validate extension
            if (!in_array($ext, $allowedExt, true)) {
                $error = "Invalid file extension. Allowed extensions are: " . implode(', ', $allowedExt);
            } 
            // Validate size (max 2MB)
            elseif ($fileSize > 2 * 1024 * 1024) {
                $error = "File is too large. Maximum allowed size is 2MB.";
            } 
            else {
                // Validate MIME type securely
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mimeType = finfo_file($finfo, $fileTmp);
                finfo_close($finfo);
                
                $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/webp'];
                if (!in_array($mimeType, $allowedMimeTypes, true)) {
                    $error = "Invalid file type. Only JPG, PNG, and WEBP images are allowed.";
                } else {
                    // Create upload directory if it does not exist
                    $uploadDir = __DIR__ . '/../uploads/profiles/';
                    if (!is_dir($uploadDir)) {
                        @mkdir($uploadDir, 0755, true);
                    }
                    
                    // Generate a unique filename using username and timestamp
                    $newFileName = 'avatar_' . preg_replace('/[^a-zA-Z0-9_-]/', '', strtolower($user['username'])) . '_' . time() . '.' . $ext;
                    $savePath = $uploadDir . $newFileName;
                    $dbRelativePath = 'uploads/profiles/' . $newFileName;
                    
                    if (move_uploaded_file($fileTmp, $savePath)) {
                        // Delete old profile picture if it exists
                        if (!empty($user['profile_image'])) {
                            $oldFilePath = __DIR__ . '/../' . $user['profile_image'];
                            if (file_exists($oldFilePath) && is_file($oldFilePath)) {
                                @unlink($oldFilePath);
                            }
                        }
                        
                        // Update DB
                        $updateStmt = $pdo->prepare("UPDATE users SET profile_image = ? WHERE id = ?");
                        $updateStmt->execute([$dbRelativePath, $user['id']]);
                        
                        // Sync Session
                        $_SESSION['admin_profile_image'] = $dbRelativePath;
                        
                        // Refresh user data
                        $user['profile_image'] = $dbRelativePath;
                        
                        $success = "Profile picture updated successfully!";
                    } else {
                        $error = "Failed to save the uploaded file.";
                    }
                }
            }
        } else {
            $fileError = $_FILES['profile_pic']['error'] ?? UPLOAD_ERR_NO_FILE;
            if ($fileError === UPLOAD_ERR_INI_SIZE || $fileError === UPLOAD_ERR_FORM_SIZE) {
                $error = "File is too large. Maximum allowed size is 2MB.";
            } elseif ($fileError === UPLOAD_ERR_NO_FILE) {
                $error = "Please select an image file to upload.";
            } else {
                $error = "File upload failed with error code: " . $fileError;
            }
        }
    } elseif (isset($_POST['action']) && $_POST['action'] === 'delete') {
        // Delete old profile picture if it exists
        if (!empty($user['profile_image'])) {
            $oldFilePath = __DIR__ . '/../' . $user['profile_image'];
            if (file_exists($oldFilePath) && is_file($oldFilePath)) {
                @unlink($oldFilePath);
            }
        }
        
        // Update DB
        $updateStmt = $pdo->prepare("UPDATE users SET profile_image = NULL WHERE id = ?");
        $updateStmt->execute([$user['id']]);
        
        // Sync Session
        $_SESSION['admin_profile_image'] = null;
        
        // Refresh user data
        $user['profile_image'] = null;
        
        $success = "Profile picture removed successfully.";
    }
}

// Layout components
include 'header.php';
include 'nav_header.php';
include 'main_header.php';
include 'sidebar.php';
?>

<div class="content-body default-height">
    <div class="container-fluid pt-4">
        <h1>Profile Settings</h1>
        
        <div class="row justify-content-center mt-4">
            <div class="col-md-6 col-lg-5">
                <div class="card">
                    <div class="card-header text-center">
                        <h4 class="card-title mb-0"><i class="fa fa-user-circle me-2 text-primary"></i>Profile Details</h4>
                    </div>
                    <div class="card-body text-center">
                        <!-- Alerts -->
                        <?php if (!empty($success)): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fa fa-check-circle me-2"></i><?= htmlspecialchars($success) ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($error)): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fa fa-exclamation-circle me-2"></i><?= htmlspecialchars($error) ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <!-- Profile Picture Frame -->
                        <div class="mb-4">
                            <?php
                            $current_avatar = 'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="%23886cc0"><rect width="24" height="24" fill="%23f1edf7"/><circle cx="12" cy="8" r="4" fill="%23886cc0"/><path d="M12 14c-6.1 0-8 4-8 4v2h16v-2s-1.9-4-8-4z" fill="%23886cc0"/></svg>';
                            if (!empty($user['profile_image'])) {
                                $avatar_path = __DIR__ . '/../' . $user['profile_image'];
                                if (file_exists($avatar_path)) {
                                    $current_avatar = '../' . $user['profile_image'];
                                }
                            }
                            ?>
                            <div class="position-relative d-inline-block">
                                <img src="<?php echo htmlspecialchars($current_avatar); ?>" id="profilePageAvatar" class="img-thumbnail rounded-circle" alt="Admin Avatar" style="width: 150px; height: 150px; object-fit: cover; border: 4px solid #886cc0;">
                            </div>
                        </div>

                        <!-- Details -->
                        <h3 class="mb-1 text-dark"><?= htmlspecialchars($user['username']) ?></h3>
                        <p class="text-muted mb-4">Administrator</p>

                        <div class="d-flex justify-content-center gap-2 mb-2">
                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#uploadPicModal">
                                <i class="fa fa-upload me-1"></i> Update Picture
                            </button>
                            
                            <?php if (!empty($user['profile_image'])): ?>
                                <form action="update_profile.php" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to remove your profile picture?');">
                                    <input type="hidden" name="action" value="delete">
                                    <button type="submit" class="btn btn-outline-danger btn-sm">
                                        <i class="fa fa-trash me-1"></i> Remove
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Upload Modal -->
<div class="modal fade" id="uploadPicModal" tabindex="-1" aria-labelledby="uploadPicModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadPicModalLabel"><i class="fa fa-image me-2 text-primary"></i>Upload Profile Picture</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="update_profile.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="upload">
                <div class="modal-body text-center">
                    <p class="text-muted">Choose a JPG, PNG, or WEBP image. Maximum file size is 2MB.</p>
                    
                    <!-- Preview Area -->
                    <div class="mb-4 d-flex justify-content-center">
                        <div style="width: 140px; height: 140px; border: 2px dashed #cbd5e1; border-radius: 50%; padding: 5px; display: flex; align-items: center; justify-content: center; overflow: hidden; background: #f8fafc;">
                            <img id="imagePreview" src="<?php echo htmlspecialchars($current_avatar); ?>" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%; display: block;" alt="Live Preview">
                        </div>
                    </div>
                    
                    <!-- File Input -->
                    <div class="mb-3">
                        <input class="form-control" type="file" name="profile_pic" id="profilePicInput" accept="image/png, image/jpeg, image/webp" required>
                        <div class="invalid-feedback text-start">
                            Please select a valid image file.
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="fa fa-save me-1"></i> Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const fileInput = document.getElementById('profilePicInput');
    const imagePreview = document.getElementById('imagePreview');
    const defaultAvatar = <?php echo json_encode($current_avatar); ?>;

    if (fileInput && imagePreview) {
        fileInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                // Perform quick client-side validation
                const allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
                const maxSize = 2 * 1024 * 1024; // 2MB
                
                if (!allowedTypes.includes(file.type)) {
                    alert('Invalid file format. Please select a JPG, PNG, or WEBP image.');
                    this.value = '';
                    imagePreview.src = defaultAvatar;
                    return;
                }
                
                if (file.size > maxSize) {
                    alert('File is too large. Maximum file size is 2MB.');
                    this.value = '';
                    imagePreview.src = defaultAvatar;
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                }
                reader.readAsDataURL(file);
            } else {
                imagePreview.src = defaultAvatar;
            }
        });
    }
});
</script>

<?php
include 'footer.php';
?>
