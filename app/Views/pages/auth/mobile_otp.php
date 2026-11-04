<div class="login-page">

    <div class="login-wrapper">

        <!-- LEFT PANEL -->
        <div class="login-left">

            <div class="cm-profile">

                <img src="<?= base_url('assets/mmapua/images/cmo_hr_img1.png') ?>"
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


            <form action="<?= site_url('Login/validateotp') ?>"
                  method="post"
                  class="login-form"
                  id="otpForm">

                <?= csrf_field() ?>

                <div class="form-group">

                    <label for="otp">
                        <i class="bi bi-key me-1"></i>
                        OTP दर्ज करें
                    </label>

                    <input type="text"
                           class="form-control"
                           name="otp"
                           id="otp"
                           pattern="[0-9]{6}"
                           maxlength="6"
                           minlength="6"
                           inputmode="numeric"
                           placeholder="Enter 6 digit OTP"
                           autocomplete="one-time-code"
                           required>

                    <small class="text-muted mt-2 d-block">
                        कृपया 6 अंकों का OTP दर्ज करें।
                    </small>

                </div>


                <input type="hidden"
                       name="mob"
                       id="mob"
                       value="<?= esc($mob ?? '') ?>">


                <div class="login-buttons">

                    <button type="submit"
                            name="submit"
                            class="login-btn login-btn-primary"
                            id="submit"
                            onclick="optSubmit()">

                        <i class="bi bi-shield-check"></i>
                        सबमिट करें

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