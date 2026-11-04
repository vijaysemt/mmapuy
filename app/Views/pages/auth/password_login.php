
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="login-page">

    <div class="login-wrapper">

        <!-- LEFT SECTION -->
        <div class="login-left" id="login_password">

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

            <h1>पासवर्ड से लॉगिन करें</h1>

            <p class="login-subtitle">
                अपनी लॉगिन जानकारी दर्ज करके पोर्टल पर आगे बढ़ें।
            </p>


            <?php if (service('request')->getGet('status') === 'ERROR'): ?>

                <div class="alert alert-warning login-alert">
                    <strong>Error!</strong>
                    Please Enter Valid Credential.
                </div>

            <?php endif; ?>


            <?php if (session()->getFlashdata('error')): ?>

                <div class="alert alert-danger login-alert">
                    <?= esc(session()->getFlashdata('error')) ?>
                </div>

            <?php endif; ?>


            <form class = "login-form" action="<?= base_url('demo-password-login') ?>" method="post">

                <?= csrf_field() ?>


                <!-- MOBILE / USERNAME -->
                <div class="form-group">

                    <label for="mobile">
                        <i class="bi bi-person me-1"></i>
                        मोबाइल नंबर / Username
                    </label>

                    <input type="text"
                           class="form-control"
                           name="mobile"
                           id="mobile"
                           maxlength="20"
                           minlength="6"
                           placeholder="मोबाइल नंबर या Username दर्ज करें"
                           autocomplete="off"
                           required>
                            
                </div>


                <!-- PASSWORD -->
                <div class="form-group">

                    <label for="password">
                        <i class="bi bi-lock me-1"></i>
                        Password
                    </label>

                    <input type="password"
                           class="form-control"
                           name="password"
                           id="password"
                           placeholder="अपना Password दर्ज करें"
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

                        <option value="2">District User (DIO)</option>
                        <option value="3">State Department User</option>
                        <option value="6">Department District User</option>
                        <option value="8">ADC User</option>
                        <option value="11">LDM User</option>
                        <option value="12">Branch User</option>
                        <option value="13">Bank User</option>
                        <option value="18">Controlling heads</option>
                        <option value="20">State caller User</option>
                        <option value="21">District caller User</option>
                        <option value="23">Seva Setu Abhiyan District User</option>
                        <option value="24">Seva Setu Abhiyan Admin User</option>

                    </select>

                </div>


                <!-- BUTTONS -->
                <div class="login-buttons">

                    <button type="submit"
                            name="submit"
                            class="login-btn login-btn-primary">

                        <i class="bi bi-box-arrow-in-right"></i>
                        लॉगिन करें

                    </button>


                    <a href="<?= site_url('login#login_otp') ?>"
                       class="login-btn login-btn-secondary">

                        <i class="bi bi-phone"></i>
                        OTP से लॉगिन

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