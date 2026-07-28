/**
 * admin/js/admin-crud.js
 * Consolidated controller handling Add, Edit, Delete, Search, Filters, Pagination, Rows Per Page via AJAX
 */

// ────────────────────────────────────────────────────────
// cleanupModals()
// Global safety-net cleanup called by AdminCrud.closeModalSafely() once a
// modal's 'hidden.bs.modal' event confirms it has actually finished closing
// (and also as the fallback when closeModalSafely() is invoked with no
// modal element at all).
//
// THIS FUNCTION WAS PREVIOUSLY MISSING FROM THIS FILE. closeModalSafely()
// called cleanupModals() in two places without it ever being defined
// anywhere, which threw a ReferenceError on every single modal close (every
// save, every delete). Because that throw happened INSIDE the
// 'hidden.bs.modal' once-listener, the line right after it —
// `bootstrap.Modal.getInstance(modalEl)?.dispose()` — never executed. The
// Modal instance was therefore never disposed, so the next
// getOrCreateInstance() call kept handing back the same instance, whose
// internal state could drift out of sync with the DOM — producing exactly
// the "dimmed backdrop, no visible dialog, only a refresh fixes it" bug
// this file's other comments describe. Defining cleanupModals() here is
// what actually lets that dispose-after-hidden fix run.
//
// Only removes backdrop(s)/resets body state when no modal is genuinely
// still open, so it never fights a modal that IS legitimately showing.
// ────────────────────────────────────────────────────────
function cleanupModals() {
    if (document.querySelector('.modal.show')) return;

    document.querySelectorAll('.modal-backdrop').forEach((el) => el.remove());
    document.body.classList.remove('modal-open');
    document.body.style.removeProperty('padding-right');
    document.body.style.removeProperty('overflow');
}

class AdminCrud {
    constructor(options) {
        this.options = Object.assign({
            endpoint: '',
            tableSelector: '',
            rowSelector: '',
            formSelector: '',
            modalSelector: '',
            deleteModalSelector: '',
            deleteExecutionSelector: '',
            statsSelector: '',
            visibleCountSelector: '',
            searchInputSelector: '',
            statusFilterSelector: null,
            brandFilterSelector: null,
            categoryFilterSelector: null,
            activeFilterBadgeSelector: null,
            activeFilterTextSelector: null,
            perPageSelector: '',
            paginationSelector: '',
            emptyStateColspan: 5,
            emptyStateText: 'No records found.',
            beforeSubmit: null,
            onError: null,
            matchRow: function() { return true; },
            onBrandFilterChange: null,
            onAddNewClick: null,
            onEditClick: null
        }, options);

        this.allRows = [];
        this.filteredRows = [];
        this.currentPage = 1;
        this.perPage = 5;

        this.init();
    }

    getStorageKey() {
        return 'adminCrudState:' + this.options.endpoint;
    }

    readSavedState() {
        try {
            const raw = sessionStorage.getItem(this.getStorageKey());
            return raw ? JSON.parse(raw) : null;
        } catch (e) {
            return null;
        }
    }

    saveState() {
        try {
            const state = {
                search: this.$searchInput.length ? this.$searchInput.val() : '',
                status: this.$statusFilter && this.$statusFilter.length ? this.$statusFilter.val() : '',
                brand: this.$brandFilter && this.$brandFilter.length ? this.$brandFilter.val() : '',
                category: this.$categoryFilter && this.$categoryFilter.length ? this.$categoryFilter.val() : '',
                perPage: this.perPage,
                currentPage: this.currentPage
            };
            sessionStorage.setItem(this.getStorageKey(), JSON.stringify(state));
        } catch (e) {}
    }

    restoreState() {
        const saved = this.readSavedState();
        if (!saved) {
            this.perPage = this.$perPageSelect.length ? (parseInt(this.$perPageSelect.val(), 10) || this.perPage) : this.perPage;
            return;
        }

        if (this.$searchInput.length) {
            this.$searchInput.val(saved.search || '');
        }

        if (this.$statusFilter && this.$statusFilter.length) {
            this.$statusFilter.val(saved.status || '');
        }

        if (this.$brandFilter && this.$brandFilter.length) {
            const savedBrand = saved.brand || '';
            this.$brandFilter.val(savedBrand);

            if (savedBrand && this.options.onBrandFilterChange) {
                this.options.onBrandFilterChange(savedBrand, this.allRows);
            }
        }

        if (this.$categoryFilter && this.$categoryFilter.length) {
            this.$categoryFilter.val(saved.category || '');
        }

        const savedPerPage = parseInt(saved.perPage, 10);
        if (Number.isFinite(savedPerPage) && savedPerPage > 0) {
            this.perPage = savedPerPage;
        } else if (this.$perPageSelect.length) {
            this.perPage = parseInt(this.$perPageSelect.val(), 10) || this.perPage;
        }

        if (this.$perPageSelect.length) {
            this.$perPageSelect.val(String(this.perPage));
        }

        const savedPage = parseInt(saved.currentPage, 10);
        this.currentPage = Number.isFinite(savedPage) && savedPage > 0 ? savedPage : 1;
    }

