<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Monthly Vehicle Report - {{ $vehicle_number }} - {{ $month }}</title>
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
        .supplier-info {
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
        <div class="report-title">MONTHLY VEHICLE REPORT</div>
        <div class="supplier-info">
            Vehicle No: <strong>{{ $vehicle_number }}</strong> |
            Month: <strong>{{ $month }}</strong>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th width="4%">NO.</th>
                <th>PASS NO.</th>
                <th>DATE</th>
                <th>TIME</th>
                <th>MATERIAL</th>
                <th width="20%">Supplier</th>
                <th width="10%">ROYALTY NO</th>
                <th width="12%" class="text-right">NET WEIGHT</th>
            </tr>
        </thead>
        <tbody>
            @php $totalWeight = 0; @endphp
            @forelse($buy_receipts as $index => $buy_receipt)
                @php $totalWeight += $buy_receipt->net_weight; @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="font-bold">{{ $buy_receipt->pass_number }}</td>
                    <td>{{ \Carbon\Carbon::parse($buy_receipt->date)->format('d-m-Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($buy_receipt->time)->format('h:i A') }}</td>
                    <td>{{ $buy_receipt->materialType->name }}</td>
                    <td class="font-bold">{{ $buy_receipt->supplier->name }}</td>
                    <td>{{ $buy_receipt->royalty_number ?: '-' }}</td>
                    <td class="text-right font-bold">{{ number_format($buy_receipt->net_weight) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center; padding: 20px;">No buys found for this vehicle in selected month.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @if($buy_receipts->count() > 0)
        <div class="summary-box">
            <div class="summary-row">
                <span class="summary-label">Total Buy Count:</span>
                <span class="summary-value">{{ $buy_receipts->count() }}</span>
            </div>
            <div class="summary-row" style="border-top: 1px solid #111827; margin-top: 5px; padding-top: 5px;">
                <span class="summary-label">Grand Total Weight:</span>
                <span class="summary-value" style="font-size: 14px;">{{ number_format($totalWeight) }} KG</span>
            </div>
        </div>
    @endif

    <div class="footer">
        Generated on: {{ now()->format('M d, Y H:i:s') }} | QUARRY BILL Buy System | Page 1
    </div>
</body>
</html>
