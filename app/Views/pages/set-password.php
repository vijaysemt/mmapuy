<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="login-page">

    <div class="login-wrapper">

        <!-- LEFT PANEL -->
        <div class="login-left">

            <div class="cm-profile">

                <img src="<?= base_url('assets/images/cmo_hr_img1.png') ?>"
                     alt="मुख्यमंत्री हरियाणा"
                     class="img-fluid">

                <h2>श्री नायब सिंह सैनी</h2>

                <h5>माननीय मुख्यमंत्री, हरियाणा</h5>

                <div class="login-left-divider"></div>

                <p class="login-left-description">
                    मुख्यमंत्री अंत्योदय परिवार उत्थान योजना
                    के आधिकारिक पोर्टल पर आपका स्वागत है।
                </p>

            </div>

        </div>


        <!-- RIGHT PANEL -->
        <div class="login-right">

            <span class="login-top-label">
                <i class="bi bi-shield-lock-fill"></i>
                PASSWORD SETUP
            </span>

            <h1>पासवर्ड सेट करें</h1>

            <p class="login-subtitle">
                अपने अकाउंट की सुरक्षा के लिए नया पासवर्ड दर्ज करें।
            </p>


            <?php if (service('request')->getGet('status') === 'ERROR'): ?>

                <div class="alert alert-warning login-alert">
                    <strong>Error!</strong>
                    Password or Confirm does not match.
                </div>

            <?php endif; ?>


            <?php if (session()->getFlashdata('error')): ?>

                <div class="alert alert-danger login-alert">
                    <?= esc(session()->getFlashdata('error')) ?>
                </div>

            <?php endif; ?>


            <form action="<?= site_url('Login/validatePassword') ?>"
                  method="post"
                  class="login-form"
                  id="passwordForm">

                <?= csrf_field() ?>


                <!-- PASSWORD -->
                <div class="form-group">

                    <label for="pass">
                        <i class="bi bi-lock me-1"></i>
                        नया पासवर्ड
                    </label>

                    <div class="password-input-wrapper">

                        <input type="password"
                               class="form-control"
                               name="pass"
                               id="pass"
                               placeholder="नया पासवर्ड दर्ज करें"
                               autocomplete="new-password"
                               required>

                        <button type="button"
                                class="password-toggle"
                                data-target="pass"
                                aria-label="Show password">

                            <i class="bi bi-eye"></i>

                        </button>

                    </div>

                </div>


                <!-- CONFIRM PASSWORD -->
                <div class="form-group">

                    <label for="con_pass">
                        <i class="bi bi-lock-fill me-1"></i>
                        पासवर्ड की पुष्टि करें
                    </label>

                    <div class="password-input-wrapper">

                        <input type="password"
                               class="form-control"
                               name="con_pass"
                               id="con_pass"
                               placeholder="पासवर्ड दोबारा दर्ज करें"
                               autocomplete="new-password"
                               required>

                        <button type="button"
                                class="password-toggle"
                                data-target="con_pass"
                                aria-label="Show password">

                            <i class="bi bi-eye"></i>

                        </button>

                    </div>

                </div>


                <div id="passwordMismatch"
                     class="text-danger small mb-3"
                     style="display:none;">

                    दोनों पासवर्ड मेल नहीं खाते।

                </div>


                <div class="login-buttons">

                    <button type="submit"
                            name="submit"
                            class="login-btn login-btn-primary">

                        <i class="bi bi-shield-check"></i>
                        पासवर्ड सबमिट करें

                    </button>

                </div>

            </form>


            <div class="login-footer">
                <i class="bi bi-lock-fill me-1"></i>
                आपका पासवर्ड सुरक्षित रखा जाएगा।
            </div>

        </div>

    </div>

</div>


<script>
document.addEventListener('DOMContentLoaded', function () {

    // Password visibility toggle

    document.querySelectorAll('.password-toggle').forEach(function(button) {

        button.addEventListener('click', function() {

            const targetId = this.getAttribute('data-target');
            const input = document.getElementById(targetId);
            const icon = this.querySelector('i');

            if (input.type === 'password') {

                input.type = 'text';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');

            } else {

                input.type = 'password';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');

            }

        });

    });


    // Confirm password validation

    const passwordForm = document.getElementById('passwordForm');

    passwordForm.addEventListener('submit', function(event) {

        const password = document.getElementById('pass').value;
        const confirmPassword = document.getElementById('con_pass').value;

        const mismatch = document.getElementById('passwordMismatch');

        if (password !== confirmPassword) {

            event.preventDefault();

            mismatch.style.display = 'block';

        } else {

            mismatch.style.display = 'none';

        }

    });

});
</script>

<?= $this->endSection() ?>