    init() {
        const opt = this.options;

        // Cache elements
        this.$tableBody = $(opt.tableSelector);
        this.$form = $(opt.formSelector);
        this.$modal = $(opt.modalSelector);
        this.$deleteModal = $(opt.deleteModalSelector);
        this.$deleteExecutionLink = $(opt.deleteExecutionSelector);
        this.$searchInput = $(opt.searchInputSelector);
        this.$statusFilter = opt.statusFilterSelector ? $(opt.statusFilterSelector) : null;
        this.$brandFilter = opt.brandFilterSelector ? $(opt.brandFilterSelector) : null;
        this.$categoryFilter = opt.categoryFilterSelector ? $(opt.categoryFilterSelector) : null;
        this.$perPageSelect = $(opt.perPageSelector);
        this.$paginationButtons = $(opt.paginationSelector);
        this.$visibleCount = $(opt.visibleCountSelector);

        console.log("AJAX HANDLER ATTACHED for endpoint:", opt.endpoint);

        // ────────────────────────────────────────────────────────
        // FOCUS GUARD (root-cause fix, v2)
        //
        // Bootstrap's Modal.hide() runs, in order:
        //   1. fires 'hide.bs.modal'                (SYNC — our listener runs here)
        //   2. this._focustrap.deactivate()          (SYNC, right after step 1)
        //   3. waits for the CSS fade transition to finish
        //   4. sets aria-hidden="true" on the modal  (only after step 3)
        //
        // Bootstrap's internal FocusTrap is still ACTIVE during step 1.
        // FocusTrap has its own document-level 'focusin' listener that
        // forcibly snaps focus back inside the modal the instant it
        // detects focus leaving — including a blur() we trigger ourselves
        // during 'hide.bs.modal', since blur() moves focus to <body>,
        // which fires a 'focusin' the trap intercepts. That's why a plain
        // blur-on-hide.bs.modal (or a blur BEFORE calling hide() at all,
        // since the trap is still active then too) doesn't actually work:
        // focus gets yanked straight back onto e.g. .btn-close before the
        // trap is deactivated, and by the time aria-hidden is finally set
        // in step 4, focus is back inside the modal again.
        //
        // Fix: defer the blur with setTimeout(0). That pushes it past
        // step 2 (_focustrap.deactivate(), which runs synchronously right
        // after the event finishes dispatching), so by the time we blur,
        // the trap is already disabled and nothing grabs focus back.
        // ────────────────────────────────────────────────────────
        const releaseFocusFromModal = ($m) => {
            if (!$m || !$m.length) return;
            const modalEl = $m[0];
            setTimeout(() => {
                const active = document.activeElement;
                if (active && modalEl.contains(active)) {
                    active.blur();
                }
            }, 0);
        };

        // GUARD: init() can run more than once for the SAME DOM nodes if
        // whatever loads this page (e.g. table-ajax.js) swaps content
        // without fully destroying the previous AdminCrud instance first.
        // addEventListener has no "already bound?" check built in, so
        // without this flag every re-init stacks another copy of these
        // listeners on the same element — e.g. two stacked listeners means
        // one click on #addNewBtn fires onAddNewClick() twice in a row,
        // synchronously, which is exactly the kind of double-invocation
        // that produces an intermittent "modal backdrop with no visible
        // dialog" glitch that gets worse the more you navigate around.
        [this.$modal, this.$deleteModal].forEach(($m) => {
            if ($m && $m.length && !$m[0]._adminCrudFocusGuardBound) {
                $m[0]._adminCrudFocusGuardBound = true;
                $m[0].addEventListener('hide.bs.modal', () => releaseFocusFromModal($m));
            }
        });
        this._releaseFocusFromModal = releaseFocusFromModal;

        // Load initial rows
        this.readRows();

        // Restore the last saved state for this endpoint before the first filter pass.
        this.restoreState();

        // Listeners for filters
        if (this.$searchInput.length) {
            this.$searchInput.on('input', () => this.applyFilters());
        }
        if (this.$statusFilter && this.$statusFilter.length) {
            this.$statusFilter.on('change', () => this.applyFilters());
        }
        if (this.$brandFilter && this.$brandFilter.length) {
            this.$brandFilter.on('change', (e) => {
                const brandid = e.target.value;
                if (opt.onBrandFilterChange) {
                    opt.onBrandFilterChange(brandid, this.allRows);
                }
                this.applyFilters();
            });
        }
        if (this.$categoryFilter && this.$categoryFilter.length) {
            this.$categoryFilter.on('change', () => this.applyFilters());
        }

        // Active filter badge clear click if any
        if (opt.activeFilterBadgeSelector) {
            $(opt.activeFilterBadgeSelector).on('click', () => {
                this.$searchInput.val('');
                if (this.$brandFilter) this.$brandFilter.val('');
                if (this.$categoryFilter) {
                    this.$categoryFilter.html('<option value="">Select brand first</option>').prop('disabled', true);
                }
                this.applyFilters();
            });
        }

        // Rows per page
        if (this.$perPageSelect.length) {
            this.$perPageSelect.on('change', (e) => {
                this.perPage = parseInt(e.target.value, 10);
                this.currentPage = 1;
                this.renderPage();
            });
        }

        // Form submission inside modal
        if (this.$form.length) {
            this.$form.on('submit', (e) => {
                console.log("Form submit event intercepted! Running preventDefault().");
                e.preventDefault();
                console.log("e.preventDefault() executed! isDefaultPrevented =", e.isDefaultPrevented());
                if (opt.beforeSubmit) {
                    opt.beforeSubmit();
                }
                this.submitForm();
            });
        }

        // Delete execution confirmation
        if (this.$deleteExecutionLink.length) {
            this.$deleteExecutionLink.on('click', (e) => {
                console.log("Delete execution link clicked! Running preventDefault().");
                e.preventDefault();
                console.log("e.preventDefault() executed! isDefaultPrevented =", e.isDefaultPrevented());
                const href = this.$deleteExecutionLink.attr('href');
                this.deleteRecord(href);
            });
        }

        // Delegated edit/delete — namespaced so re-init across SPA nav doesn't stack handlers
        $(document).off('click.adminCrud');

        $(document).on('click.adminCrud', '.edit-btn', (e) => {
            const btn = e.currentTarget;
            // Blur the triggering button itself before opening — otherwise
            // if a previous modal's close is still settling, this click's
            // own focus can collide with that modal's aria-hidden change.
            btn.blur();
            if (opt.onEditClick) {
                opt.onEditClick(btn);
            }
        });

        $(document).on('click.adminCrud', '.delete-confirm-trigger', (e) => {
            e.preventDefault();
            const btn = e.currentTarget;
            btn.blur();
            const id = btn.dataset.id;
            if (id && this.$deleteExecutionLink.length) {
                const params = new URLSearchParams(window.location.search);
                params.delete('success_msg');
                params.delete('products_removed');
                params.set('action', 'delete');
                params.set('id', id);
                this.$deleteExecutionLink.attr('href', '?' + params.toString());
            }
            if (this.$deleteModal.length) {
                const deleteInst = bootstrap.Modal.getOrCreateInstance(this.$deleteModal[0]);
                deleteInst.show();
            }
        });

        // Add new button click trigger
        // See the guard note above closeModalSafely's focus-guard binding —
        // same reasoning applies here: without this flag, a re-init on the
        // same #addNewBtn node (no full page reload) stacks another native
        // click listener, so a single click fires onAddNewClick() twice.
        const addNewBtn = document.getElementById('addNewBtn');
        if (addNewBtn && opt.onAddNewClick && !addNewBtn._adminCrudBound) {
            addNewBtn._adminCrudBound = true;
            addNewBtn.addEventListener('click', () => {
                addNewBtn.blur();
                opt.onAddNewClick();
            });
        }

        // Initial apply & render
        this.applyFilters({ resetPage: false });
    }

