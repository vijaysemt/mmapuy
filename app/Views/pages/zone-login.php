<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="login-page">

    <div class="login-wrapper">

        <!-- LEFT PANEL -->
        <div class="login-left">

            <div class="cm-profile">

                <img src="<?= base_url('assets/mmapua/home_page/images/cmo_hr_img1.png') ?>"
                     alt="मुख्यमंत्री हरियाणा"
                     class="img-fluid">

                <h2>MMAPUA Survey</h2>

                <h5>District Login Portal</h5>

                <div class="login-left-divider"></div>

                <p class="login-left-description">
                    मुख्यमंत्री अंत्योदय परिवार उत्थान योजना
                    के सर्वेक्षण पोर्टल पर आपका स्वागत है।
                </p>

            </div>

        </div>


        <!-- RIGHT PANEL -->
        <div class="login-right">

            <span class="login-top-label">
                <i class="bi bi-shield-lock-fill"></i>
                DISTRICT ACCESS PORTAL
            </span>

            <h1>District Login</h1>

            <p class="login-subtitle">
                जारी रखने के लिए अपना पंजीकृत मोबाइल नंबर दर्ज करें।
            </p>


            <?php if (session()->getFlashdata('error')): ?>

                <div class="alert alert-danger login-alert">
                    <?= esc(session()->getFlashdata('error')) ?>
                </div>

            <?php endif; ?>


            <form action="<?= site_url('Zone_user/validate') ?>"
                  method="post"
                  class="login-form">

                <?= csrf_field() ?>

                <div class="form-group">

                    <label for="mobile">
                        <i class="bi bi-phone me-1"></i>
                        Mobile Number
                    </label>

                    <input type="tel"
                           class="form-control"
                           name="mobile"
                           id="mobile"
                           pattern="[0-9]{10}"
                           maxlength="10"
                           minlength="10"
                           inputmode="numeric"
                           placeholder="Enter 10 digit mobile number"
                           autocomplete="off"
                           required>

                </div>


                <div class="login-buttons">

                    <button type="submit"
                            name="submit"
                            class="login-btn login-btn-primary">

                        <i class="bi bi-box-arrow-in-right"></i>
                        SUBMIT

                    </button>

                </div>

            </form>


            <div class="login-footer">
                <i class="bi bi-lock-fill me-1"></i>
                Secure Government Portal
            </div>

        </div>

    </div>

</div>
<?= $this->endSection() ?>