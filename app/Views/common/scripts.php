
<!-- SCHEME DETAIL MODAL -->
<div class="modal fade" id="scheme-detail-popup" tabindex="-1">

    <div class="modal-dialog modal-xl modal-dialog-scrollable">

        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">योजना का विवरण</h5>

                <button type="button" class="btn-close"
                        data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body" id="scheme-modal-content">
                जानकारी लोड हो रही है...
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary"
                        data-bs-dismiss="modal">
                    बंद करें
                </button>
            </div>

        </div>
    </div>
</div>

<!-- SCRIPTS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/dompurify/3.3.1/purify.min.js"></script>

<script src="https://code.jquery.com/jquery-4.0.0.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

<script>
    const BASE_URL = <?= json_encode(rtrim(base_url(), '/') . '/') ?>;
</script>

<script src="<?= base_url('assets/mmapua/js/home.js') ?>"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    document.querySelectorAll('.login-form').forEach(function(form) {

        form.addEventListener('submit', function(event) {

            if (!form.checkValidity()) {
                return;
            }

            const button = form.querySelector(
                'button[type="submit"]'
            );

            if (!button) return;

            if (button.dataset.submitting === 'true') {
                event.preventDefault();
                return;
            }

            button.dataset.submitting = 'true';
            button.disabled = true;

            const text = button.querySelector('.btn-text');
            const loader = button.querySelector('.btn-loader');

            if (text) text.classList.add('d-none');

            if (loader) loader.classList.remove('d-none');

        });

    });

});
</script>

</body>
</html>
