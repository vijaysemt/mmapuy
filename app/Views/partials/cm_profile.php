
<?php
$portalConfig = config('Portal');
$cm = $portalConfig->cmProfile;

$profileType = $profileType ?? 'login';
?>

<?php if ($profileType === 'home'): ?>

    <!-- HOME PAGE CM PROFILE -->

    <div class="col-lg-4 text-center mb-4 mb-lg-0">

        <div class="cm-card">

            <div class="cm-image">
                <img src="<?= base_url($cm['image']) ?>"
                     alt="<?= esc($cm['alt']) ?>"
                     class="img-fluid">
            </div>

            <h4><?= esc($cm['name']) ?></h4>

            <p><?= esc($cm['designation']) ?></p>

        </div>

    </div>

<?php else: ?>

    <!-- LOGIN / OTP / PASSWORD PAGE CM PROFILE -->

    <div class="login-left" id="login_otp">

        <div class="cm-profile">

            <img src="<?= base_url($cm['image']) ?>"
                 alt="<?= esc($cm['alt']) ?>"
                 class="img-fluid">

            <h2><?= esc($cm['name']) ?></h2>

            <h5><?= esc($cm['designation']) ?></h5>

            <div class="login-left-divider"></div>

            <p class="login-left-description">
                <?= esc($cm['description']) ?>
            </p>

        </div>

    </div>

<?php endif; ?>
