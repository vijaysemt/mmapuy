
<!-- FOOTER -->
<footer class="main-footer">

    <div class="container">
        <div class="row align-items-center">

            <div class="col-lg-9">

                <p>
                    Developed by
                    <strong>
                        Citizen Resources Information Department,
                        Government of Haryana.
                    </strong>

                    Content owned by
                    <strong>
                        Department of Social Justice and Empowerment,
                        Government of Haryana.
                    </strong>
                </p>

                <p>
                    <small>
                        © <?= date('Y') ?> Government of Haryana.
                        All Rights Reserved.
                    </small>
                </p>

            </div>

            <div class="col-lg-3 text-lg-end mt-3 mt-lg-0">

                <div class="d-flex align-items-center justify-content-lg-end gap-2">

                    <h6 class="mb-0">Visitor Counts:</h6>

                    <span class="visitor-count">
                        <?= esc($counter[0]['counter'] ?? '0') ?>
                    </span>

                </div>

            </div>

        </div>
    </div>

</footer>
