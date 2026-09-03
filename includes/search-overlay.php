<div id="globalSearchOverlay" role="dialog" aria-modal="true" aria-labelledby="pvcSearchTitle" hidden>
    <div class="pvc-search-page">
        <div class="pvc-search-header">
            <button type="button" id="pvcSearchBack" class="pvc-search-back-btn" aria-label="Close search">
                <i class="fa-solid fa-arrow-left"></i>
            </button>
            <h1 id="pvcSearchTitle" class="pvc-search-title">Search</h1>
        </div>

        <div class="pvc-search-input-wrapper">
            <i class="fa-solid fa-search" aria-hidden="true"></i>
            <input type="search" id="pvcSearchInput" class="pvc-search-input" placeholder="Search products, brands, categories" autocomplete="off" enterkeyhint="search">
            
        </div>
        <div class="pvc-search-helper">Find products, brands or categories.</div>

        <div id="pvcSearchEmptyState" class="pvc-state-box">
            <p>Start typing to see suggestions and results.</p>
        </div>

        <div id="pvcSearchLoading" class="pvc-state-box hidden">
            <div class="pvc-loader"></div>
            <p>Searching...</p>
        </div>

        <div id="pvcSearchError" class="pvc-state-box hidden">
            <p>Something went wrong. Please try again.</p>
        </div>

        <div id="pvcSearchNoResults" class="pvc-state-box hidden">
            <p>No results found for your search.</p>
        </div>

        <div id="pvcSearchContent" class="hidden">
            <div id="pvcSuggestionsSection" class="hidden">
                <div class="pvc-section-header">
                    <div class="pvc-section-heading">
                        <h2 class="pvc-section-title">Suggestions</h2>
                    </div>
                </div>
                <div class="pvc-suggestions-container" id="pvcSuggestionsContainer"></div>
            </div>

            <div id="pvcResultsSection" class="hidden">
                <div class="pvc-section-header">
                    <h2 class="pvc-section-title" id="pvcResultsCount">0 Results</h2>
                </div>
                <div class="pvc-results-container" id="pvcResultsContainer"></div>
            </div>
        </div>
    </div>
</div>
