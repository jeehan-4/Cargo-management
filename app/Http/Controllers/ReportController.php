<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use App\Models\Destination;
use App\Models\Shipment;
use App\Models\Setting;
use App\Services\ReportService;
use App\Support\XlsxWriter;
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
       EXPORT EXCEL — RTL .xlsx (replaces mirrored PDF)
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
        $filename = 'shipments_report_' . ($periodType === 'all' ? 'all_' . date('Y-m-d') : $from->format('Y-m-d')) . '.xlsx';

        return $this->buildWorkbook($report)->download($filename);
    }

    /**
     * Assemble the multi-sheet RTL workbook from report data.
     */
    protected function buildWorkbook(array $report): XlsxWriter
    {
        $summary     = $report['summary'];
        $companyName = Setting::get('company_name');
        $xlsx        = new XlsxWriter();

        /* ─── Sheet 1: Summary ─── */
        $summaryRows = [
            [['v' => $companyName . ' — تقرير الشحنات', 's' => 'title']],
            [['v' => 'الفترة: ' . $summary['period_label'], 's' => 'plain']],
            [['v' => 'تاريخ الإصدار: ' . $summary['generated_at']->format('Y-m-d H:i'), 's' => 'plain']],
            [],
            [['v' => 'البند', 's' => 'header'], ['v' => 'القيمة', 's' => 'header']],
        ];
        $stats = [
            'الشحنات المسجلة'        => $summary['registered'],
            'الشحنات المسندة'        => $summary['assigned'],
            'بدأت النقل'             => $summary['started'],
            'تم التسليم'             => $summary['delivered'],
            'متعذر التسليم'          => $summary['failed'],
            'قيد التنفيذ نهاية الفترة' => $summary['in_progress'],
            'إجمالي الشحنات المتأثرة' => $summary['total_touched'],
            'عدد سجلات التغيير'      => $summary['log_count'],
        ];
        foreach ($stats as $label => $value) {
            $summaryRows[] = [['v' => $label, 's' => 'label'], ['v' => $value, 's' => 'cell']];
        }
        if ($footer = Setting::get('report_footer')) {
            $summaryRows[] = [];
            $summaryRows[] = [['v' => $footer, 's' => 'plain']];
        }
        $xlsx->addSheet('الملخص', $summaryRows, [34, 22]);

        /* ─── Sheet 2: Shipments ─── */
        $shipmentRows = [[
            ['v' => 'رقم الشحنة', 's' => 'header'], ['v' => 'نوع البضاعة', 's' => 'header'],
            ['v' => 'الكمية', 's' => 'header'], ['v' => 'الوزن', 's' => 'header'],
            ['v' => 'ميناء المنشأ', 's' => 'header'], ['v' => 'الوجهة', 's' => 'header'],
            ['v' => 'السائق', 's' => 'header'], ['v' => 'الحالة', 's' => 'header'],
            ['v' => 'تاريخ الإنشاء', 's' => 'header'],
        ]];
        foreach ($report['allTouched'] as $s) {
            $shipmentRows[] = [
                ['v' => $s->shipment_number, 's' => 'cell'],
                ['v' => $s->cargo_type, 's' => 'cell'],
                ['v' => $s->quantity ? $s->quantity . ' ' . $s->unit : '—', 's' => 'cell'],
                ['v' => $s->weight ? $s->weight . ' طن' : '—', 's' => 'cell'],
                ['v' => $s->origin_port, 's' => 'cell'],
                ['v' => $s->destination?->name ?? '—', 's' => 'cell'],
                ['v' => $s->driver?->name ?? 'غير معين', 's' => 'cell'],
                ['v' => Shipment::statusLabel($s->status), 's' => 'cell'],
                ['v' => $s->created_at ? $s->created_at->format('Y-m-d') : '—', 's' => 'cell'],
            ];
        }
        $xlsx->addSheet('الشحنات', $shipmentRows, [20, 16, 14, 12, 20, 20, 20, 18, 16], true);

        /* ─── Sheet 3: Driver summary ─── */
        $driverRows = [[
            ['v' => 'السائق', 's' => 'header'], ['v' => 'إجمالي الشحنات', 's' => 'header'],
            ['v' => 'تم التسليم', 's' => 'header'], ['v' => 'متعذر التسليم', 's' => 'header'],
            ['v' => 'نشطة', 's' => 'header'],
        ]];
        foreach ($report['driverSummary'] as $ds) {
            $driverRows[] = [
                ['v' => $ds['driver']?->name ?? '—', 's' => 'cell'],
                ['v' => $ds['total'], 's' => 'cell'],
                ['v' => $ds['delivered'], 's' => 'cell'],
                ['v' => $ds['failed'], 's' => 'cell'],
                ['v' => $ds['active'], 's' => 'cell'],
            ];
        }
        $xlsx->addSheet('ملخص السائقين', $driverRows, [24, 18, 16, 18, 12], true);

        /* ─── Sheet 4: Status change logs ─── */
        $logRows = [[
            ['v' => 'رقم الشحنة', 's' => 'header'], ['v' => 'الحالة السابقة', 's' => 'header'],
            ['v' => 'الحالة الجديدة', 's' => 'header'], ['v' => 'المستخدم', 's' => 'header'],
            ['v' => 'ملاحظات', 's' => 'header'], ['v' => 'التاريخ', 's' => 'header'],
        ]];
        foreach ($report['logs'] as $log) {
            $logRows[] = [
                ['v' => $log->shipment?->shipment_number ?? '—', 's' => 'cell'],
                ['v' => $log->old_status_label, 's' => 'cell'],
                ['v' => $log->new_status_label, 's' => 'cell'],
                ['v' => $log->user?->name ?? '—', 's' => 'cell'],
                ['v' => $log->notes ?? '—', 's' => 'cell'],
                ['v' => $log->created_at ? $log->created_at->format('Y-m-d H:i') : '—', 's' => 'cell'],
            ];
        }
        $xlsx->addSheet('سجل التغييرات', $logRows, [20, 18, 18, 20, 30, 20], true);

        return $xlsx;
    }
}