    readRows() {
        this.allRows = Array.from(document.querySelectorAll(this.options.tableSelector + ' ' + this.options.rowSelector));
    }

    applyFilters(options = { resetPage: true }) {
        const opt = this.options;
        const q = this.$searchInput.length ? this.$searchInput.val().trim().toLowerCase() : '';
        const status = this.$statusFilter ? this.$statusFilter.val() : '';
        const brand = this.$brandFilter ? this.$brandFilter.val() : '';
        const category = this.$categoryFilter ? this.$categoryFilter.val() : '';

        this.filteredRows = this.allRows.filter((row) => {
            return opt.matchRow(row, q, status, brand, category);
        });

        // Update badge text if badge elements are present
        if (opt.activeFilterBadgeSelector && opt.activeFilterTextSelector) {
            const parts = [];
            if (this.$brandFilter && brand) {
                parts.push(this.$brandFilter.find('option:selected').text());
            }
            if (this.$categoryFilter && category) {
                parts.push(this.$categoryFilter.find('option:selected').text());
            }
            if (q) {
                parts.push('"' + q + '"');
            }

            const $badge = $(opt.activeFilterBadgeSelector);
            const $text = $(opt.activeFilterTextSelector);
            if (parts.length > 0) {
                $text.text(parts.join(' · '));
                $badge.show();
            } else {
                $badge.hide();
            }
        }

        if (options.resetPage) {
            this.currentPage = 1;
        }
        this.renderPage();
    }

