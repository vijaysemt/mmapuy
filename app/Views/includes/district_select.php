<?php
/**
 * District dropdown. Changing it reloads the page with ?d=<id> and keeps the other filters.
 * File: app/Views/includes/district_select.php
 *
 * Expects: $district_list (id, dist_name), $selected_district ('' | 'all' | district id)
 */
$selectedDistrict = (string) ($selected_district ?? '');
?>
<div class="d-flex align-items-center gap-2">
    <label for="districtSelect" class="fw-semibold mb-0 text-nowrap">Select District</label>

    <select class="form-select form-select-sm" style="min-width:200px" name="district" id="districtSelect">
        <option value="">-- Select District --</option>
        <option value="all" <?= $selectedDistrict === 'all' ? 'selected' : '' ?>>-- All --</option>

        <?php foreach ($district_list as $district): ?>
            <option value="<?= esc((string) $district['id'], 'attr') ?>"
                <?= $selectedDistrict === (string) $district['id'] ? 'selected' : '' ?>>
                <?= esc($district['dist_name']) ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>

<script>
    document.getElementById('districtSelect').addEventListener('change', function () {
        const url = new URL(window.location.href);

        if (this.value === '') {
            url.searchParams.delete('d');
        } else {
            url.searchParams.set('d', this.value);
        }

        url.searchParams.delete('refresh');

        if (window.PageLoader) PageLoader.show();   // loader, because assigning location.href is not a click or submit
        window.location.href = url.toString();
    });
</script>