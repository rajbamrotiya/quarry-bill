@props([
    'receipt' => null,
    'preview' => false,
    'clientName' => '',
    'vehicleNumber' => '',
    'materialName' => '',
    'date' => '',
    'time' => '',
    'gross' => 0,
    'tare' => 0,
    'net' => 0,
    'remarks' => '',
    'slipNumber' => '#0000000000',
    'copyType' => 'OFFICE COPY',
    'copyClass' => 'office'
])

@php
    $finalClientName = $receipt ? $receipt->client->name : $clientName;
    $finalVehicleNumber = $receipt ? $receipt->vehicle_number : $vehicleNumber;
    $finalMaterialName = $receipt ? $receipt->materialType->name : $materialName;
    $finalDate = $receipt ? $receipt->date->format('Y-m-d') : $date;
    $finalTime = $receipt ? $receipt->time : $time;
    $finalGross = $receipt ? $receipt->gross_weight : $gross;
    $finalTare = $receipt ? $receipt->tare_weight : $tare;
    $finalNet = $receipt ? $receipt->net_weight : $net;
    $finalRemarks = $receipt ? $receipt->remarks : $remarks;
    $finalSlipNumber = $receipt ? ($receipt->pass_number ?: '#' . str_pad($receipt->id, 10, '0', STR_PAD_LEFT)) : $slipNumber;
@endphp

<div class="receipt-box {{ $copyClass }} {{ $preview ? 'preview-mode' : '' }}">
    <table class="header-table">
        <tr>
            <td width="60%">
                <p class="company-name">QUARRY BILL</p>
                <p class="tagline">PROFESSIONAL WORK SLIP</p>
            </td>
            <td width="40%" align="right">
                <div class="copy-pill">{{ $copyType }}</div>
                <div class="slip-number-section">
                    <p class="slip-label">SLIP NUMBER</p>
                    <p class="slip-value">{{ $finalSlipNumber }}</p>
                </div>
            </td>
        </tr>
    </table>

    <table class="data-table">
        <tr>
            <td>
                <p class="field-label">CONSIGNEE / CLIENT</p>
                <p class="field-value">{{ $finalClientName ?: '---' }}</p>
            </td>
            <td>
                <p class="field-label">DATE OF ISSUE</p>
                <p class="field-value">{{ $finalDate ?: '---' }}</p>
            </td>
        </tr>
        <tr>
            <td>
                <p class="field-label">VEHICLE DETAILS</p>
                <p class="field-value">{{ $finalVehicleNumber ?: '---' }}</p>
            </td>
            <td>
                <p class="field-label">ENTRY TIME</p>
                <p class="field-value">{{ $finalTime ?: '---' }}</p>
            </td>
        </tr>
        <tr>
            <td>
                <p class="field-label">MATERIAL DESCRIPTION</p>
                <p class="field-value">{{ $finalMaterialName ?: '---' }}</p>
            </td>
            <td>
                <p class="field-label">REMARKS / NOTES</p>
                <p class="field-value">{{ $finalRemarks ?: '---' }}</p>
            </td>
        </tr>
    </table>

    <table class="weight-table">
        <tr>
            <td style="border-right: none;">
                <div class="weight-label">GROSS WEIGHT</div>
                <div class="weight-value">{{ number_format($finalGross) }} <span>kg</span></div>
            </td>
            <td>
                <div class="weight-label">TARE WEIGHT</div>
                <div class="weight-value">{{ number_format($finalTare) }} <span>kg</span></div>
            </td>
            <td class="net-weight-cell">
                <div class="weight-label">NET PRODUCT WEIGHT</div>
                <div class="weight-value">{{ number_format($finalNet) }} <span>kg</span></div>
            </td>
        </tr>
    </table>

    <table class="footer-table">
        <tr>
            <td width="33%">
                <span class="dot"></span> OFFICIAL COPY
            </td>
            <td width="33%" align="center" style="color: #6b7280;">
                DRIVER
            </td>
            <td width="33%" align="right">
                <strong>AUTHORITY</strong>
            </td>
        </tr>
    </table>
</div>
