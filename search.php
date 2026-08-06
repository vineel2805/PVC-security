<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search - PVC Global</title>
    <meta name="description" content="Search PVC Security's premium range of CCTV and AIoT surveillance solutions.">
    <?php include 'head.php'; ?>
    <link rel="stylesheet" href="assets/css/global-search.css">
    <script src="assets/js/global-search.js"></script>
    <script src="assets/js/plugins/jquery-3-6-0.min.js"></script>
    
</head>
<body>
<?php include 'header.php'; ?>

    <div class="pvc-search-page">
        <div class="pvc-search-header">
            <a href="javascript:history.back()" class="pvc-search-back-btn"><i class="fa-solid fa-arrow-left"></i></a>
            <h1 class="pvc-search-title">Search</h1>
        </div>

        <div class="pvc-search-input-wrapper">
            <i class="fa-solid fa-search"></i>
            <input type="text" id="pvcSearchInput" class="pvc-search-input" placeholder="Search" autocomplete="off" autofocus>
            <button type="button" id="pvcSearchClear" class="pvc-search-clear"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="pvc-search-helper">Find products, brands or categories.</div>

        <div id="pvcSearchEmptyState" class="pvc-state-box">
            <i class="fa-solid fa-magnifying-glass" style="font-size: 32px; color: #ddd; margin-bottom: 16px;"></i>
            <p>Start typing to see suggestions and results.</p>
        </div>

        <div id="pvcSearchLoading" class="pvc-state-box hidden">
            <div class="pvc-loader"></div>
            <p>Searching...</p>
        </div>

        <div id="pvcSearchError" class="pvc-state-box hidden">
            <i class="fa-solid fa-circle-exclamation" style="font-size: 32px; color: #ff6b6b; margin-bottom: 16px;"></i>
            <p>Something went wrong. Please try again.</p>
        </div>
        
        <div id="pvcSearchNoResults" class="pvc-state-box hidden">
            <i class="fa-solid fa-box-open" style="font-size: 32px; color: #ddd; margin-bottom: 16px;"></i>
            <p>No results found for your search.</p>
        </div>

        <div id="pvcSearchContent" class="hidden">
            <div id="pvcSuggestionsSection" class="hidden">
                <div class="pvc-section-header">
                    <div style="display:flex; align-items:center;">
                        <h2 class="pvc-section-title">Suggestions</h2>
                        <span class="pvc-badge-top">Top</span>
                    </div>
                </div>
                <div class="pvc-suggestions-container" id="pvcSuggestionsContainer"></div>
            </div>

            <div id="pvcResultsSection" class="hidden">
                <div class="pvc-section-header">
                    <h2 class="pvc-section-title" id="pvcResultsCount">0 Results</h2>
                    <div style="font-size:13px; color:#666;">Sort by <span style="color:#c9a14a; font-weight:600;">Relevance <i class="fa-solid fa-chevron-down"></i></span></div>
                </div>
                <div class="pvc-results-container" id="pvcResultsContainer"></div>
            </div>
        </div>
    </div>

   

    <script src="assets/js/plugins/bootstrap.min.js"></script>
    <script src="assets/js/global_footer.js"></script>
</body>
</html>
