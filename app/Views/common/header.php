
<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title><?= esc($title ?? 'मुख्यमंत्री अंत्योदय परिवार उत्थान योजना') ?> | हरियाणा सरकार</title>

    <meta name="description"
          content="मुख्यमंत्री अंत्योदय परिवार उत्थान योजना, हरियाणा सरकार">

    <link rel="icon" type="image/png"
          href="<?= base_url('assets/images/favicon-32x32.png') ?>">

    <!-- Hindi Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Devanagari:wght@400;500;600;700;800&display=swap"
          rel="stylesheet">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css"
          rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet"
          href="<?= base_url('assets/mmapua/css/home.css') ?>">

    <link rel="stylesheet"
      href="<?= base_url('assets/mmapua/css/login.css') ?>">

      <link rel="stylesheet"
      href="<?= base_url('assets/mmapua/css/dashboard.css') ?>">
</head>

<body>

<!-- HEADER -->
<header class="main-header">
    <div class="container">
        <div class="row align-items-center py-3">

            <div class="col-8 col-lg-5">
                <div class="brand-area">
                    <img src="<?= base_url('assets/images/logo.png') ?>"
                         alt="MMAPUY Logo">

                    <div>
                        <h5>मुख्यमंत्री अंत्योदय परिवार उत्थान योजना</h5>
                        <h5>हरियाणा सरकार</h5>
                    </div>
                </div>
            </div>

            <div class="col-4 col-lg-3 text-center d-none d-lg-block">
                <img class="amrit-logo"
                     src="<?= base_url('assets/images/logo-amrit-mahotsav.png') ?>"
                     alt="Azadi Ka Amrit Mahotsav">
            </div>

            <div class="col-4 col-lg-4 text-end d-none d-lg-block">
                <img class="state-logo"
                     src="<?= base_url('assets/images/haryana-logo.png') ?>"
                     alt="Government of Haryana">
            </div>

        </div>
    </div>
</header>
