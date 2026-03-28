<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $report['title'] }}</title>
    <style>
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 12px;
            color: #14233d;
            margin: 24px;
        }
        .report-shell {
            display: block;
        }
        .report-head {
            margin-bottom: 18px;
        }
        .report-kicker {
            display: inline-block;
            margin-bottom: 8px;
            padding: 4px 8px;
            border: 1px solid #cad8eb;
            border-radius: 999px;
            background: #eef5ff;
            color: #24579f;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        h1 {
            margin: 0 0 6px;
            font-size: 24px;
            line-height: 1.2;
        }
        .report-copy {
            margin: 0;
            color: #51647f;
            line-height: 1.6;
        }
        .report-meta {
            margin-top: 8px;
            font-size: 11px;
            color: #6a7990;
        }
        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin: 0 0 18px;
        }
        .summary-table td {
            width: 33.33%;
            border: 1px solid #d7e1ef;
            padding: 10px 12px;
            vertical-align: top;
            background: #f8fbff;
        }
        .summary-label {
            display: block;
            color: #63708a;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }
        .summary-value {
            display: block;
            margin-top: 6px;
            font-size: 18px;
            font-weight: 700;
            color: #102849;
        }
        .report-table {
            width: 100%;
            border-collapse: collapse;
        }
        .report-table th,
        .report-table td {
            border: 1px solid #d7e1ef;
            padding: 8px 10px;
            text-align: left;
            vertical-align: top;
        }
        .report-table th {
            background: #edf4ff;
            color: #214a86;
            font-size: 11px;
            font-weight: 700;
        }
        .report-table td {
            font-size: 11px;
            color: #21344f;
        }
        .empty-state {
            margin-top: 14px;
            padding: 14px;
            border: 1px dashed #ccd8ea;
            background: #f9fbff;
            color: #5c6b84;
        }
    </style>
</head>
<body>
    <div class="report-shell">
        <div class="report-head">
            <span class="report-kicker">Manager / HR Export</span>
            <h1>{{ $report['title'] }}</h1>
            <p class="report-copy">{{ $report['subtitle'] }}</p>
            <div class="report-meta">Generated {{ $report['generated_at'] }}</div>
        </div>

        @if (!empty($report['summary']))
            <table class="summary-table">
                <tr>
                    @foreach ($report['summary'] as $item)
                        <td>
                            <span class="summary-label">{{ $item['label'] }}</span>
                            <span class="summary-value">{{ $item['value'] }}</span>
                        </td>
                    @endforeach
                </tr>
            </table>
        @endif

        @if (!empty($report['rows']))
            <table class="report-table">
                <thead>
                    <tr>
                        @foreach ($report['columns'] as $column)
                            <th>{{ $column['label'] }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($report['rows'] as $row)
                        <tr>
                            @foreach ($report['columns'] as $column)
                                <td>{{ $row[$column['key']] ?? '' }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="empty-state">No rows are available for this report yet.</div>
        @endif
    </div>
</body>
</html>
