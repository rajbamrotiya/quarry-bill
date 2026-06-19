<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Daily Vehicle Summary - {{ \Carbon\Carbon::parse($date)->format('M d, Y') }}</title>
    <style>
        @page {
            margin: 1cm;
            size: A4 portrait;
        }
        body {
            font-family: 'Helvetica', Arial, sans-serif;
            font-size: 10px;
            color: #111827;
            margin: 0;
            padding: 0;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #111827;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .company-name {
            font-size: 20px;
            font-weight: 900;
            margin: 0;
            text-transform: uppercase;
        }
        .report-title {
            font-size: 14px;
            font-weight: bold;
            margin: 5px 0;
            color: #4b5563;
        }
        .supplier-info {
            text-align: center;
            font-size: 12px;
            margin-bottom: 15px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th {
            background-color: #f3f4f6;
            text-align: left;
            padding: 10px 8px;
            border: 1px solid #d1d5db;
            text-transform: uppercase;
            font-size: 9px;
            font-weight: bold;
        }
        td {
            padding: 10px 8px;
            border: 1px solid #d1d5db;
            vertical-align: middle;
            font-size: 11px;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .footer {
            margin-top: 30px;
            border-top: 1px solid #d1d5db;
            padding-top: 10px;
            font-size: 8px;
            color: #6b7280;
        }
        .summary-box {
            float: right;
            width: 250px;
            border: 2px solid #111827;
            padding: 10px;
            background: #f9fafb;
        }
        .summary-row {
            overflow: hidden;
            margin-bottom: 5px;
        }
        .summary-label { float: left; font-weight: bold; }
        .summary-value { float: right; font-weight: 900; }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-name">QUARRY BILL</div>
        <div class="report-title">DAILY VEHICLE SUMMARY</div>
        <div>Date: <strong>{{ \Carbon\Carbon::parse($date)->format('M d, Y') }}</strong></div>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%" class="text-center">NO</th>
                <th>Vehicle Number</th>
                <th width="15%" class="text-center">Total Slips</th>
                <th width="20%" class="text-right">Net Weight (KG)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($vehicles as $index => $vehicle)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="font-bold">{{ $vehicle['vehicle_number'] }}</td>
                    <td class="text-center">{{ $vehicle['count'] }}</td>
                    <td class="text-right font-bold">{{ number_format($vehicle['total_weight']) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="text-align: center; padding: 20px;">No buys found for this date.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @if($vehicles->count() > 0)
        <div class="summary-box">
            <div class="summary-row">
                <span class="summary-label">Total Slips:</span>
                <span class="summary-value">{{ $vehicles->sum('count') }}</span>
            </div>

            <div class="summary-row" style="border-top: 1px solid #111827; margin-top: 5px; padding-top: 5px;">
                <span class="summary-label">Total Weight:</span>
                <span class="summary-value">{{ number_format($vehicles->sum('total_weight')) }} KG</span>
            </div>
        </div>
    @endif

    <div class="footer">
        Generated on: {{ now()->format('M d, Y H:i:s') }} | QUARRY BILL Buy System
    </div>
</body>
</html>
