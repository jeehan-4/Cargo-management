<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use App\Models\Shipment;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        /* ─── Global shipment stats ─── */
        $stats = [
            'total'      => Shipment::count(),
            'pending'    => Shipment::where('status', 'pending')->count(),
            'assigned'   => Shipment::where('status', 'assigned')->count(),
            'in_transit' => Shipment::where('status', 'in_transit')->count(),
            'delivered'  => Shipment::where('status', 'delivered')->count(),
            'failed'     => Shipment::where('status', 'failed')->count(),
        ];

        /* ─── Driver stats ─── */
        $driverStats = [
            'total'     => Driver::where('status', '!=', 'inactive')->count(),
            'available' => Driver::where('status', 'available')->count(),
            'busy'      => Driver::where('status', 'busy')->count(),
        ];

        /* ─── Today's activity ─── */
        $today = [
            'registered'  => Shipment::whereDate('created_at', today())->count(),
            'assigned'    => Shipment::whereDate('assigned_at', today())->count(),
            'in_transit'  => Shipment::whereDate('started_at', today())->count(),
            'delivered'   => Shipment::whereDate('delivered_at', today())->count(),
        ];

        /* ─── Recent shipments ─── */
        $recentShipments = Shipment::with(['destination', 'driver'])
            ->orderBy('created_at', 'desc')
            ->limit(8)
            ->get();

        /* ─── Shipments that need action (pending + failed) ─── */
        $needsAction = Shipment::with(['destination', 'driver'])
            ->whereIn('status', ['pending', 'failed'])
            ->orderBy('created_at', 'asc')
            ->get();

        /* ─── Recent drivers (for sidebar widget) ─── */
        $recentDrivers = Driver::where('status', '!=', 'inactive')
            ->orderByRaw("CASE WHEN status = 'available' THEN 1 WHEN status = 'busy' THEN 2 ELSE 3 END")
            ->limit(5)
            ->get();

        /* ─── Chart: shipments by month (last 6 months) ─── */
        $chartData = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $chartData[] = [
                'label' => $month->translatedFormat('M Y'),
                'count' => Shipment::whereYear('created_at', $month->year)
                    ->whereMonth('created_at', $month->month)
                    ->count(),
            ];
        }

        return view('dashboard', compact(
            'stats',
            'driverStats',
            'today',
            'recentShipments',
            'needsAction',
            'recentDrivers',
            'chartData'
        ));
    }
}
