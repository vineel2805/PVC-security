
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
    