<?php

namespace App\Http\Controllers;

use App\Models\Receipt;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class DailyReportController extends Controller
{
    public function generate(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
        ]);

        $date = $request->date;

        $receipts = Receipt::with(['client', 'materialType'])
            ->whereDate('date', $date)
            ->orderBy('time')
            ->get();

        $pdf = Pdf::loadView('pdf.daily-report', [
            'receipts' => $receipts,
            'date' => $date,
        ]);

        return $pdf->stream("daily-report-{$date}.pdf");
    }
}
