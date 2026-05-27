<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Daily Dispatch Report - {{ $date }}</title>
    <style>
        @page {
            margin: 1cm;
            size: A4 landscape;
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
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th {
            background-color: #f3f4f6;
            text-align: left;
            padding: 8px;
            border: 1px solid #d1d5db;
            text-transform: uppercase;
            font-size: 9px;
            font-weight: bold;
        }
        td {
            padding: 8px;
            border: 1px solid #d1d5db;
            vertical-align: middle;
        }
        .text-right { text-align: right; }
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
        <div class="report-title">DAILY DISPATCH REPORT</div>
        <div>Date: <strong>{{ \Carbon\Carbon::parse($date)->format('M d, Y') }}</strong></div>
    </div>

    <table>
        <thead>
            <tr>
                <th width="4%">NO</th>
                <th>Client Name</th>
                <th>Material</th>
                <th>Vehicle Number</th>
                <th>Royalty No</th>
                <th width="10%">Payment Type</th>
                <th width="12%" class="text-right">Payment Value</th>
                <th width="10%" class="text-right">Weight (Tons)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($receipts as $index => $receipt)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td class="font-bold">{{ $receipt->client->name }}</td>
                    <td>{{ $receipt->materialType->name }}</td>
                    <td class="font-bold">{{ $receipt->vehicle_number }}</td>
                    <td>{{ $receipt->royalty_number ?: '-' }}</td>
                    <td>{{ ucfirst($receipt->payment_type) ?: '-' }}</td>
                    <td class="text-right">
                        @if($receipt->payment_value)
                            {{ number_format($receipt->payment_value, 2) }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-right font-bold">{{ number_format($receipt->net_weight, 3) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center; padding: 20px;">No dispatches found for this date.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @if($receipts->count() > 0)
        <div class="summary-box">
            <div class="summary-row">
                <span class="summary-label">Total Slips:</span>
                <span class="summary-value">{{ $receipts->count() }}</span>
            </div>
            <div class="summary-row">
                <span class="summary-label">Total Payment:</span>
                <span class="summary-value">{{ number_format($receipts->sum('payment_value'), 2) }}</span>
            </div>
            <div class="summary-row" style="border-top: 1px solid #111827; margin-top: 5px; padding-top: 5px;">
                <span class="summary-label">Total Weight:</span>
                <span class="summary-value">{{ number_format($receipts->sum('net_weight'), 3) }} Tons</span>
            </div>
        </div>
    @endif

    <div class="footer">
        Generated on: {{ now()->format('M d, Y H:i:s') }} | QUARRY BILL Dispatch System
    </div>
</body>
</html>
