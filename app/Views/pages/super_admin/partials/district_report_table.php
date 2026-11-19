<?php
/**
 * Shared table for the district reports.
 *
 * Expects:
 *   $columns  list of [label, key, pendingType|null]   (pendingType 1,2,3 => link to pending list)
 *   $rows     rows from DistrictReportModel (last row = Total)
 *   $version  "1", "2" or "1,2"
 *   $urls     ['district' => url, 'pending' => url]
 *   $fType    1 = family wise, 2 = member wise
 */
?>
<div class="table-responsive table-static-header">
    <table class="table table-bordered table-hover align-middle text-center mt-1" id="table1">

        <thead class="bg-info text-white text-center">
            <tr class="bg-info">
                <th class="text-white text-center">Sr. No</th>
                <th class="text-white text-center">District</th>
                <?php foreach ($columns as [$label]): ?>
                    <th class="text-white text-center"><?= esc($label) ?></th>
                <?php endforeach; ?>
            </tr>
        </thead>

        <tbody>
            <?php if (count($rows) < 2): ?>
                <tr>
                    <td colspan="<?= count($columns) + 2 ?>" class="text-center text-muted py-4">
                        No data found.
                    </td>
                </tr>
            <?php else: ?>

                <?php $sr = 1; ?>
                <?php foreach ($rows as $row): ?>
                    <?php
                    $id      = (string) ($row['id'] ?? '');
                    $isTotal = ($row['District'] ?? '') === 'Total';
                    ?>
                    <tr class="<?= $isTotal ? 'fw-bold table-light' : '' ?>">

                        <td><?= $isTotal ? '' : $sr++ ?></td>

                        <td>
                            <?php if ($id !== ''): ?>
                                <a href="<?= $urls['district'] . '?' . http_build_query(['dcode' => $id, 'v' => $version]) ?>">
                                    <?= esc($row['District']) ?>
                                </a>
                            <?php else: ?>
                                <?= esc($row['District']) ?>
                            <?php endif; ?>
                        </td>

                        <?php foreach ($columns as [$label, $key, $pendingType]): ?>
                            <?php $value = $row[$key] ?? null; ?>
                            <td>
                                <?php if ($value === null): ?>
                                    <span class="text-muted">&mdash;</span>
                                <?php elseif ($pendingType !== null && $id !== ''): ?>
                                    <a href="<?= $urls['pending'] . '?' . http_build_query([
                                                    'dist_id' => $id,
                                                    'type'    => $pendingType,
                                                    'f_type'  => $fType,
                                                    'v'       => $version,
                                                ]) ?>">
                                        <?= esc((string) $value) ?>
                                    </a>
                                <?php else: ?>
                                    <?= esc((string) $value) ?>
                                <?php endif; ?>
                            </td>
                        <?php endforeach; ?>

                    </tr>
                <?php endforeach; ?>

            <?php endif; ?>
        </tbody>

    </table>
</div>