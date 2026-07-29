<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Define default avatar fallback or load user avatar
$avatar_src = 'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="%23886cc0"><rect width="24" height="24" fill="%23f1edf7"/><circle cx="12" cy="8" r="4" fill="%23886cc0"/><path d="M12 14c-6.1 0-8 4-8 4v2h16v-2s-1.9-4-8-4z" fill="%23886cc0"/></svg>';
if (isset($_SESSION['admin_profile_image']) && !empty($_SESSION['admin_profile_image'])) {
    $avatar_path = __DIR__ . '/../' . $_SESSION['admin_profile_image'];
    if (file_exists($avatar_path)) {
        $avatar_src = '../' . $_SESSION['admin_profile_image'];
    }
}
?>
<!--**********************************
    Header start
***********************************-->
<div class="header">
    <div class="header-content">
        <nav class="navbar navbar-expand">
            <div class="collapse navbar-collapse justify-content-between">
                <div class="header-left">
                    <div class="dashboard_bar">
                        Admin Dashboard
                    </div>
                </div>
                <ul class="navbar-nav header-right">

                    <li class="nav-item dropdown header-profile">
                        <a class="nav-link" href="javascript:void(0);" role="button" data-bs-toggle="dropdown">
                            <img src="<?php echo htmlspecialchars($avatar_src); ?>" width="56" alt="Admin Avatar" style="object-fit: cover; aspect-ratio: 1/1;" />
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a href="update_profile.php" class="dropdown-item ai-icon">
                                <svg id="icon-user1" xmlns="http://www.w3.org/2000/svg" class="text-primary" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                                <span class="ms-2">Profile Settings </span>
                            </a>
                            <a href="index.php" class="dropdown-item ai-icon">
                                <svg id="icon-logout" xmlns="http://www.w3.org/2000/svg" class="text-danger" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                                <span class="ms-2">Logout </span>
                            </a>
                        </div>
                    </li>
                </ul>
            </div>
        </nav>
    </div>
</div>
<!--**********************************
    Header end
***********************************-->
