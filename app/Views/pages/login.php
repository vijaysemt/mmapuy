
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="login-page">

    <div class="login-wrapper">

        <!-- LEFT SECTION -->
        <div class="login-left">

            <div class="cm-profile">

                <img src="<?= base_url('assets/images/cmo_hr_img1.png') ?>"
                     alt="माननीय मुख्यमंत्री हरियाणा"
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


        <!-- RIGHT SECTION -->
        <div class="login-right">

            <span class="login-top-label">
                <i class="bi bi-shield-lock-fill"></i>
                SECURE LOGIN PORTAL
            </span>

            <h1>आपका स्वागत है!</h1>

            <p class="login-subtitle">
                मुख्यमंत्री अंत्योदय परिवार उत्थान योजना
                जारी रखने के लिए लॉगिन करें।
            </p>


            <?php if (service('request')->getGet('status') === 'ERROR'): ?>

                <div class="alert alert-warning login-alert">
                    <strong>Error!</strong>
                    Invalid Username or password. Please set your
                    password first after login through OTP.
                </div>

            <?php endif; ?>


            <?php if (session()->getFlashdata('error')): ?>

                <div class="alert alert-danger login-alert">
                    <?= esc(session()->getFlashdata('error')) ?>
                </div>

            <?php endif; ?>


            <form action="<?= site_url('Login/validate') ?>"
                  method="post"
                  class="login-form">

                <?= csrf_field() ?>

                <!-- MOBILE -->
                <div class="form-group">

                    <label for="mobile">
                        <i class="bi bi-phone me-1"></i>
                        मोबाइल नंबर
                    </label>

                    <input type="tel"
                           class="form-control"
                           name="mobile"
                           id="mobile"
                           placeholder="10 अंकों का मोबाइल नंबर दर्ज करें"
                           pattern="[0-9]{10}"
                           maxlength="10"
                           minlength="10"
                           inputmode="numeric"
                           autocomplete="off"
                           required>

                </div>


                <!-- ROLE -->
                <div class="form-group">

                    <label for="role">
                        <i class="bi bi-person-badge me-1"></i>
                        रोल का चयन करें
                    </label>

                    <select class="form-select"
                            name="role"
                            id="role"
                            required>

                        <option value="">-- रोल चुनें --</option>

                        <option value="9">Super Admin</option>
                        <option value="10">SLBC</option>
                        <option value="2">District User (DIO)</option>
                        <option value="3">State Department User</option>
                        <option value="6">Department District User</option>
                        <option value="8">ADC User</option>
                        <option value="11">LDM User</option>
                        <option value="18">Controlling heads</option>

                    </select>

                </div>


                <!-- BUTTONS -->
                <div class="login-buttons">

                    <button type="submit"
                            name="submit"
                            class="login-btn login-btn-primary">

                        <i class="bi bi-shield-check"></i>
                        ओटीपी से लॉगिन करें

                    </button>


                    <a href="<?= site_url('password-login') ?>"
                       class="login-btn login-btn-secondary">

                        <i class="bi bi-key"></i>
                        पासवर्ड से लॉगिन

                    </a>

                </div>

            </form>


            <div class="login-footer">

                <i class="bi bi-lock-fill me-1"></i>
                सुरक्षित सरकारी पोर्टल

            </div>

        </div>

    </div>

</div>

<?= $this->endSection() ?>