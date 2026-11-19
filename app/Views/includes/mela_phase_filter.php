<?php
/**
 * Version + mela phase filter.   File: app/Views/includes/mela_phase_filter.php
 *
 * Expects: $selected_version ('' | '1' | '2'), $selected_mela_phase ('' | '6' | '6_ssa'), $mela_phases
 * Optional: $extra  array of other query params to keep, e.g. ['d' => '01'] (the district filter)
 */
$selectedVersion = (string) ($selected_version ?? '');
$selectedPhase   = (string) ($selected_mela_phase ?? '');
$extra           = array_filter((array) ($extra ?? []), static fn($v): bool => $v !== '' && $v !== null);
?>
<form method="get" action="<?= current_url() ?>" class="d-flex gap-2 align-items-center flex-wrap">

    <?php foreach ($extra as $name => $value): ?>
        <input type="hidden" name="<?= esc((string) $name, 'attr') ?>" value="<?= esc((string) $value, 'attr') ?>">
    <?php endforeach; ?>

    <select name="v" class="form-select form-select-sm" style="width:auto"
        onchange="this.form.mela_phase.value='';this.form.submit()">
        <option value="" <?= $selectedVersion === '' || $selectedVersion === '0' ? 'selected' : '' ?>>All versions</option>
        <option value="1" <?= $selectedVersion === '1' ? 'selected' : '' ?>>MMAPUY 1.0</option>
        <option value="2" <?= $selectedVersion === '2' ? 'selected' : '' ?>>MMAPUY 2.0</option>
    </select>

    <select name="mela_phase" class="form-select form-select-sm" style="width:auto">
        <option value="">All phases</option>
        <?php foreach ($mela_phases as $phase): ?>
            <?php
            $value = (int) $phase['is_ssa'] === 1
                ? $phase['mela_phase_number'] . '_ssa'
                : (string) $phase['mela_phase_number'];
            ?>
            <option value="<?= esc($value) ?>" <?= $selectedPhase === $value ? 'selected' : '' ?>>
                <?= esc($phase['mela_phase_name']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <button type="submit" class="btn btn-primary btn-sm">Apply</button>

    <a class="btn btn-outline-secondary btn-sm"
        href="<?= current_url() . '?' . http_build_query($extra + [
                    'v' => $selectedVersion, 'mela_phase' => $selectedPhase, 'refresh' => 1,
                ]) ?>">
        <i class="bi bi-arrow-clockwise"></i> Refresh
    </a>
</form>
