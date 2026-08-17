<!--this is header file in PVC SECURITY website-->
<!DOCTYPE html>
<html lang="en">
 
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>PVC SECURITY SOLUTIONS - Leading the Future of AIoT Security</title>
  <meta name="description"
   content="PVC Security Solutions delivers state-of-the-art surveillance solutions, including IP cameras, network recorders, and smart security systems for homes and businesses.">
 
  <!--=====FAB ICON=======-->
  <link rel="shortcut icon" href="assets/img/logo/Untitled design-3.png" type="image/x-icon">
  <link rel="shortcut icon" href="assets/img/logo/logo1.png" type="image/x-icon">
  <!--===== CSS LINK =======-->
  <!-- CSS Plugins -->
   <link rel="stylesheet" href="assets/css/plugins/bootstrap.min.css">
   <link rel="stylesheet" href="assets/css/plugins/aos.css">
   <link rel="stylesheet" href="assets/css/plugins/fontawesome.css">
   <link rel="stylesheet" href="assets/css/plugins/magnific-popup.css">
   <link rel="stylesheet" href="assets/css/plugins/mobile.css">
   <link rel="stylesheet" href="assets/css/plugins/owlcarousel.min.css">
   <link rel="stylesheet" href="assets/css/plugins/sidebar.css">
   <link rel="stylesheet" href="assets/css/plugins/slick-slider.css">
   <link rel="stylesheet" href="assets/css/plugins/nice-select.css">
   <link rel="stylesheet" href="assets/css/main.css">
   <link rel="stylesheet" href="assets/css/global_header.css?v=<?php echo filemtime(__DIR__ . '/assets/css/global_header.css'); ?>">
   <link rel="stylesheet" href="assets/css/breadcrumbs.css">
   <link rel="stylesheet" href="assets/css/bottom-nav.css?v=<?php echo filemtime(__DIR__ . '/assets/css/bottom-nav.css'); ?>">
   <link rel ="stylesheet" href="assets/css/global_footer.css">
   <link rel="stylesheet" href="assets/css/products_styles.css">
   <link rel="stylesheet" href="assets/css/global_search.css">
   
<!-- JS Plugins -->
  <script src="assets/js/plugins/jquery-3-6-0.min.js"></script>
  <script src="assets/js/plugins/waypoints.js"></script>
  <script src="assets/js/global_search.js"></script>

  <?php $pvcIsHomePage = in_array(basename($_SERVER['SCRIPT_NAME']), ['index.php', '']); ?>

  <?php if ($pvcIsHomePage): ?>
  <!-- PRELOADER HIDE SCRIPT -->
  <script>
    // Keep preloader visible for a fixed 3 seconds, regardless of load timing
    (function() {
      function hidePreloader() {
        var preloader = document.getElementById('pvc-preloader');
        if (preloader && !preloader.classList.contains('hide')) {
          preloader.classList.add('hide');
          setTimeout(function() {
            if (document.getElementById('pvc-preloader')) {
              preloader.remove();
            }
          }, 500);
        }
      }
      setTimeout(hidePreloader, 2000);
    })();
  </script>
  <?php endif; ?>
</head>
<body>
  <?php if ($pvcIsHomePage): ?>
  <!-- PRELOADER -->
  <div id="pvc-preloader" class="pvc-preloader">
    <div class="pvc-preloader-content">
      <div class="pvc-preloader-logo-wrapper">
        <div class="pvc-logo-formation" role="img" aria-label="PVC Logo">
          <?php
            $pvcCols = 5;
            $pvcRows = 5;
            $pvcCenter = ($pvcCols - 1) / 2;
            for ($pvcR = 0; $pvcR < $pvcRows; $pvcR++) {
              for ($pvcC = 0; $pvcC < $pvcCols; $pvcC++) {
                $gx  = round($pvcC * (100 / $pvcCols), 3);
                $gy  = round($pvcR * (100 / $pvcRows), 3);
                $bgx = $pvcCols > 1 ? round($pvcC / ($pvcCols - 1) * 100, 3) : 0;
                $bgy = $pvcRows > 1 ? round($pvcR / ($pvcRows - 1) * 100, 3) : 0;
                $dx  = round(($pvcC - $pvcCenter) * 65, 1);
                $dy  = round(($pvcR - $pvcCenter) * 65, 1);
                $dist  = max(abs($pvcC - $pvcCenter), abs($pvcR - $pvcCenter));
                $delay = round($dist * 0.09, 2);
                $rot   = (($pvcR + $pvcC) % 2 === 0) ? 14 : -14;
                echo '<span class="pvc-logo-piece" style="--gx:' . $gx . '%; --gy:' . $gy . '%; --bgx:' . $bgx . '%; --bgy:' . $bgy . '%; --dx:' . $dx . 'px; --dy:' . $dy . 'px; --rot:' . $rot . 'deg; --delay:' . $delay . 's;"></span>';
              }
            }
          ?>
        </div>
      </div>

      <div class="pvc-preloader-loading">
        <span class="pvc-loading-text" aria-label="Loading">
          <span class="pvc-letter">L</span><span class="pvc-letter">o</span><span class="pvc-letter">a</span><span class="pvc-letter">d</span><span class="pvc-letter">i</span><span class="pvc-letter">n</span><span class="pvc-letter">g</span><span class="pvc-letter pvc-letter-dot">.</span><span class="pvc-letter pvc-letter-dot">.</span><span class="pvc-letter pvc-letter-dot">.</span>
        </span>
      </div>
    </div>
  </div>
  <?php endif; ?>