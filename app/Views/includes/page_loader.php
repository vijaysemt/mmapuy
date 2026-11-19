<?php
/**
 * Full-page loader. File: app/Views/includes/page_loader.php
 *
 * Include ONCE in your main layout (layouts/dashboard.php), after common/scripts:
 *
 *     <?= $this->include('includes/page_loader') ?>
 *
 * It shows automatically when the user:
 *   - clicks a normal link to another page (dashboard cards, report links, Back, ...)
 *   - submits a form (filter "Apply", "Refresh" is a link so it is covered too)
 *   - changes a filter that calls form.submit()
 * and hides again when the new page loads (or when the user comes back with the Back button).
 *
 * Skipped automatically: #anchors, new-tab links, downloads, Bootstrap tabs/dropdowns,
 * external sites. Add  data-no-loader  to any link or form to skip it.
 *
 * From your own JavaScript:   PageLoader.show('Preparing file...');   PageLoader.hide();
 */
?>
<style>
    #pageLoader {
        position: fixed;
        inset: 0;
        z-index: 2000;
        display: none;
        align-items: center;
        justify-content: center;
        background: rgba(15, 23, 42, .45);
        backdrop-filter: blur(2px);
        opacity: 0;
        transition: opacity .15s ease;
    }

    #pageLoader.is-visible {
        display: flex;
        opacity: 1;
    }

    #pageLoader .page-loader-box {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 14px;
        min-width: 220px;
        padding: 26px 34px;
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 18px 40px rgba(15, 23, 42, .3);
        color: #1e293b;
        font-size: 14px;
        font-weight: 600;
        text-align: center;
    }

    #pageLoader .page-loader-spinner {
        width: 46px;
        height: 46px;
        border: 5px solid #dbe5f3;
        border-top-color: #2d4667;
        border-radius: 50%;
        animation: pageLoaderSpin .8s linear infinite;
    }

    #pageLoader .page-loader-sub {
        font-size: 12px;
        font-weight: 400;
        color: #64748b;
    }

    @keyframes pageLoaderSpin {
        to { transform: rotate(360deg); }
    }

    @media (prefers-reduced-motion: reduce) {
        #pageLoader .page-loader-spinner { animation-duration: 2s; }
    }
</style>

<div id="pageLoader" role="status" aria-live="polite" aria-hidden="true">
    <div class="page-loader-box">
        <div class="page-loader-spinner"></div>
        <div class="page-loader-text">Loading, please wait&hellip;</div>
        <div class="page-loader-sub">Large reports can take a few seconds</div>
    </div>
</div>

<script>
    (function () {
        const loader  = document.getElementById('pageLoader');
        const textEl  = loader.querySelector('.page-loader-text');
        const DEFAULT = 'Loading, please wait\u2026';
        let safety = null;

        function show(message) {
            textEl.textContent = message || DEFAULT;
            loader.classList.add('is-visible');
            loader.setAttribute('aria-hidden', 'false');

            // Safety net: never leave the screen blocked (e.g. a link that turned out to be a download).
            clearTimeout(safety);
            safety = setTimeout(hide, 60000);
        }

        function hide() {
            clearTimeout(safety);
            loader.classList.remove('is-visible');
            loader.setAttribute('aria-hidden', 'true');
        }

        window.PageLoader = { show: show, hide: hide };

        // ---- links -------------------------------------------------------
        document.addEventListener('click', function (e) {
            if (e.defaultPrevented || e.button !== 0 || e.ctrlKey || e.metaKey || e.shiftKey || e.altKey) {
                return;
            }

            const a = e.target.closest('a[href]');
            if (!a) return;

            if (a.hasAttribute('data-no-loader') || a.hasAttribute('download') || a.hasAttribute('data-bs-toggle')) {
                return;
            }

            if (a.target && a.target !== '_self') return;

            const href = a.getAttribute('href');
            if (!href || href.charAt(0) === '#' || /^(javascript|mailto|tel):/i.test(href)) return;

            let url;
            try { url = new URL(a.href, window.location.href); } catch (err) { return; }

            if (url.origin !== window.location.origin) return;

            // Same page, only the #hash changes
            if (url.hash && url.pathname === window.location.pathname && url.search === window.location.search) {
                return;
            }

            show();
        });

        // ---- forms (normal submit button / Enter key) --------------------
        document.addEventListener('submit', function (e) {
            const form = e.target;

            if (e.defaultPrevented || form.hasAttribute('data-no-loader')) return;
            if (form.target && form.target !== '_self') return;

            show();
        });

        // ---- forms submitted from JavaScript: this.form.submit() ---------
        const nativeSubmit = HTMLFormElement.prototype.submit;

        HTMLFormElement.prototype.submit = function () {
            if (!this.hasAttribute('data-no-loader')) show();
            nativeSubmit.call(this);
        };

        // ---- hide again ---------------------------------------------------
        // Back / forward button can restore the old page from cache with the loader still visible.
        window.addEventListener('pageshow', function (e) {
            if (e.persisted) hide();
        });

        window.addEventListener('load', hide);
    })();
</script>