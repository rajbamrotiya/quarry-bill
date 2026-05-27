<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Receipt #{{ $receipt->id }}</title>
    <style>
        @page {
            margin: 0;
        }
        body {
            font-family: 'Helvetica', Arial, sans-serif;
            font-size: 8px;
            color: #111827;
            margin: 0;
            padding: 15px 30px;
            line-height: 1.1;
        }
        .receipt-container {
            width: 100%;
            height: 265px;
            margin-bottom: 2px;
            position: relative;
            margin-top: 10px;
        }
        .receipt-box {
            border: 1.5px solid #ccc;
            padding: 10px 15px;
            height: 100%;
            box-sizing: border-box;
        }
        .office { border-color: #1d4ed8; }
        .client { border-color: #047857; }
        .transport { border-color: #b45309; }

        table {
            width: 100%;
            border-collapse: collapse;
        }
        .header-table td {
            vertical-align: middle;
        }
        .company-name {
            font-size: 16px;
            font-weight: 900;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: -0.5px;
        }
        .tagline {
            font-size: 7px;
            color: #4b5563;
            margin: 0;
            font-weight: normal;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .copy-pill {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 10px;
            color: #fff;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 8px;
        }
        .office .copy-pill { background: #1d4ed8; }
        .client .copy-pill { background: #047857; }
        .transport .copy-pill { background: #b45309; }

        .slip-number-section {
            margin-top: 4px;
        }
        .slip-label { color: #6b7280; font-size: 6px; text-transform: uppercase; margin: 0; font-weight: bold; }
        .slip-value { font-size: 11px; font-weight: 900; margin: 0; color: #000; }

        .data-table {
            border-top: 1px solid #f3f4f6;
            margin-top: 6px;
        }
        .data-table td {
            padding: 6px 0;
            width: 50%;
        }
        .field-label { font-size: 7px; color: #9ca3af; text-transform: uppercase; margin-bottom: 1px; font-weight: 600; }
        .field-value { font-size: 10px; font-weight: 800; text-transform: uppercase; margin: 0; color: #111827; }

        .weight-table {
            margin-top: 8px;
            width: 100%;
        }
        .weight-table td {
            border: 1px solid #111827;
            width: 33.33%;
            padding: 10px 5px;
            text-align: center;
        }
        .weight-label { font-size: 7px; color: #4b5563; text-transform: uppercase; font-weight: bold; margin-bottom: 4px; }
        .weight-value { font-size: 15px; font-weight: 900; margin: 0; }
        .weight-value span { font-size: 9px; font-weight: normal; margin-left: 2px; }

        .net-weight-cell { color: #fff; }
        .office .net-weight-cell { background: #1d4ed8; }
        .client .net-weight-cell { background: #047857; }
        .transport .net-weight-cell { background: #b45309; }
        .net-weight-cell .weight-label { color: #fff; opacity: 0.8; }

        .footer-table {
            margin-top: 10px;
            border-top: 1px solid #111827;
        }
        .footer-table td {
            padding-top: 6px;
            font-size: 7px;
            text-transform: uppercase;
            color: #111827;
        }
        .dot {
            display: inline-block;
            width: 5px;
            height: 5px;
            border-radius: 50%;
            margin-right: 4px;
            vertical-align: middle;
        }
        .office .dot { background: #1d4ed8; }
        .client .dot { background: #047857; }
        .transport .dot { background: #b45309; }

        .perforation {
            text-align: center;
            color: #e5e7eb;
            font-size: 6px;
            height: 8px;
            line-height: 8px;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    @foreach([
        ['class' => 'office', 'label' => 'OFFICE COPY'],
        ['class' => 'client', 'label' => 'CLIENT COPY'],
        ['class' => 'transport', 'label' => 'TRANSPORT COPY']
    ] as $copy)
        <div class="receipt-container">
            <x-receipt-slip
                :receipt="$receipt"
                :copyType="$copy['label']"
                :copyClass="$copy['class']"
            />
        </div>

        @if(!$loop->last)
            <div class="perforation">
                &nbsp;
            </div>
        @endif
    @endforeach
</body>
</html>
