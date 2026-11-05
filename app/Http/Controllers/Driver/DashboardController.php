<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use App\Models\Shipment;
use App\Models\ShipmentLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display the driver dashboard.
     */
    public function index()
    {
        $user   = Auth::user();
        $driver = $user->driver;

        if (! $driver || $driver->status === 'inactive') {
            abort(403, 'لا يوجد سجل سائق نشط مرتبط بحسابك.');
        }

        // Stats
        $assignedCount  = $driver->shipments()->whereIn('status', ['assigned', 'in_transit'])->count();
        $deliveredCount = $driver->shipments()->where('status', 'delivered')->count();

        // Current active shipment (only assigned or in_transit)
        $currentShipment = $driver->shipments()
            ->with('destination')
            ->whereIn('status', ['assigned', 'in_transit'])
            ->orderByDesc('updated_at')
            ->first();

        // Previous delivered shipments (latest 10)
        $previousShipments = $driver->shipments()
            ->with('destination')
            ->where('status', 'delivered')
            ->orderByDesc('delivered_at')
            ->take(10)
            ->get();

        return view('driver.dashboard', compact(
            'driver',
            'assignedCount',
            'deliveredCount',
            'currentShipment',
            'previousShipments'
        ));
    }

    /**
     * Show the driver's shipment history.
     */
    public function shipments()
    {
        $driver = Auth::user()->driver;

        if (! $driver || $driver->status === 'inactive') {
            abort(403, 'لا يوجد سجل سائق نشط مرتبط بحسابك.');
        }

        $shipments = $driver->shipments()
            ->with('destination')
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('driver.shipments', compact('driver', 'shipments'));
    }

    /**
     * Show a single shipment (only if it belongs to the driver).
     */
    public function showShipment(Shipment $shipment)
    {
        $driver = Auth::user()->driver;

        if (! $driver || $driver->status === 'inactive' || $shipment->driver_id !== $driver->id) {
            abort(403, 'هذه الشحنة ليست مسندة إليك أو أن حسابك غير نشط.');
        }

        $shipment->load(['destination', 'logs']);

        return view('driver.shipment-show', compact('driver', 'shipment'));
    }

    /**
     * Update shipment status (driver can only move forward).
     */
    public function updateStatus(Request $request, Shipment $shipment)
    {
        $driver = Auth::user()->driver;

        // Security 1: ensure driver exists and is active
        if (! $driver || $driver->status === 'inactive') {
            abort(403, 'تم إيقاف حسابك من قبل الإدارة.');
        }

        // Security 2: ensure shipment belongs to this driver
        if ($shipment->driver_id !== $driver->id) {
            abort(403, 'هذه الشحنة ليست مسندة إليك.');
        }

        // Security 3: prevent updating already completed shipment (cannot deliver twice)
        if (in_array($shipment->status, ['delivered', 'failed'])) {
            abort(403, 'لا يمكن تعديل حالة شحنة تم تسليمها أو إغلاقها مسبقاً.');
        }

        $validated = $request->validate([
            'status' => 'required|string|in:in_transit,delivered,failed',
        ]);

        $oldStatus = $shipment->status;
        $newStatus = $validated['status'];

        // Workflow transition validation:
        // Allows direct transition to delivered or failed from assigned or in_transit
        if (!in_array($newStatus, ['in_transit', 'delivered', 'failed'])) {
            abort(400, 'حالة الانتقال غير صحيحة.');
        }

        // Update timestamps based on status
        if ($newStatus === 'in_transit' && ! $shipment->started_at) {
            $shipment->started_at = now();
        }
        if ($newStatus === 'delivered') {
            if (! $shipment->started_at) {
                $shipment->started_at = now();
            }
            if (! $shipment->delivered_at) {
                $shipment->delivered_at = now();
            }
        }

        $shipment->status = $newStatus;
        $shipment->save();

        // Record in shipment logs (سجل العمليات)
        $actionNote = match($newStatus) {
            'in_transit' => "بدء الرحلة بواسطة السائق: {$driver->name}",
            'delivered'  => "تم التسليم بنجاح بواسطة السائق: {$driver->name} في " . ($shipment->delivered_at ? $shipment->delivered_at->format('Y-m-d H:i') : now()->format('Y-m-d H:i')),
            'failed'     => "تعذر التسليم بواسطة السائق: {$driver->name}",
            default      => "تحديث الحالة إلى: {$newStatus}",
        };

        ShipmentLog::create([
            'shipment_id' => $shipment->id,
            'old_status'  => $oldStatus,
            'new_status'  => $newStatus,
            'user_id'     => Auth::id(),
            'notes'       => $actionNote,
        ]);

        // Update driver availability: will set to available if no other active shipments
        $driver->updateAvailability();

        $message = match($newStatus) {
            'in_transit' => 'تم بدء الرحلة بنجاح، حالة الشحنة الآن قيد النقل.',
            'delivered'  => 'تم تأكيد تسليم الشحنة بنجاح.',
            'failed'     => 'تم تسجيل تعذر التسليم.',
            default      => 'تم تحديث حالة الشحنة بنجاح.',
        };

        return redirect()->back()->with('success', $message);
    }

    /**
     * Show the driver's profile.
     */
    public function profile()
    {
        $driver = Auth::user()->driver;

        if (! $driver || $driver->status === 'inactive') {
            abort(403, 'لا يوجد سجل سائق نشط مرتبط بحسابك.');
        }

        $driver->load('truck');

        return view('driver.profile', compact('driver'));
    }
}
