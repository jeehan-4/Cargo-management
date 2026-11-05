<?php

namespace App\Services;

use App\Models\Driver;
use App\Models\Shipment;
use App\Models\ShipmentLog;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ReportService
{
    /**
     * Build a full report data array for the given date range.
     */
    public function build(Carbon $from, Carbon $to, string $periodType = 'all'): array
    {
        $fromStart = $from->copy()->startOfDay();
        $toEnd     = $to->copy()->endOfDay();
        $isAll     = ($periodType === 'all');

        /* ─── 1. Shipments registered in period ─── */
        $registeredQuery = Shipment::with(['destination', 'driver', 'driver.truck', 'logs.user'])
            ->orderBy('created_at', 'desc');
        if (! $isAll) {
            $registeredQuery->whereBetween('created_at', [$fromStart, $toEnd]);
        }
        $registered = $registeredQuery->get();

        /* ─── 2. Shipments whose status changed inside period (via logs) ─── */
        if (! $isAll) {
            $changedIds = ShipmentLog::whereBetween('created_at', [$fromStart, $toEnd])
                ->distinct()
                ->pluck('shipment_id');

            $changedShipments = Shipment::with(['destination', 'driver', 'driver.truck', 'logs'])
                ->whereIn('id', $changedIds)
                ->whereNotIn('id', $registered->pluck('id'))   // avoid duplicates
                ->orderBy('created_at', 'desc')
                ->get();

            // All shipments touched in this period (registered OR changed)
            $allTouched = $registered->concat($changedShipments)->sortByDesc('created_at');
        } else {
            $allTouched = $registered;
        }

        /* ─── 3. Shipments assigned in period ─── */
        $assignedQuery = Shipment::whereNotNull('driver_id');
        if (! $isAll) {
            $assignedQuery->whereBetween('assigned_at', [$fromStart, $toEnd]);
        }
        $assignedCount = $assignedQuery->count();

        /* ─── 4. Shipments that started transit in period ─── */
        $startedQuery = Shipment::whereNotNull('started_at');
        if (! $isAll) {
            $startedQuery->whereBetween('started_at', [$fromStart, $toEnd]);
        }
        $startedCount = $startedQuery->count();

        /* ─── 5. Shipments delivered in period ─── */
        $deliveredQuery = Shipment::where('status', 'delivered');
        if (! $isAll) {
            $deliveredQuery->whereBetween('delivered_at', [$fromStart, $toEnd]);
        }
        $deliveredInPeriod = $deliveredQuery->get();

        /* ─── 6. Shipments failed in period ─── */
        $failedQuery = Shipment::where('status', 'failed');
        if (! $isAll) {
            $failedQuery->whereBetween('updated_at', [$fromStart, $toEnd]);
        }
        $failedCount = $failedQuery->count();

        /* ─── 7. Shipments still in-progress at end of period ─── */
        $inProgressQuery = Shipment::whereIn('status', ['pending', 'assigned', 'in_transit']);
        if (! $isAll) {
            $inProgressQuery->where('created_at', '<=', $toEnd);
        }
        $inProgressAtEnd = $inProgressQuery->count();

        /* ─── 8. Logs in period ─── */
        $logsQuery = ShipmentLog::with(['shipment', 'user'])->orderBy('created_at', 'desc');
        if (! $isAll) {
            $logsQuery->whereBetween('created_at', [$fromStart, $toEnd]);
        }
        $logs = $logsQuery->get();

        /* ─── 9. Driver summary ─── */
        $driverSummary = $this->buildDriverSummary($allTouched, $fromStart, $toEnd);

        /* ─── 10. Summary stats ─── */
        $summary = [
            'period_label'    => $isAll ? 'جميع الفترات' : $this->periodLabel($from, $to),
            'period_type'     => $periodType,
            'from'            => $fromStart,
            'to'              => $toEnd,
            'generated_at'    => now(),
            'registered'      => $registered->count(),
            'assigned'        => $assignedCount,
            'started'         => $startedCount,
            'delivered'       => $deliveredInPeriod->count(),
            'failed'          => $failedCount,
            'in_progress'     => $inProgressAtEnd,
            'total_touched'   => $allTouched->count(),
            'log_count'       => $logs->count(),
        ];

        return [
            'summary'          => $summary,
            'registered'       => $registered,
            'allTouched'       => $allTouched,
            'deliveredInPeriod'=> $deliveredInPeriod,
            'logs'             => $logs,
            'driverSummary'    => $driverSummary,
        ];
    }

    /**
     * Build per-driver statistics from the touched shipments.
     */
    protected function buildDriverSummary(Collection $shipments, Carbon $from, Carbon $to): Collection
    {
        return $shipments
            ->filter(fn($s) => $s->driver_id !== null)
            ->groupBy('driver_id')
            ->map(function ($group) {
                $driver    = $group->first()->driver;
                $total     = $group->count();
                $delivered = $group->filter(fn($s) => $s->status === 'delivered')->count();
                $failed    = $group->filter(fn($s) => $s->status === 'failed')->count();
                $active    = $group->filter(fn($s) => in_array($s->status, ['assigned', 'in_transit']))->count();

                return [
                    'driver'    => $driver,
                    'total'     => $total,
                    'delivered' => $delivered,
                    'failed'    => $failed,
                    'active'    => $active,
                ];
            })
            ->values()
            ->sortByDesc('total');
    }

    /**
     * Human-readable period label.
     */
    protected function periodLabel(Carbon $from, Carbon $to): string
    {
        if ($from->isSameDay($to)) {
            return $from->isSameDay(today()) ? 'اليوم (' . $from->format('Y/m/d') . ')' : $from->format('Y/m/d');
        }
        return $from->format('Y/m/d') . ' — ' . $to->format('Y/m/d');
    }

    /**
     * Parse period type + optional dates into Carbon from/to.
     */
    public static function parsePeriod(string $type, ?string $from = null, ?string $to = null): array
    {
        return match ($type) {
            'today'  => [today(), today()],
            'week'   => [today()->startOfWeek(), today()->endOfWeek()],
            'month'  => [today()->startOfMonth(), today()->endOfMonth()],
            'year'   => [today()->startOfYear(), today()->endOfYear()],
            'custom' => [
                $from ? Carbon::parse($from) : today()->subMonth(),
                $to   ? Carbon::parse($to)   : today(),
            ],
            'all'    => [Carbon::parse('2020-01-01'), today()->endOfDay()],
            default  => [Carbon::parse('2020-01-01'), today()->endOfDay()],
        };
    }
}