    renderPage() {
        const total = this.filteredRows.length;
        const totalPages = Math.max(1, Math.ceil(total / this.perPage));
        if (this.currentPage > totalPages) this.currentPage = totalPages;

        const start = (this.currentPage - 1) * this.perPage;
        const end = Math.min(start + this.perPage, total);

        // Hide all rows, show only page slice
        this.allRows.forEach(r => r.style.display = 'none');
        this.filteredRows.forEach((r, i) => {
            r.style.display = (i >= start && i < end) ? '' : 'none';
        });

        // Empty state row handling
        let emptyRow = this.$tableBody.find('.js-empty-row');
        if (total === 0) {
            if (emptyRow.length === 0) {
                const tr = document.createElement('tr');
                tr.className = 'js-empty-row empty-state-row';
                tr.innerHTML = `<td colspan="${this.options.emptyStateColspan}">
                    <div class="empty-state-icon"><i class="fa-solid fa-magnifying-glass"></i></div>
                    <p class="text-muted mb-0">${this.options.emptyStateText}</p>
                </td>`;
                this.$tableBody.append(tr);
            } else {
                emptyRow.show();
            }
        } else if (emptyRow.length) {
            emptyRow.hide();
        }

        if (this.$visibleCount.length) {
            this.$visibleCount.text(total);
        }

        this.renderPaginationButtons(this.currentPage, totalPages);
        this.saveState();
    }

    renderPaginationButtons(page, totalPages) {
        this.$paginationButtons.html('');

        const makeBtn = (label, targetPage, disabled, active) => {
            const btn = document.createElement('button');
            btn.className = 'page-btn' + (active ? ' active' : '');
            btn.innerHTML = label;
            btn.disabled = disabled;
            if (!disabled) {
                btn.addEventListener('click', () => {
                    this.currentPage = targetPage;
                    this.renderPage();
                });
            }
            return btn;
        };

        this.$paginationButtons.append(makeBtn('<i class="fa fa-angle-left"></i>', page - 1, page === 1, false));

        let lo = Math.max(1, page - 2);
        let hi = Math.min(totalPages, lo + 4);
        lo = Math.max(1, hi - 4);

        if (lo > 1) {
            this.$paginationButtons.append(makeBtn('1', 1, false, false));
            if (lo > 2) {
                const dots = document.createElement('span');
                dots.className = 'page-btn'; dots.style.cursor = 'default'; dots.textContent = '…';
                this.$paginationButtons.append(dots);
            }
        }
        for (let i = lo; i <= hi; i++) {
            this.$paginationButtons.append(makeBtn(i, i, false, i === page));
        }
        if (hi < totalPages) {
            if (hi < totalPages - 1) {
                const dots = document.createElement('span');
                dots.className = 'page-btn'; dots.style.cursor = 'default'; dots.textContent = '…';
                this.$paginationButtons.append(dots);
            }
            this.$paginationButtons.append(makeBtn(totalPages, totalPages, false, false));
        }

        this.$paginationButtons.append(makeBtn('<i class="fa fa-angle-right"></i>', page + 1, page === totalPages, false));
    }

    showTableSpinner() {
        if ($('#table-spinner').length === 0) {
            const spinnerHtml = `
                <div id="table-spinner" style="
                    position: absolute;
                    top: 0; left: 0; right: 0; bottom: 0;
                    background: rgba(255, 255, 255, 0.7);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    z-index: 999;
                    opacity: 0;
                    transition: opacity 0.2s ease;
                ">
                    <div class="spinner-border text-primary" role="status" style="width: 2.5rem; height: 2.5rem;">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            `;
            $('.table-responsive').css('position', 'relative').append(spinnerHtml);
            setTimeout(() => $('#table-spinner').css('opacity', '1'), 10);
        }
    }

    hideTableSpinner() {
        const spinner = $('#table-spinner');
        if (spinner.length) {
            spinner.css('opacity', '0');
            setTimeout(() => spinner.remove(), 200);
        }
    }

