// Excel export and print helpers for the Vue report pages.
// SheetJS (window.XLSX) is loaded from CDN in reports/state_report*.blade.php, as in company_report.

function periodText(startDate, endDate) {
    if (!startDate && !endDate) return "Barcha sanalar";
    return `${startDate || "..."} — ${endDate || "..."}`;
}

function fileDate() {
    return new Date().toISOString().split("T")[0];
}

function safeFileName(name) {
    return name.replace(/[\\/:*?"<>|]+/g, "").replace(/\s+/g, "_");
}

/**
 * @param {Object} options
 * @param {string} options.title      report title (first row of the sheet)
 * @param {string} options.fileName   file name without date/extension
 * @param {string} [options.startDate]
 * @param {string} [options.endDate]
 * @param {Array<{label: string, width?: number}>} options.columns
 * @param {Array<Array>} options.rows  cell values, numbers stay numbers
 * @param {Array} [options.totals]    last row
 */
export function exportToExcel({ title, fileName, startDate, endDate, columns, rows, totals }) {
    const XLSX = window.XLSX;
    if (!XLSX) {
        alert("Excel kutubxonasi yuklanmadi. Sahifani yangilab qayta urinib ko'ring.");
        return;
    }

    const data = [
        [title],
        [`Davr: ${periodText(startDate, endDate)}`],
        [],
        columns.map((column) => column.label),
        ...rows,
    ];
    if (totals) {
        data.push(totals);
    }

    const ws = XLSX.utils.aoa_to_sheet(data);
    ws["!cols"] = columns.map((column) => ({ wch: column.width || 15 }));
    ws["!merges"] = [
        { s: { r: 0, c: 0 }, e: { r: 0, c: columns.length - 1 } },
        { s: { r: 1, c: 0 }, e: { r: 1, c: columns.length - 1 } },
    ];

    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, "Hisobot");
    XLSX.writeFile(wb, `${safeFileName(fileName)}_${fileDate()}.xlsx`);
}

function escapeHtml(value) {
    return String(value ?? "")
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;");
}

/**
 * Opens a print window with the given table element.
 */
export function printReport({ title, startDate, endDate, table }) {
    if (!table) return;

    const printWindow = window.open("", "", "height=700,width=1000");
    if (!printWindow) {
        alert("Chop etish oynasini ochib bo'lmadi. Brauzerda pop-up oynalarga ruxsat bering.");
        return;
    }

    printWindow.document.write(`<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>${escapeHtml(title)}</title>
    <style>
        @page { size: A4 landscape; margin: 12mm; }
        body { font-family: Arial, sans-serif; color: #000; }
        h2 { margin: 0 0 4px; font-size: 18px; text-align: center; }
        .period { margin-bottom: 12px; font-size: 12px; text-align: center; }
        table { width: 100%; border-collapse: collapse; font-size: 11px; }
        th, td { padding: 5px 6px; border: 1px solid #555; text-align: left; }
        th { background: #eee; }
        th span { display: none; }
        a { color: #000; text-decoration: none; }
        tr:last-child td { font-weight: bold; background: #f5f5f5; }
    </style>
</head>
<body>
    <h2>${escapeHtml(title)}</h2>
    <div class="period">Davr: ${escapeHtml(periodText(startDate, endDate))} · Chop etilgan: ${fileDate()}</div>
    ${table.outerHTML}
</body>
</html>`);
    printWindow.document.close();
    printWindow.focus();
    setTimeout(() => {
        printWindow.print();
        printWindow.close();
    }, 300);
}
