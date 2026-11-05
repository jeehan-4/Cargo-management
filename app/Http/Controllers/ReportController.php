<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use App\Models\Destination;
use App\Models\Shipment;
use App\Services\ReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function __construct(protected ReportService $reportService) {}

    /* ─────────────────────────────────────────
       INDEX — Main reports page
    ───────────────────────────────────────── */
    public function index(Request $request)
    {
        // Period selector (defaults to 'all' to show all registered data)
        $periodType = $request->input('period', 'all');
        [$from, $to] = ReportService::parsePeriod(
            $periodType,
            $request->input('from'),
            $request->input('to')
        );

        // Build report data
        $report = $this->reportService->build($from, $to, $periodType);

        // Filters for the table view
        $query = Shipment::with(['destination', 'driver'])->orderBy('created_at', 'desc');
        if ($request->filled('status'))         { $query->where('status', $request->status); }
        if ($request->filled('driver_id'))      { $query->where('driver_id', $request->driver_id); }
        if ($request->filled('destination_id')) { $query->where('destination_id', $request->destination_id); }
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('shipment_number', 'like', "%{$request->search}%")
                  ->orWhere('cargo_type', 'like', "%{$request->search}%");
            });
        }

        if ($periodType !== 'all') {
            $query->whereDate('created_at', '>=', $from->toDateString())
                  ->whereDate('created_at', '<=', $to->toDateString());
        }

        $shipments    = $query->paginate(20)->withQueryString();
        $drivers      = Driver::where('status', '!=', 'inactive')->orderBy('name')->get();
        $destinations = Destination::where('is_active', true)->orderBy('name')->get();

        return view('reports.index', compact(
            'report', 'shipments', 'drivers', 'destinations',
            'periodType', 'from', 'to'
        ));
    }

    /* ─────────────────────────────────────────
       EXPORT PDF — Server-side dompdf
    ───────────────────────────────────────── */
    public function export(Request $request)
    {
        $periodType = $request->input('period', 'all');
        [$from, $to] = ReportService::parsePeriod(
            $periodType,
            $request->input('from'),
            $request->input('to')
        );

        $report   = $this->reportService->build($from, $to, $periodType);
        $filename = 'shipments_report_' . ($periodType === 'all' ? 'all_' . date('Y-m-d') : $from->format('Y-m-d')) . '.pdf';

        $pdf = Pdf::loadView('reports.pdf', compact('report'))
            ->setPaper('a4', 'portrait')
            ->setOption('defaultFont', 'dejavusans')
            ->setOption('isHtml5ParserEnabled', true)
            ->setOption('isRemoteEnabled', false);

        return $pdf->download($filename);
    }

    /* ─────────────────────────────────────────
       PREVIEW PDF in browser (for testing)
    ───────────────────────────────────────── */
    public function preview(Request $request)
    {
        $periodType = $request->input('period', 'all');
        [$from, $to] = ReportService::parsePeriod(
            $periodType,
            $request->input('from'),
            $request->input('to')
        );

        $report = $this->reportService->build($from, $to, $periodType);

        $pdf = Pdf::loadView('reports.pdf', compact('report'))
            ->setPaper('a4', 'portrait')
            ->setOption('defaultFont', 'dejavusans')
            ->setOption('isHtml5ParserEnabled', true)
            ->setOption('isRemoteEnabled', false);

        return $pdf->stream('preview.pdf');
    }
}
