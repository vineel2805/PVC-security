<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search - PVC Global</title>
    <meta name="description" content="Search PVC Security's premium range of CCTV and AIoT surveillance solutions.">
    <?php include 'head.php'; ?>
    

    <script src="assets/js/plugins/jquery-3-6-0.min.js"></script>
    <style>
        body {
            background-color: #ffffff;
            color: #111111;
        }
        
        .pvc-search-page {
            padding: 16px 16px 240px 16px;
            font-family: 'Inter', sans-serif;
            max-width: 600px;
            margin: 0 auto;
        }

        .pvc-search-header {
            display: flex;
            align-items: center;
            margin-bottom: 24px;
            gap: 16px;
        }
        .pvc-search-back-btn {
            color: #111111;
            font-size: 20px;
            text-decoration: none;
        }
        .pvc-search-title {
            font-size: 24px;
            font-weight: 700;
            color: #111111;
            margin: 0;
            font-family: 'Outfit', sans-serif;
        }

        .pvc-search-input-wrapper {
            position: relative;
            margin-bottom: 12px;
        }
        .pvc-search-input-wrapper i.fa-search {
            position: absolute;
            left: 22px;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
            font-size: 18px;
            line-height: 1;
            pointer-events: none;
        }
        .pvc-search-input {
            width: 100%;
            padding: 16px 48px 16px 56px !important;
            border-radius: 30px;
            border: 1px solid #c9a14a;
            font-size: 16px;
            line-height: normal;
            box-sizing: border-box;
            outline: none;
            background: #ffffff;
            color: #111111;
            box-shadow: 0 4px 15px rgba(201, 161, 74, 0.1);
            transition: all 0.3s ease;
        }
        .pvc-search-input:focus {
            box-shadow: 0 4px 20px rgba(201, 161, 74, 0.25);
            border-color: #b8860b;
        }
        .pvc-search-clear {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
            font-size: 16px;
            background: none;
            border: none;
            padding: 8px;
            margin: 0;
            line-height: 1;
            cursor: pointer;
            display: none;
        }
        
        .pvc-search-helper {
            font-size: 13px;
            color: #666;
            margin-bottom: 24px;
        }

        .pvc-section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
        }
        .pvc-section-title {
            font-size: 16px;
            font-weight: 700;
            color: #111111;
            margin: 0;
        }
        .pvc-badge-top {
            background-color: rgba(201, 161, 74, 0.15);
            color: #b8860b;
            font-size: 11px;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 12px;
            margin-left: 8px;
        }
        
        .pvc-suggestions-container {
            display: flex;
            gap: 10px;
            overflow-x: auto;
            padding-bottom: 8px;
            margin-bottom: 32px;
            scrollbar-width: none;
            -webkit-overflow-scrolling: touch;
        }
        .pvc-suggestions-container::-webkit-scrollbar {
            display: none;
        }
        .pvc-suggestion-chip {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            border: 1px solid #f0f0f0;
            border-radius: 8px;
            background: #ffffff;
            color: #111;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            white-space: nowrap;
            box-shadow: 0 2px 5px rgba(0,0,0,0.02);
        }
        .pvc-suggestion-chip i {
            color: #c9a14a;
        }
        
        .pvc-results-container {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .pvc-result-card {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 16px;
            border: 1px solid #f0f0f0;
            border-radius: 12px;
            background: #ffffff;
            text-decoration: none;
            box-shadow: 0 2px 8px rgba(0,0,0,0.03);
        }
        .pvc-result-img {
            width: 70px;
            height: 70px;
            object-fit: contain;
            border-radius: 8px;
        }
        .pvc-result-info {
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        .pvc-result-title {
            font-size: 15px;
            font-weight: 700;
            color: #111;
            margin-bottom: 4px;
            line-height: 1.3;
        }
        .pvc-result-meta {
            font-size: 12px;
            color: #666;
            margin-bottom: 6px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .pvc-result-meta i {
            color: #999;
        }
        .pvc-result-price {
            font-size: 15px;
            font-weight: 700;
            color: #c9a14a;
        }
        .pvc-result-arrow {
            color: #999;
            font-size: 14px;
        }

        .pvc-state-box {
            text-align: center;
            padding: 40px 20px;
            color: #666;
            font-size: 14px;
        }
        .pvc-loader {
            border: 3px solid #f3f3f3;
            border-top: 3px solid #c9a14a;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            animation: spin 1s linear infinite;
            margin: 0 auto 16px auto;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .hidden {
            display: none !important;
        }
    </style>
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

    <script>
        $(document).ready(function() {
            const $input = $('#pvcSearchInput');
            const $clearBtn = $('#pvcSearchClear');
            
            const $emptyState = $('#pvcSearchEmptyState');
            const $loading = $('#pvcSearchLoading');
            const $error = $('#pvcSearchError');
            const $noResults = $('#pvcSearchNoResults');
            const $content = $('#pvcSearchContent');
            
            const $suggestionsSection = $('#pvcSuggestionsSection');
            const $suggestionsContainer = $('#pvcSuggestionsContainer');
            
            const $resultsSection = $('#pvcResultsSection');
            const $resultsContainer = $('#pvcResultsContainer');
            const $resultsCount = $('#pvcResultsCount');

            let debounceTimer = null;
            let currentController = null;

            function showState(state) {
                $emptyState.addClass('hidden');
                $loading.addClass('hidden');
                $error.addClass('hidden');
                $noResults.addClass('hidden');
                $content.addClass('hidden');

                if (state === 'empty') $emptyState.removeClass('hidden');
                else if (state === 'loading') $loading.removeClass('hidden');
                else if (state === 'error') $error.removeClass('hidden');
                else if (state === 'no_results') $noResults.removeClass('hidden');
                else if (state === 'content') $content.removeClass('hidden');
            }

            $input.on('input', function() {
                const query = $(this).val().trim();
                
                if (query.length > 0) {
                    $clearBtn.show();
                } else {
                    $clearBtn.hide();
                    showState('empty');
                    if (currentController) currentController.abort();
                    return;
                }

                clearTimeout(debounceTimer);
                showState('loading');

                debounceTimer = setTimeout(() => {
                    performSearch(query);
                }, 300);
            });

            $clearBtn.on('click', function() {
                $input.val('').focus();
                $clearBtn.hide();
                showState('empty');
                if (currentController) currentController.abort();
            });

            function performSearch(query) {
                if (currentController) currentController.abort();
                currentController = new AbortController();

                fetch('search-suggest.php?q=' + encodeURIComponent(query), {
                    signal: currentController.signal
                })
                .then(res => {
                    if (!res.ok) throw new Error('HTTP ' + res.status);
                    return res.json();
                })
                .then(items => {
                    renderResults(items);
                })
                .catch(err => {
                    if (err.name !== 'AbortError') {
                        console.error('Search error:', err);
                        showState('error');
                    }
                });
            }

            function getIconForType(type) {
                if (type === 'brand') return '<i class="fa-solid fa-tags"></i>';
                if (type === 'category') return '<i class="fa-solid fa-border-all"></i>';
                return '<i class="fa-solid fa-box"></i>';
            }

            function renderResults(items) {
                if (!items || items.length === 0) {
                    showState('no_results');
                    return;
                }

                showState('content');

                const suggestions = items.filter(item => item.type === 'brand' || item.type === 'category');
                const products = items.filter(item => item.type === 'product');

                if (suggestions.length > 0) {
                    $suggestionsSection.removeClass('hidden');
                    $suggestionsContainer.empty();
                    
                    suggestions.forEach(item => {
                        const icon = getIconForType(item.type);
                        const chip = `
                            <a href="${item.url}" class="pvc-suggestion-chip">
                                ${icon} <span>${item.label}</span>
                            </a>
                        `;
                        $suggestionsContainer.append(chip);
                    });
                } else {
                    $suggestionsSection.addClass('hidden');
                }

                if (products.length > 0) {
                    $resultsSection.removeClass('hidden');
                    $resultsCount.text(products.length + (products.length === 1 ? ' Result' : ' Results'));
                    $resultsContainer.empty();

                    products.forEach(item => {
                        const img = item.pimage ? item.pimage : 'assets/img/logo/logo1.png';
                        const priceHtml = item.price ? '<div class="pvc-result-price">' + item.price + '</div>' : '';
                        
                        const card = `
                            <a href="${item.url}" class="pvc-result-card">
                                <img src="${img}" alt="${item.label}" class="pvc-result-img" onerror="this.onerror=null;this.src='assets/img/logo/logo1.png';">
                                <div class="pvc-result-info">
                                    <div class="pvc-result-title">${item.label}</div>
                                    <div class="pvc-result-meta">
                                        <i class="fa-solid fa-shield-halved"></i> ${item.sublabel}
                                    </div>
                                    ${priceHtml}
                                </div>
                                <i class="fa-solid fa-chevron-right pvc-result-arrow"></i>
                            </a>
                        `;
                        $resultsContainer.append(card);
                    });
                } else {
                    $resultsSection.addClass('hidden');
                }
            }
        });
    </script>

    <script src="assets/js/plugins/bootstrap.min.js"></script>
    <script src="assets/js/global_footer.js"></script>
</body>
</html>
