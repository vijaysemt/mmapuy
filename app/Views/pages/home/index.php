
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!-- HERO SECTION -->
<section class="hero-section" id="about">

    <div class="container">
        <div class="row align-items-center">

            <?= view('partials/cm_profile', [
                'profileType' => 'home'
            ]) ?>

            <div class="col-lg-8">

                <span class="welcome-label">
                    <i class="bi bi-stars me-2"></i>
                    आपका स्वागत है
                </span>

                <h1 class="hero-title">
                    मुख्यमंत्री अंत्योदय परिवार उत्थान योजना
                </h1>

                <p class="hero-description">
                    मुख्यमंत्री अंत्योदय परिवार उत्थान योजना हरियाणा सरकार
                    की एक महत्वपूर्ण पहल है। इसके तहत राज्य के सबसे गरीब
                    परिवारों की पहचान कर उन्हें कौशल विकास, स्वरोजगार
                    एवं रोजगार सृजन के माध्यम से आर्थिक रूप से सशक्त
                    बनाने का प्रयास किया जाता है। इस योजना का उद्देश्य
                    अंत्योदय परिवारों की न्यूनतम वार्षिक आय 1.80 लाख रुपये
                    तक बढ़ाने के लिए विभिन्न सरकारी योजनाओं का लाभ
                    उपलब्ध करवाना है।
                </p>

                <div class="hero-buttons">

                    <a href="https://play.google.com/store/apps/details?id=com.ditech.mmapua"
                       target="_blank"
                       rel="noopener noreferrer"
                       class="btn btn-download">

                        <i class="bi bi-google-play me-2"></i>
                        Android App
                    </a>

                    <a href="https://apps.apple.com/in/app/antyodaya-parivar-uthan-yojana/id1603806984"
                       target="_blank"
                       rel="noopener noreferrer"
                       class="btn btn-download">

                        <i class="bi bi-apple me-2"></i>
                        iOS App
                    </a>

                    <a href="<?= site_url('/download-documents') ?>"
                       class="btn btn-documents">

                        <i class="bi bi-file-earmark-pdf me-2"></i>
                         Download Documents
                    </a>

                </div>

            </div>
        </div>
    </div>
</section>

<!-- SCHEME SEARCH -->
<section class="scheme-section" id="schemes">

    <div class="container">

        <div class="section-heading text-center">
            <span>EXPLORE SCHEMES</span>
            <h2>विभाग एवं योजनाओं की जानकारी</h2>
            <p>योजना की जानकारी प्राप्त करने के लिए विभाग एवं योजना चुनें</p>
        </div>

        <div class="scheme-search-card">

            <div class="row g-3 align-items-end">

                <div class="col-lg-4">

                    <label for="department_name" class="form-label">
                        <i class="bi bi-building me-1"></i>
                        विभाग का चयन करें
                    </label>

                    <select class="form-select" id="department_name">

                        <option value="">-- विभाग चुनें --</option>

                        <?php if (!empty($Get_department_name)): ?>
                            <?php foreach ($Get_department_name as $value): ?>

                                <option value="<?= esc($value['id']) ?>">
                                    <?= esc($value['name']) ?>
                                </option>

                            <?php endforeach; ?>
                        <?php endif; ?>

                    </select>

                </div>

                <div class="col-lg-4">

                    <label for="scheme_list" class="form-label">
                        <i class="bi bi-journal-text me-1"></i>
                        योजना का चयन करें
                    </label>

                    <select class="form-select" id="scheme_list" disabled>
                        <option value="">-- योजना चुनें --</option>
                    </select>

                </div>

                <div class="col-lg-4">

                    <label for="sub_scheme_list" class="form-label">
                        <i class="bi bi-diagram-3 me-1"></i>
                        उप योजना का चयन करें
                    </label>

                    <select class="form-select" id="sub_scheme_list" disabled>
                        <option value="">-- उप योजना चुनें --</option>
                    </select>

                </div>

                <div class="col-12 text-center mt-4" id="scheme-result">

                    <div id="scheme-loader" class="d-none">
                        <div class="spinner-border spinner-border-sm"></div>
                        जानकारी लोड हो रही है...
                    </div>

                    <div id="viewScheme"></div>

                </div>

            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>
