// admin/js/table-ajax.js
$(document).ready(function() {
    // Inject Toast Container if not exists
    if ($('#toast-container').length === 0) {
        $('body').append('<div id="toast-container" style="position: fixed; top: 20px; right: 20px; z-index: 10000; display: flex; flex-direction: column; gap: 10px;"></div>');
    }

    // Shared Premium Toast system matching Black & Gold theme
    window.showToast = function(message, type = 'success') {
        const id = 'toast-' + Date.now();
        const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
        const borderStyle = type === 'success' ? '4px solid #D4AF37' : '4px solid #dc3545';
        const iconColor = type === 'success' ? '#D4AF37' : '#dc3545';

        const toastHtml = `
            <div id="${id}" style="
                background: #0B0B0B;
                color: #FFFFFF;
                border-left: ${borderStyle};
                padding: 15px 20px;
                border-radius: 8px;
                box-shadow: 0 4px 15px rgba(0,0,0,0.25);
                display: flex;
                align-items: center;
                gap: 12px;
                min-width: 280px;
                max-width: 400px;
                transform: translateX(120%);
                transition: all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
                font-family: inherit;
            ">
                <i class="fa ${icon}" style="color: ${iconColor}; font-size: 20px;"></i>
                <div style="flex: 1; font-size: 14px; font-weight: 500; line-height: 1.4;">${message}</div>
                <button onclick="$('#${id}').css('transform', 'translateX(120%)'); setTimeout(() => $('#${id}').remove(), 300);" style="background: none; border: none; color: #aaaaaa; cursor: pointer; font-size: 18px; padding: 0 0 0 10px; line-height: 1;">&times;</button>
            </div>
        `;

        $('#toast-container').append(toastHtml);

        // Slide in
        setTimeout(() => {
            $(`#${id}`).css('transform', 'translateX(0)');
        }, 50);

        // Auto remove
        setTimeout(() => {
            $(`#${id}`).css('transform', 'translateX(120%)');
            setTimeout(() => {
                $(`#${id}`).remove();
            }, 300);
        }, 4000);
    };

    // Parse URL parameter utility
    window.appendAjaxParam = function(url) {
        if (!url || typeof url !== 'string') {
            url = window.location.pathname || '';
        }
        try {
            const urlObj = new URL(url, window.location.href);
            urlObj.searchParams.set('ajax', '1');
            return urlObj.pathname + urlObj.search + urlObj.hash;
        } catch (e) {
            const separator = url.includes('?') ? '&' : '?';
            return url + separator + 'ajax=1';
        }
    };

    // Shared modal cleanup utility
    window.cleanupModals = function() {
        $('.modal-backdrop').remove();
        $('body').removeClass('modal-open').css({ overflow: '', 'padding-right': '' });
    };

    // ==============================================================
    // PARTIAL PAGE LOADER FOR SIDEBAR NAVIGATION
    // ==============================================================

    // Helper: append partial parameter
    function appendPartialParam(url) {
        const separator = url.includes('?') ? '&' : '?';
        return url + separator + 'partial=1';
    }

    // Helper: update active sidebar classes
    function updateActiveSidebar(url) {
        const filename = url.split('/').pop().split('?')[0];
        
        // Remove active classes
        $('#menu a').removeClass('mm-active');
        $('#menu li').removeClass('mm-active active');

        // Add active classes to matched link
        $('#menu a').each(function() {
            const href = $(this).attr('href');
            if (href && href.includes(filename)) {
                $(this).addClass('mm-active');
                $(this).parent('li').addClass('mm-active active');
            }
        });
    }

    // Helper: detach and later re-run scripts from fetched partial content
    function extractScripts($body) {
        const scripts = [];
        $body.find('script').each(function() {
            scripts.push({
                src: this.src || '',
                type: this.type || '',
                text: this.text || this.textContent || ''
            });
            $(this).remove();
        });
        return scripts;
    }

    function runExtractedScripts(scripts) {
        scripts.forEach(function(script) {
            if (script.src) {
                const el = document.createElement('script');
                if (script.type) el.type = script.type;
                el.src = script.src;
                document.body.appendChild(el);
            } else if (script.text) {
                $.globalEval(script.text);
            }
        });
    }

    // Global loadPage function
    window.loadPage = function(url, pushState = true) {
        const $contentBody = $('.content-body');

        // ────────────────────────────────────────────────────────
        // FORCE-CLEAR ANY IN-FLIGHT MODAL BACKDROP BEFORE NAVIGATING
        //
        // Bootstrap appends .modal-backdrop as a sibling of <body>'s other
        // children — it lives OUTSIDE .content-body, which is the only
        // thing this function swaps. If the user navigates away (clicks a
        // sidebar link) while a modal is still mid-close — its fade-out
        // transition still running, Bootstrap waiting on that element's
        // 'transitionend' before it removes its own backdrop — and we then
        // rip .content-body (which contains that modal element) out of the
        // DOM below, the transition gets aborted. 'transitionend' never
        // fires on a detached element, so Bootstrap's own backdrop-removal
        // logic never runs. The backdrop was never part of .content-body,
        // so the page swap can't clean it up either — it's orphaned on
        // <body> permanently, dimming every page after this one.
        //
        // We're about to destroy .content-body regardless, so there's no
        // need to wait for that modal's graceful close — just force-clear
        // any backdrop/body-state right now, before starting the fade.
        // ────────────────────────────────────────────────────────
        if (typeof cleanupModals === 'function') {
            cleanupModals();
        }

        // Render inline premium loading spinner overlay on top of existing content
        const loaderHtml = `
            <div id="content-body-loader" style="
                position: absolute;
                top: 0; left: 0; right: 0; bottom: 0;
                background: rgba(255, 255, 255, 0.4);
                backdrop-filter: blur(2px);
                display: flex;
                align-items: center;
                justify-content: center;
                z-index: 10000;
                opacity: 0;
                transition: opacity 0.15s ease;
            ">
                <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem; color: #D4AF37 !important;">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        `;
        $contentBody.css('position', 'relative').append(loaderHtml);
        setTimeout(() => $('#content-body-loader').css('opacity', '1'), 10);

        const ajaxUrl = appendPartialParam(url);

        $.ajax({
            url: ajaxUrl,
            type: 'GET',
            success: function(html) {
                console.log("CONTENT LOADED");
                
                // 1. Fade out current content-body
                $contentBody.css({
                    'transition': 'opacity 0.15s ease',
                    'opacity': '0'
                });

                // Wait 150ms for fade out transition to complete
                setTimeout(function() {
                    // Find and extract .content-body from response or fallback
                    let $newBody = null;
                    const $root = $(html);
                    if ($root.hasClass('content-body')) {
                        $newBody = $root;
                    } else {
                        const $temp = $('<div>').html(html);
                        $newBody = $temp.find('.content-body');
                    }

                    if ($newBody && $newBody.length > 0) {
                        // Detach scripts before DOM insert (they won't auto-run on swap)
                        const pageScripts = extractScripts($newBody);

                        // Keep new body invisible initially
                        $newBody.css({
                            'opacity': '0',
                            'transition': 'opacity 0.15s ease'
                        });
                        $('.content-body').replaceWith($newBody);

                        // Re-execute page init scripts against the live DOM
                        runExtractedScripts(pageScripts);
                    } else {
                        $('.content-body').html(html).css({
                            'opacity': '0',
                            'transition': 'opacity 0.15s ease'
                        });
                    }

                    // Trigger browser reflow
                    const $activeBody = $('.content-body');
                    if ($activeBody.length > 0) {
                        const reflow = $activeBody[0].offsetHeight;
                        // Fade in new content body
                        $activeBody.css('opacity', '1');
                    }

                    // Force-clear again after the swap completes — belt and
                    // suspenders alongside the pre-navigation clear above,
                    // in case anything managed to re-add a backdrop during
                    // the fetch/fade window (e.g. a modal shown just before
                    // the click was processed).
                    if (typeof cleanupModals === 'function') {
                        cleanupModals();
                    }

                    // Update title
                    let title = "PVC Admin Dashboard";
                    const filename = url.split('/').pop().split('?')[0];
                    if (filename === 'brands.php') title = "Brands - PVC Admin Dashboard";
                    else if (filename === 'categories.php') title = "Categories - PVC Admin Dashboard";
                    else if (filename === 'products.php') title = "Products - PVC Admin Dashboard";
                    else if (filename === 'dashboard.php') title = "Dashboard - PVC Admin Dashboard";
                    document.title = title;

                    // Update active sidebar
                    updateActiveSidebar(url);

                    // Push to history state
                    if (pushState) {
                        window.history.pushState({ path: url }, '', url);
                    }
                }, 150);
            },
            error: function() {
                showToast('Failed to load page content.', 'danger');
                $('#content-body-loader').remove();
            }
        });
    };

    // Listen to history changes (back/forward navigation)
    window.addEventListener('popstate', function(event) {
        if (event.state && event.state.path) {
            loadPage(event.state.path, false);
        } else {
            loadPage(window.location.href, false);
        }
    });

    // Initialize initial state inside history
    if (window.history && window.history.replaceState) {
        window.history.replaceState({ path: window.location.pathname + window.location.search }, '', window.location.href);
    }

    // Intercept sidebar link clicks
    $(document).on('click', '#menu a', function(e) {
        const href = $(this).attr('href');
        if (!href || href.startsWith('#') || href.startsWith('javascript:') || href.startsWith('http://') || href.startsWith('https://')) {
            return;
        }
        e.preventDefault();
        loadPage(href);
    });
});