    reloadTableAndStats(successMessage) {
        $.ajax({
            url: this.options.endpoint,
            type: 'GET',
            success: (html) => {
                const $newDom = $(html);
                
                // Update table body HTML content
                this.$tableBody.html($newDom.find(this.options.tableSelector).html());
                
                // Update stats cards counts
                if (this.options.statsSelector) {
                    const $container = $(this.options.statsSelector).parent();
                    $container.html($newDom.find(this.options.statsSelector).parent().html());
                }
                
                // Read the updated row elements
                this.readRows();
                
                // Maintain active filters
                this.applyFilters({ resetPage: false });
                
                if (successMessage) {
                    showToast(successMessage, 'success');
                }
            },
            error: () => {
                showToast('Failed to refresh table.', 'danger');
            },
            complete: () => {
                this.hideTableSpinner();
            }
        });
    }

    // ────────────────────────────────────────────────────────
    // closeModalSafely()
    // hide() is an ASYNC fade transition. We now:
    //   1. Proactively release focus from inside the modal BEFORE calling
    //      hide(), so Bootstrap never hits the aria-hidden/focus collision
    //      that corrupts its internal _isShown state.
    //   2. Wait for Bootstrap's own 'hidden.bs.modal' confirmation before
    //      running cleanupModals(), instead of racing it.
    //   3. Dispose the Bootstrap Modal instance after it's confirmed hidden,
    //      so any leftover/mismatched internal state (e.g. a backdrop
    //      reference to a node that was manually removed) can't leak into
    //      the next .show() call. The caller is expected to fetch a fresh
    //      instance via bootstrap.Modal.getOrCreateInstance() next time
    //      rather than holding a long-lived cached reference.
    // ────────────────────────────────────────────────────────
    closeModalSafely($modalEl) {
        if (!$modalEl || !$modalEl.length) {
            cleanupModals();
            return;
        }
        const modalEl = $modalEl[0];

        // Release focus BEFORE calling hide() — this is the actual fix for
        // the "page dims, no dialog, only refresh fixes it" bug. Doing this
        // reactively inside a 'hide.bs.modal' listener is too late in some
        // browsers/timings; doing it here, synchronously, before hide() is
        // invoked, prevents the aria-hidden collision from ever happening.
        if (this._releaseFocusFromModal) {
            this._releaseFocusFromModal($modalEl);
        } else {
            const active = document.activeElement;
            if (active && modalEl.contains(active)) active.blur();
        }

        const modalInst = bootstrap.Modal.getOrCreateInstance(modalEl);

        modalEl.addEventListener('hidden.bs.modal', () => {
            cleanupModals();
            // Dispose so the instance's internal state can't outlive/desync
            // from the DOM. Next open must call getOrCreateInstance() again.
            bootstrap.Modal.getInstance(modalEl)?.dispose();
        }, { once: true });

        modalInst.hide();
    }

    submitForm() {
        this.showTableSpinner();

        const formData = new FormData(this.$form[0]);
        formData.append('ajax', '1');

        $.ajax({
            url: this.options.endpoint,
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: (response) => {
                let res = { status: 'success' };
                try {
                    res = JSON.parse(response);
                } catch (e) {}

                if (res.status === 'error') {
                    showToast(res.msg || 'An error occurred.', 'danger');
                    if (this.options.onError) {
                        this.options.onError();
                    }
                    this.hideTableSpinner();
                    return;
                }

                const isEdit = document.getElementById('modal_edit_mode') && document.getElementById('modal_edit_mode').value === '1';

                // Hide Bootstrap modal safely, THEN reload once it's actually closed
                this.closeModalSafely(this.$modal);
                this.reloadTableAndStats(res.msg || (isEdit ? 'Updated successfully.' : 'Created successfully.'));
            },
            error: () => {
                showToast('Failed to submit form.', 'danger');
                if (this.options.onError) {
                    this.options.onError();
                }
                this.hideTableSpinner();
            }
        });
    }

    deleteRecord(href) {
        this.closeModalSafely(this.$deleteModal);

        this.showTableSpinner();

        $.ajax({
            url: appendAjaxParam(href),
            type: 'GET',
            success: (response) => {
                let res = { status: 'success' };
                try {
                    res = JSON.parse(response);
                } catch (e) {}

                if (res.status === 'error') {
                    showToast(res.msg || 'Failed to delete record.', 'danger');
                    this.hideTableSpinner();
                    return;
                }

                this.reloadTableAndStats(res.msg || 'Deleted successfully.');
            },
            error: () => {
                showToast('Failed to execute deletion.', 'danger');
                this.hideTableSpinner();
            }
        });
    }
}