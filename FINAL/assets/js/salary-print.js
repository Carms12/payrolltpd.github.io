// salary-print - extracted from views/admin/salary.php (print / export view)
// Values are injected by the view as window.SALARY_PRINT:
//   autoPrint (bool), excel (bool), monthYear (string), colCount (int), presetSuffix (string)

(function () {
    var cfg = window.SALARY_PRINT || {};

    if (cfg.autoPrint) {
        window.onload = function () {
            setTimeout(function () { window.print(); }, 500);
        };
    }

    if (cfg.excel) {
        window.onload = function () {
            setTimeout(function () {
                var table = document.getElementById('payrollTable');
                // spacer rows exist only for printing - keep them out of the Excel file
                table.querySelectorAll('.page-gap, .page-gap-foot').forEach(function (r) { r.remove(); });
                var wb = XLSX.utils.table_to_book(table, {sheet: "Payroll", raw: true});
                var ws = wb.Sheets["Payroll"];
                var colWidths = [];
                for (var i = 0; i < cfg.colCount; i++) colWidths.push({wch: 8});
                ws['!cols'] = colWidths;
                var wbout = XLSX.write(wb, {bookType: 'xlsx', type: 'array'});
                var blob = new Blob([wbout], {type: 'application/octet-stream'});
                var link = document.createElement('a');
                link.href = URL.createObjectURL(blob);
                link.download = 'Payroll_' + cfg.monthYear + cfg.presetSuffix + '.xlsx';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                setTimeout(function () { window.close(); }, 1000);
            }, 300);
        };
    }
})();
