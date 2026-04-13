<style>
    @page {
        margin: 25mm 15mm 20mm 15mm;
    }
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }
    body {
        font-family: DejaVu Sans, sans-serif;
        font-size: 11px;
        color: #333;
        line-height: 1.5;
        padding: 0 10px;
    }
    html[dir="rtl"] body {
        text-align: right;
    }
    html[dir="rtl"] th {
        text-align: right;
    }
    .header {
        text-align: center;
        border-bottom: 2px solid #2563eb;
        padding-bottom: 15px;
        margin-bottom: 20px;
    }
    .header h1 {
        font-size: 20px;
        color: #2563eb;
        margin-bottom: 4px;
    }
    .header .subtitle {
        font-size: 14px;
        color: #555;
    }
    .header .date {
        font-size: 10px;
        color: #888;
        margin-top: 4px;
    }
    .section-title {
        font-size: 14px;
        font-weight: bold;
        color: #1e40af;
        border-bottom: 1px solid #ddd;
        padding-bottom: 5px;
        margin-top: 20px;
        margin-bottom: 10px;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 15px;
    }
    th, td {
        border: 1px solid #ddd;
        padding: 6px 8px;
        font-size: 10px;
    }
    th {
        background-color: #f3f4f6;
        font-weight: bold;
        color: #374151;
    }
    tr:nth-child(even) {
        background-color: #f9fafb;
    }
    .stat-grid {
        display: table;
        width: 100%;
        margin-bottom: 15px;
    }
    .stat-card {
        display: table-cell;
        width: 33%;
        text-align: center;
        padding: 10px;
        border: 1px solid #ddd;
        background: #f9fafb;
    }
    .stat-value {
        font-size: 18px;
        font-weight: bold;
        color: #2563eb;
    }
    .stat-label {
        font-size: 10px;
        color: #666;
    }
    .page-break {
        page-break-before: always;
    }
    .footer {
        position: fixed;
        bottom: 0;
        width: 100%;
        text-align: center;
        font-size: 9px;
        color: #999;
        border-top: 1px solid #eee;
        padding-top: 5px;
    }
</style>
