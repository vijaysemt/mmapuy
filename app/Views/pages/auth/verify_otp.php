<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="login-page">

    <div class="login-wrapper">

        <!-- LEFT PANEL -->
       <?= view('partials/cm_profile') ?>


        <!-- RIGHT PANEL -->
        <div class="login-right">

            <span class="login-top-label">
                <i class="bi bi-shield-check"></i>
                OTP VERIFICATION
            </span>

            <h1>OTP सत्यापन</h1>

            <p class="login-subtitle">
                आपके फ़ोन नंबर पर भेजा गया OTP सत्यापित करें।
            </p>


            <?php if (session()->getFlashdata('error')): ?>

                <div class="alert alert-danger login-alert">
                    <?= esc(session()->getFlashdata('error')) ?>
                </div>

            <?php endif; ?>


        <form
            class="login-form"
            action="<?= site_url('auth/verify-otp') ?>"
            method="post"
        >   
    <?= csrf_field() ?>

    <div class="form-group">

        <label for="otp">
            <i class="bi bi-key me-1"></i>
            OTP दर्ज करें
        </label>

        <input
            type="text"
            class="form-control"
            name="otp"
            id="otp"
            pattern="[0-9]{6}"
            maxlength="6"
            minlength="4"
            inputmode="numeric"
            autocomplete="one-time-code"
            placeholder="Enter 6 digit OTP"
            required
        >

    </div>

    <div class="login-buttons">

        <button type="submit"
                class="login-btn login-btn-primary">

            <span class="btn-text">
                <i class="bi bi-shield-check"></i>
                सबमिट करें
            </span>

            <span class="btn-loader d-none">
                <span class="spinner-border spinner-border-sm"></span>
                Verifying...
            </span>

        </button>

    </div>

</form>


            <div class="login-footer">
                <i class="bi bi-lock-fill me-1"></i>
                सुरक्षित OTP सत्यापन
            </div>

        </div>

    </div>

</div>

<?= $this->endSection() ?>