<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Monthly Dispatch Report - {{ $client->name }} - {{ $month }}</title>
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
        .client-info {
            margin-bottom: 10px;
            font-size: 11px;
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
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            border-top: 1px solid #d1d5db;
            padding-top: 5px;
            font-size: 8px;
            color: #6b7280;
        }
        .summary-box {
            float: right;
            width: 300px;
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
        .page-break { page-break-after: always; }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-name">QUARRY BILL</div>
        <div class="report-title">MONTHLY DISPATCH REPORT</div>
        <div class="client-info">
            Client: <strong>{{ $client->name }}</strong> |
            Month: <strong>{{ $month }}</strong>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th width="4%">NO</th>
                <th width="12%">Pass No</th>
                <th width="10%">Date</th>
                <th width="8%">Time</th>
                <th>Material</th>
                <th width="15%">Vehicle Number</th>
                <th width="15%">Royalty No</th>
                <th width="12%" class="text-right">Net Weight (T)</th>
            </tr>
        </thead>
        <tbody>
            @php $totalWeight = 0; @endphp
            @forelse($receipts as $index => $receipt)
                @php $totalWeight += $receipt->net_weight; @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="font-bold">{{ $receipt->pass_number }}</td>
                    <td>{{ \Carbon\Carbon::parse($receipt->date)->format('d-m-Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($receipt->time)->format('h:i A') }}</td>
                    <td>{{ $receipt->materialType->name }}</td>
                    <td class="font-bold">{{ $receipt->vehicle_number }}</td>
                    <td>{{ $receipt->royalty_number ?: '-' }}</td>
                    <td class="text-right font-bold">{{ number_format($receipt->net_weight, 3) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center; padding: 20px;">No dispatches found for this client in selected month.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @if($receipts->count() > 0)
        <div class="summary-box">
            <div class="summary-row">
                <span class="summary-label">Total Dispatch Count:</span>
                <span class="summary-value">{{ $receipts->count() }}</span>
            </div>
            <div class="summary-row" style="border-top: 1px solid #111827; margin-top: 5px; padding-top: 5px;">
                <span class="summary-label">Grand Total Weight:</span>
                <span class="summary-value" style="font-size: 14px;">{{ number_format($totalWeight, 3) }} Tons</span>
            </div>
        </div>
    @endif

    <div class="footer">
        Generated on: {{ now()->format('M d, Y H:i:s') }} | QUARRY BILL Dispatch System | Page 1
    </div>
</body>
</html>
