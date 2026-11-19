<?php
/**
 * "Download Excel" button for the district report tables.
 * Exports the table with id="table1" to a real .xlsx file in the browser (SheetJS),
 * so it always matches what is on screen (including filters and the Total row).
 *
 * Expects: $filename  base file name, e.g. 'family_wise_report'
 */
$filename = $filename ?? 'district_report';
?>
<button type="button" id="exportExcel" class="btn btn-success btn-sm"
    data-filename="<?= esc($filename, 'attr') ?>">
    <i class="bi bi-file-earmark-excel"></i> Download Excel
</button>

<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script>
    document.getElementById('exportExcel').addEventListener('click', function () {

        const table = document.getElementById('table1');

        if (!table) {
            alert('Nothing to export.');
            return;
        }

        if (typeof XLSX === 'undefined') {
            alert('Excel library could not be loaded. Please check your internet connection and try again.');
            return;
        }

        const filename = this.dataset.filename;

        if (window.PageLoader) PageLoader.show('Preparing Excel file\u2026');

        // Small delay so the loader is painted before the (blocking) file generation starts.
        setTimeout(function () {
            try {
                const workbook = XLSX.utils.table_to_book(table, { sheet: 'Report' });

                const d = new Date();
                const stamp = d.getFullYear() + '-' +
                    String(d.getMonth() + 1).padStart(2, '0') + '-' +
                    String(d.getDate()).padStart(2, '0');

                XLSX.writeFile(workbook, filename + '_' + stamp + '.xlsx');
            } finally {
                if (window.PageLoader) PageLoader.hide();
            }
        }, 60);
    });
</script>