<?php

namespace App\Http\Controllers;

use App\Models\Receipt;
use Barryvdh\DomPDF\Facade\Pdf;

class ReceiptPdfController extends Controller
{
    public function download(Receipt $receipt)
    {
        $receipt->load(['client', 'materialType']);

        $pdf = Pdf::loadView('pdf.receipt', compact('receipt'));

        return $pdf->stream("receipt-{$receipt->id}.pdf");
    }
}
