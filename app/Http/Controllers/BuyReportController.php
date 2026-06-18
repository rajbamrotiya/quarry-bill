<?php

namespace App\Http\Controllers;

use App\Models\BuyReceipt;
use App\Models\Supplier;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BuyReportController extends Controller
{
    public function daily(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
        ]);

        $date = $request->date;

        $buy_receipts = BuyReceipt::with(['supplier', 'materialType'])
            ->whereDate('date', $date)
            ->orderBy('time')
            ->get();

        $pdf = Pdf::loadView('pdf.buy-daily-report', [
            'buy_receipts' => $buy_receipts,
            'date' => $date,
        ]);

        return $pdf->stream("buy-daily-report-{$date}.pdf");
    }

    public function monthly(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'month' => 'required|string|regex:/^\d{4}-\d{2}$/',
        ]);

        $supplier = Supplier::findOrFail($request->supplier_id);
        [$year, $monthNum] = explode('-', $request->month);
        $monthName = Carbon::createFromDate($year, $monthNum)->format('F Y');

        $buy_receipts = BuyReceipt::with(['materialType'])
            ->where('supplier_id', $supplier->id)
            ->whereYear('date', $year)
            ->whereMonth('date', $monthNum)
            ->orderBy('date')
            ->orderBy('time')
            ->get();

        $pdf = Pdf::loadView('pdf.buy-monthly-report', [
            'buy_receipts' => $buy_receipts,
            'supplier' => $supplier,
            'month' => $monthName,
        ]);

        $filename = "buy-monthly-report-{$supplier->name}-{$request->month}.pdf";

        return $pdf->stream($filename);
    }

    public function supplierMaterialSummary(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'month' => 'required|string|regex:/^\d{4}-\d{2}$/',
        ]);

        $supplier = Supplier::findOrFail($request->supplier_id);
        [$year, $monthNum] = explode('-', $request->month);
        $monthName = Carbon::createFromDate($year, $monthNum)->format('F Y');

        $buy_receipts = BuyReceipt::with('materialType')
            ->where('supplier_id', $supplier->id)
            ->whereYear('date', $year)
            ->whereMonth('date', $monthNum)
            ->get();

        $materials = $buy_receipts->groupBy('material_type_id')->map(function ($group) {
            return [
                'material_name' => $group->first()->materialType->name,
                'count' => $group->count(),
                'total_weight' => $group->sum('net_weight'),

            ];
        })->values();

        $pdf = Pdf::loadView('pdf.supplier-material-summary', [
            'materials' => $materials,
            'supplier' => $supplier,
            'month' => $monthName,
        ]);

        $filename = "buy-material-summary-{$supplier->name}-{$request->month}.pdf";

        return $pdf->stream($filename);
    }

    public function monthlyVehicleSummary(Request $request)
    {
        $request->validate([
            'month' => 'required|string|regex:/^\d{4}-\d{2}$/',
        ]);

        [$year, $monthNum] = explode('-', $request->month);
        $monthName = Carbon::createFromDate($year, $monthNum)->format('F Y');

        $buy_receipts = BuyReceipt::whereYear('date', $year)
            ->whereMonth('date', $monthNum)
            ->get();

        $vehicles = $buy_receipts->groupBy('vehicle_number')->map(function ($group, $vehicleNumber) {
            return [
                'vehicle_number' => $vehicleNumber ?: 'Unknown',
                'count' => $group->count(),
                'total_weight' => $group->sum('net_weight'),
            ];
        })->values();

        $pdf = Pdf::loadView('pdf.buy-vehicle-summary', [
            'vehicles' => $vehicles,
            'month' => $monthName,
        ]);

        $filename = "buy-vehicle-summary-{$request->month}.pdf";

        return $pdf->stream($filename);
    }
}
