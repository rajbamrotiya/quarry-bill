<?php

namespace App\Http\Controllers;

use App\Models\BuyReceipt;
use Barryvdh\DomPDF\Facade\Pdf;

class BuyReceiptPdfController extends Controller
{
    public function download(BuyReceipt $buy_receipt)
    {
        $buy_receipt->load(['supplier', 'materialType']);

        $pdf = Pdf::loadView('pdf.buy_receipt', compact('buy_receipt'));

        return $pdf->stream("buy_receipt-{$buy_receipt->id}.pdf");
    }
}
