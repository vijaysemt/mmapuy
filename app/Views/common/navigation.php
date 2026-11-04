 
<!-- NAVIGATION -->
<nav class="navbar navbar-expand-lg main-nav">
    <div class="container">

        <button class="navbar-toggler text-white"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#mainNavigation"
                aria-controls="mainNavigation"
                aria-expanded="false"
                aria-label="Toggle navigation">

            <i class="bi bi-list fs-2"></i>
        </button>

        <div class="collapse navbar-collapse" id="mainNavigation">

            <ul class="navbar-nav me-auto">

                <li class="nav-item">
                    <a class="nav-link active" href="<?= base_url() ?>">
                        <i class="bi bi-house-door me-1"></i> होम
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="<?= base_url('/#about') ?>">
                        योजना के बारे में
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="<?= base_url('/#schemes') ?>">
                        विभाग एवं योजनाएं
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link"
                       href="<?= site_url('download-documents') ?>">
                        दस्तावेज डाउनलोड
                    </a>
                </li>

            </ul>

            <a href="<?= site_url('login#login_otp') ?>" class="btn header-login-btn">
                <i class="bi bi-person-circle me-1"></i>
                लॉग इन
            </a>

        </div>
    </div>
</nav>
