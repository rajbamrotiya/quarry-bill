<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Receipt;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DispatchReportController extends Controller
{
    public function daily(Request $request)
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

    public function monthly(Request $request)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'month' => 'required|string|regex:/^\d{4}-\d{2}$/',
        ]);

        $client = Client::findOrFail($request->client_id);
        [$year, $monthNum] = explode('-', $request->month);
        $monthName = Carbon::createFromDate($year, $monthNum)->format('F Y');

        $receipts = Receipt::with(['materialType'])
            ->where('client_id', $client->id)
            ->whereYear('date', $year)
            ->whereMonth('date', $monthNum)
            ->orderBy('date')
            ->orderBy('time')
            ->get();

        $pdf = Pdf::loadView('pdf.monthly-report', [
            'receipts' => $receipts,
            'client' => $client,
            'month' => $monthName,
        ]);

        $filename = "monthly-report-{$client->name}-{$request->month}.pdf";

        return $pdf->stream($filename);
    }

    public function clientMaterialSummary(Request $request)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'month' => 'required|string|regex:/^\d{4}-\d{2}$/',
        ]);

        $client = Client::findOrFail($request->client_id);
        [$year, $monthNum] = explode('-', $request->month);
        $monthName = Carbon::createFromDate($year, $monthNum)->format('F Y');

        $receipts = Receipt::with('materialType')
            ->where('client_id', $client->id)
            ->whereYear('date', $year)
            ->whereMonth('date', $monthNum)
            ->get();

        $materials = $receipts->groupBy('material_type_id')->map(function ($group) {
            return [
                'material_name' => $group->first()->materialType->name,
                'count' => $group->count(),
                'total_weight' => $group->sum('net_weight'),
                'total_payment' => $group->sum('payment_value'),
            ];
        })->values();

        $pdf = Pdf::loadView('pdf.client-material-summary', [
            'materials' => $materials,
            'client' => $client,
            'month' => $monthName,
        ]);

        $filename = "material-summary-{$client->name}-{$request->month}.pdf";

        return $pdf->stream($filename);
    }
}
