<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<section class="documents-section py-5">

    <div class="container">

        <div class="text-center mb-5">
            <span class="text-primary fw-semibold">DOCUMENT LIBRARY</span>
            <h2 class="mt-2">समर्थित दस्तावेज</h2>
            <p class="text-muted">
                मुख्यमंत्री अंत्योदय परिवार उत्थान योजना से संबंधित
                महत्वपूर्ण दस्तावेज देखें।
            </p>
        </div>

        <?php
        $documents = [
            [
                'title' => 'Scheme Description Template',
                'file' => 'Scheme Description Template.pdf'
            ],
            [
                'title' => 'Download Final Scheme Booklet',
                'file' => 'MMAPUY 2.0  Scheme Booklet V1(updated 17.07.2026).pdf'
            ],
            [
                'title' => 'Download SoP of MMAPUY for Banks',
                'file' => 'SoP_MMAPUY_Banks.pdf'
            ],
            [
                'title' => 'Download SoP of MMAPUY',
                'file' => 'MMAPUY 2.0_SOP.pdf'
            ],
            [
                'title' => 'Download Consent Letter',
                'file' => 'consent_letter.pdf'
            ],
            [
                'title' => 'MMAPUY Success Stories: Hindi',
                'file' => 'MMAPUY Success Stories:  Hindi.pdf'
            ],
            [
                'title' => 'MMAPUY Success Stories: English',
                'file' => 'MMAPUY Success Stories:  English.pdf'
            ],
            [
                'title' => 'Download Pre-Mela SoP of MMAPUY',
                'file' => 'MMAPUY 2.0_Pre_Mela SOP.pdf'
            ],
            [
                'title' => 'MMAPUY Portal User Manual for ADCs',
                'file' => 'User Manual.pdf'
            ],
            [
                'title' => 'MMAPUY 2.0 App Workflow Guidelines',
                'file' => 'MMAPUY 2.0 App Workflow Guidelines.pdf'
            ],
            [
                'title' => 'Department Scheme List: Hindi',
                'file' => 'Department scheme list hindi.pdf'
            ],
            [
                'title' => 'Department With Scheme Name: English',
                'file' => 'Department with scheme name english.pdf'
            ]
        ];
        ?>

        <div class="row g-4">

            <?php foreach ($documents as $document): ?>

                <div class="col-12 col-md-6 col-lg-4">

                    <div class="document-card h-100">

                        <div class="document-icon">
                            <img
                                src="<?= base_url('assets/images/guide.png') ?>"
                                alt="PDF Document">
                        </div>

                        <h6>
                            <?= esc($document['title']) ?>
                        </h6>

                        <a
                            href="<?= base_url('assets/uploads/' . rawurlencode($document['file'])) ?>"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="btn btn-success w-100">

                            <i class="bi bi-file-earmark-pdf me-2"></i>
                            View Document

                        </a>

                    </div>

                </div>

            <?php endforeach; ?>

        </div>

    </div>

</section>

<?= $this->endSection() ?>