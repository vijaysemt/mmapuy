
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="login-page">

    <div class="login-wrapper">

        <?= view('partials/cm_profile') ?>

        <div class="login-right">

            <span class="login-top-label">
                <i class="bi bi-shield-lock-fill"></i>
                CREATE YOUR PASSWORD
            </span>

            <h1>पासवर्ड सेट करें</h1>

            <p class="login-subtitle">
                अपना नया पासवर्ड बनाएं और पोर्टल पर आगे बढ़ें।
            </p>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger login-alert">
                    <?= esc(session()->getFlashdata('error')) ?>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('errors')): ?>
                <div class="alert alert-danger login-alert">
                    <ul class="mb-0">
                        <?php foreach (
                            session()->getFlashdata('errors')
                            as $error
                        ): ?>
                            <li><?= esc($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form
                class="login-form"
                action="<?= site_url('auth/set-password') ?>"
                method="post"
                id="setPasswordForm"
            >

                <?= csrf_field() ?>

                <div class="form-group">

                    <label for="password">
                        <i class="bi bi-lock me-1"></i>
                        नया पासवर्ड
                    </label>

                    <input
                        type="password"
                        class="form-control"
                        name="password"
                        id="password"
                        minlength="6"
                        maxlength="20"
                        placeholder="नया पासवर्ड दर्ज करें"
                        autocomplete="new-password"
                        required
                    >

                    <small class="text-muted">
                        कम से कम 6 अक्षर का पासवर्ड रखें।
                    </small>

                </div>

                <div class="form-group">

                    <label for="confirm_password">
                        <i class="bi bi-shield-lock me-1"></i>
                        पासवर्ड की पुष्टि करें
                    </label>

                    <input
                        type="password"
                        class="form-control"
                        name="confirm_password"
                        id="confirm_password"
                        minlength="6"
                        maxlength="20"
                        placeholder="पासवर्ड दोबारा दर्ज करें"
                        autocomplete="new-password"
                        required
                    >

                </div>

                <div class="login-buttons">

                    <button
                        type="submit"
                        class="login-btn login-btn-primary"
                    >
                        <i class="bi bi-check-circle"></i>
                        पासवर्ड सेव करें
                    </button>

                </div>

            </form>

            <div class="login-footer">
                <i class="bi bi-lock-fill me-1"></i>
                सुरक्षित पासवर्ड प्रबंधन
            </div>

        </div>

    </div>

</div>

<?= $this->endSection() ?>
