(function () {
    if (window.__pvcSearchBound) {
        return;
    }
    window.__pvcSearchBound = true;

    function ready(fn) {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', fn);
        } else {
            fn();
        }
    }

    function safeInternalUrl(url) {
        const value = String(url || '');
        if (/^(all-products|all-categories)\.php(?:\?[\w.~!*'();:@&=+$,/?%#\[\]-]*)?$/i.test(value)) {
            return value;
        }
        return '#';
    }

    ready(function () {
        const overlay = document.getElementById('globalSearchOverlay');
        const input = document.getElementById('pvcSearchInput');
        if (!overlay || !input || typeof window.jQuery === 'undefined') {
            return;
        }

        const $ = window.jQuery;
        const $input = $(input);
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
        let lastQuery = '';

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

        function openOverlay() {
            overlay.hidden = false;
            overlay.classList.add('open');
            document.body.classList.add('pvc-search-open');
            window.setTimeout(function () {
                input.focus();
            }, 0);
        }

        function closeOverlay() {
            overlay.classList.remove('open');
            overlay.hidden = true;
            document.body.classList.remove('pvc-search-open');
            if (currentController) currentController.abort();
        }

        document.getElementById('bottom-nav-search')?.addEventListener('click', openOverlay);
        document.getElementById('desktop-search-btn')?.addEventListener('click', openOverlay);
        document.getElementById('pvcSearchBack')?.addEventListener('click', closeOverlay);

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && overlay.classList.contains('open')) {
                closeOverlay();
            }
        });

        $input.on('input', function () {
            const query = String($input.val() || '').trim();

            if (query.length === 0) {
                $clearBtn.hide();
                showState('empty');
                clearTimeout(debounceTimer);
                if (currentController) currentController.abort();
                lastQuery = '';
                return;
            }

            $clearBtn.show();
            clearTimeout(debounceTimer);
            showState('loading');

            const wait = query.length === 1 ? 0 : 140;
            debounceTimer = setTimeout(function () {
                performSearch(query);
            }, wait);
        });

        $clearBtn.on('click', function () {
            $input.val('').focus();
            $clearBtn.hide();
            showState('empty');
            lastQuery = '';
            if (currentController) currentController.abort();
        });

        function performSearch(query) {
            if (query === lastQuery) {
                return;
            }

            if (currentController) currentController.abort();
            currentController = new AbortController();
            const requestQuery = query;

            fetch('search-suggest.php?q=' + encodeURIComponent(query), {
                signal: currentController.signal,
                headers: { Accept: 'application/json' },
            })
                .then(function (res) {
                    if (!res.ok) throw new Error('HTTP ' + res.status);
                    return res.json();
                })
                .then(function (items) {
                    if (String($input.val() || '').trim() !== requestQuery) {
                        return;
                    }
                    lastQuery = requestQuery;
                    renderResults(Array.isArray(items) ? items : []);
                })
                .catch(function (err) {
                    if (err.name !== 'AbortError') {
                        showState('error');
                    }
                });
        }

        function getIconForType(type) {
            if (type === 'brand') return '<i class="fa-solid fa-tags" aria-hidden="true"></i>';
            if (type === 'category') return '<i class="fa-solid fa-border-all" aria-hidden="true"></i>';
            return '<i class="fa-solid fa-box" aria-hidden="true"></i>';
        }

        function renderResults(items) {
            if (!items.length) {
                showState('no_results');
                return;
            }

            showState('content');

            const suggestions = items.filter(function (item) {
                return item.type === 'brand' || item.type === 'category';
            });
            const products = items.filter(function (item) {
                return item.type === 'product';
            });

            if (suggestions.length > 0) {
                $suggestionsSection.removeClass('hidden');
                $suggestionsContainer.empty();
                suggestions.forEach(function (item) {
                    const chip = $(
                        '<a class="pvc-suggestion-chip"></a>'
                    );
                    chip.attr('href', safeInternalUrl(item.url));
                    chip.html(getIconForType(item.type) + ' <span></span>');
                    chip.find('span').text(item.label || '');
                    $suggestionsContainer.append(chip);
                });
            } else {
                $suggestionsSection.addClass('hidden');
            }

            if (products.length > 0) {
                $resultsSection.removeClass('hidden');
                $resultsCount.text(products.length + (products.length === 1 ? ' Result' : ' Results'));
                $resultsContainer.empty();

                products.forEach(function (item) {
                    const img = item.pimage ? String(item.pimage) : 'assets/img/logo/logo1.png';
                    const card = $(
                        '<a class="pvc-result-card">' +
                            '<img alt="" class="pvc-result-img">' +
                            '<div class="pvc-result-info">' +
                                '<div class="pvc-result-title"></div>' +
                                '<div class="pvc-result-meta">' +
                                    '<i class="fa-solid fa-shield-halved" aria-hidden="true"></i> ' +
                                    '<span class="pvc-result-sub"></span>' +
                                '</div>' +
                            '</div>' +
                            '<i class="fa-solid fa-chevron-right pvc-result-arrow" aria-hidden="true"></i>' +
                        '</a>'
                    );
                    card.attr('href', safeInternalUrl(item.url));
                    card.find('.pvc-result-title').text(item.label || '');
                    card.find('.pvc-result-sub').text(item.sublabel || '');
                    card.find('img')
                        .attr('src', img)
                        .attr('alt', item.label || '')
                        .on('error', function () {
                            this.onerror = null;
                            this.src = 'assets/img/logo/logo1.png';
                        });
                    $resultsContainer.append(card);
                });
            } else {
                $resultsSection.addClass('hidden');
            }
        }
    });
})();
