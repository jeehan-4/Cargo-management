<?php

namespace App\Http\Controllers;

use App\Models\Destination;
use App\Models\Driver;
use App\Models\Shipment;
use App\Models\ShipmentLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShipmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Shipment::with(['destination', 'driver'])->orderBy('created_at', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } elseif (!$request->has('status')) {
            // بالوضع الافتراضي: إخفاء الشحنات المؤرشفة من القائمة الرئيسية، وتظهر عند طلبها من الفلتر أو زر الأرشيف
            $query->where('status', '!=', 'archived');
        }

        if ($request->filled('driver_id')) {
            $query->where('driver_id', $request->driver_id);
        }
        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        $shipments = $query->paginate(15)->withQueryString();
        $drivers = Driver::where('status', '!=', 'inactive')->orderBy('name')->get();
        $archivedCount = Shipment::where('status', 'archived')->count();

        return view('shipments.index', compact('shipments', 'drivers', 'archivedCount'));
    }

    public function create()
    {
        // جلب كافة الوجهات مباشرة لتظهر في القائمة المنسدلة
        $destinations = Destination::orderBy('name')->get();
        return view('shipments.create', compact('destinations'));
    }

   public function store(Request $request)
    {
        $data = $request->validate([
            'cargo_type'     => 'required|string|max:255',
            'weight'         => 'nullable|numeric|min:0',
            'quantity'       => 'nullable|integer|min:0',
            'unit'           => 'required|string|max:50',
            'origin_port'    => 'required|string|max:255',
            'destination_id' => 'required|exists:destinations,id',
            'notes'          => 'nullable|string',
        ]);

        // حماية من التكرار: استخدام session token لمنع الإرسال المزدوج
        if ($request->session()->has('last_shipment_token') &&
            $request->session()->get('last_shipment_token') === $request->input('_token')) {
            return redirect()->route('shipments.index')
                ->with('warning', 'تم حفظ الشحنة مسبقاً، تم تجاهل الطلب المكرر.');
        }

        $data['status']          = 'pending';
        $data['shipment_number'] = 'TEMP';

        $shipment = Shipment::create($data);

        // تحديث رقم الشحنة ليكون مرتبطاً بالـ ID
        $shipment->update(['shipment_number' => (string) $shipment->id]);

        ShipmentLog::create([
            'shipment_id' => $shipment->id,
            'old_status'  => null,
            'new_status'  => 'pending',
            'user_id'     => Auth::id(),
            'notes'       => 'تم إنشاء الشحنة',
        ]);

        // حفظ token لمنع الإرسال المزدوج
        $request->session()->put('last_shipment_token', $request->input('_token'));

        return redirect()->route('shipments.show', $shipment)
            ->with('success', "تم إنشاء الشحنة رقم {$shipment->shipment_number} بنجاح");
    }

  public function show(Shipment $shipment)
    {
        $shipment->load(['destination', 'driver.truck', 'logs.user']);
        
        // استخدام نموذج Driver بوضوح لضمان إرجاع كائنات Eloquent Model حقيقية
        $allDrivers = Driver::query()
            ->with('truck')
            ->where('status', '!=', 'inactive')
            ->orderBy('name')
            ->get();

        // استخدام طريقة آمنة للفلترة تتأكد من أن الكيان هو نموذج ويحتوي على الدالة
        $availableDrivers = $allDrivers->filter(function($d) {
            return method_exists($d, 'isAvailable') ? $d->isAvailable() : ($d->status === 'available');
        });

        // Driver assignment history from logs
        $assignmentLogs = ShipmentLog::with('user')
            ->where('shipment_id', $shipment->id)
            ->where(function($q) {
                $q->where('notes', 'like', '%تكليف%')
                  ->orWhere('notes', 'like', '%تغيير السائق%')
                  ->orWhere('new_status', 'assigned');
            })
            ->orderByDesc('created_at')
            ->get();

        return view('shipments.show', compact('shipment', 'availableDrivers', 'allDrivers', 'assignmentLogs'));
    }
// Assign / Reassign a driver to a shipment
    public function assign(Request $request, Shipment $shipment)
    {
        $request->validate([
            'driver_id' => 'required|exists:drivers,id',
            'reason'    => 'nullable|string|max:500',
        ]);

        $newDriver = Driver::findOrFail($request->driver_id);

        // التحقق المباشر من أن السائق متاح (سواء كانت القيم بالعربية أو الإنجليزية لضمان عدم التعطيل)
        // تأكدي أن القيم تطابق ما تخزنينه في قاعدة البيانات (مثل 'available' أو 'متاح')
        $isDriverAvailable = ($newDriver->status === 'available' || $newDriver->status === 'متاح');

        if (!$isDriverAvailable) {
            return back()->with('error', 'السائق المختار مشغول حالياً ولا يمكن إسناد شحنة له.');
        }

        $previousDriver = $shipment->driver;
        $old = $shipment->status;

        // إذا كان هناك سائق سابق، نقوم بتحريره وإعادته متاحاً
        if ($previousDriver && $previousDriver->id !== $newDriver->id) {
            $previousDriver->update(['status' => 'available']); // أو 'متاح'
        }

        // إسناد الشحنة وتغيير حالتها إلى 'assigned'
        $shipment->update([
            'driver_id'   => $newDriver->id,
            'status'      => 'assigned',
            'assigned_at' => now(),
        ]);

        // جعل السائق الجديد مشغولاً
        $newDriver->update(['status' => 'busy']);

        $reasonText = $request->filled('reason') ? " — السبب: " . $request->reason : '';
        $logNote = $previousDriver 
            ? "تم تغيير السائق من [{$previousDriver->name}] إلى [{$newDriver->name}]{$reasonText}"
            : "تم تكليف السائق: {$newDriver->name}{$reasonText}";

        ShipmentLog::create([
            'shipment_id' => $shipment->id,
            'old_status'  => $old,
            'new_status'  => 'assigned',
            'user_id'     => Auth::id(),
            'notes'       => $logNote,
        ]);

        return back()->with('success', "تم إسناد الشحنة للسائق {$newDriver->name} بنجاح.");
    }
    // Update shipment status
    public function updateStatus(Request $request, Shipment $shipment)
    {
        $request->validate([
            'status' => 'required|in:pending,assigned,in_transit,delivered,failed',
            'notes'  => 'nullable|string',
        ]);

        $old    = $shipment->status;
        $new    = $request->status;
        $update = ['status' => $new];

        if ($new === 'in_transit') {
            $update['started_at'] = now();
        }
        if (in_array($new, ['delivered', 'failed'])) {
            $update['delivered_at'] = now();
        }

        // إذا أصبحت الشحنة معلقة أو بدون سائق، حرر السائق الحالي
        if ($new === 'pending') {
            $update['driver_id']   = null;
            $update['assigned_at'] = null;
        }

        $shipment->update($update);

        // تحرير السائق عند الانتهاء (تسليم أو فشل)
        if (in_array($new, ['delivered', 'failed', 'pending']) && $shipment->driver) {
            $shipment->driver->updateAvailability();
        }

        ShipmentLog::create([
            'shipment_id' => $shipment->id,
            'old_status'  => $old,
            'new_status'  => $new,
            'user_id'     => Auth::id(),
            'notes'       => $request->notes,
        ]);

        return back()->with('success', 'تم تحديث حالة الشحنة إلى: ' . Shipment::statusLabel($new));
    }

    // إلغاء إسناد الشحنة للسائق وإرجاعها بانتظار التوزيع وتحرير السائق
    public function unassign(Request $request, Shipment $shipment)
    {
        if (in_array($shipment->status, ['delivered', 'failed', 'cancelled', 'archived'])) {
            return back()->with('error', 'لا يمكن إلغاء إسناد شحنة منتهية أو ملغاة.');
        }

        $driver = $shipment->driver;
        $old = $shipment->status;

        $shipment->update([
            'driver_id'   => null,
            'status'      => 'pending',
            'assigned_at' => null,
            'started_at'  => null,
        ]);

        if ($driver) {
            $driver->updateAvailability();
        }

        $reason = $request->filled('reason') ? " — السبب: " . $request->reason : '';
        ShipmentLog::create([
            'shipment_id' => $shipment->id,
            'old_status'  => $old,
            'new_status'  => 'pending',
            'user_id'     => Auth::id(),
            'notes'       => 'تم إلغاء إسناد الشحنة وتحرير السائق' . ($driver ? " [{$driver->name}]" : '') . $reason,
        ]);

        return back()->with('success', 'تم إلغاء الإسناد بنجاح، وعادت الشحنة بانتظار التوزيع وأصبح السائق متاحاً.');
    }

    // إلغاء الشحنة (من الميناء أو الإدارة) مع الاحتفاظ بكافة بياناتها
    public function cancel(Request $request, Shipment $shipment)
    {
        if ($shipment->status === 'delivered') {
            return back()->with('error', 'لا يمكن إلغاء شحنة تم تسليمها بنجاح.');
        }

        if ($shipment->status === 'cancelled') {
            return back()->with('warning', 'هذه الشحنة ملغاة مسبقاً.');
        }

        $driver = $shipment->driver;
        $old = $shipment->status;

        $shipment->update([
            'status' => 'cancelled',
        ]);

        // تحرير السائق المرتبط بها فوراً ليصبح متاحاً
        if ($driver) {
            $driver->updateAvailability();
        }

        $reason = $request->filled('reason') ? " — سبب الإلغاء: " . $request->reason : ' — تم الإلغاء من الميناء / الإدارة';
        ShipmentLog::create([
            'shipment_id' => $shipment->id,
            'old_status'  => $old,
            'new_status'  => 'cancelled',
            'user_id'     => Auth::id(),
            'notes'       => 'تم إلغاء الشحنة' . $reason,
        ]);

        return back()->with('success', 'تم إلغاء الشحنة بنجاح مع الاحتفاظ بكافة بياناتها في السجلات.');
    }

    // أرشفة الشحنات القديمة بدلاً من حذفها
    public function archive(Shipment $shipment)
    {
        if (in_array($shipment->status, ['assigned', 'in_transit'])) {
            return back()->with('error', 'لا يمكن أرشفة شحنة ما زالت قيد التوصيل.');
        }

        $old = $shipment->status;
        $shipment->update([
            'status' => 'archived',
        ]);

        ShipmentLog::create([
            'shipment_id' => $shipment->id,
            'old_status'  => $old,
            'new_status'  => 'archived',
            'user_id'     => Auth::id(),
            'notes'       => 'تم نقل الشحنة إلى الأرشيف لحفظ السجلات',
        ]);

        return back()->with('success', 'تمت أرشفة الشحنة بنجاح.');
    }

    // حذف الشحنة من النظام (مسموح فقط للشحنات الجديدة المضافة بالخطأ ولم تُسند)
    public function destroy(Shipment $shipment)
    {
        // حظر حذف الشحنات المسلمة قطعياً
        if ($shipment->status === 'delivered') {
            return redirect()->back()->with('error', 'ممنوع حذف الشحنات التي تم تسليمها؛ يجب الاحتفاظ بها دائماً في السجلات والتقارير.');
        }

        // حظر حذف الشحنات الملغاة أو المؤرشفة للحفاظ على نزاهة السجلات
        if (in_array($shipment->status, ['cancelled', 'archived'])) {
            return redirect()->back()->with('error', 'لا يمكن حذف الشحنات الملغاة أو المؤرشفة، حيث يتم الاحتفاظ بها في السجل الدائم.');
        }

        // حظر حذف أي شحنة مسندة لسائق أو قيد التوصيل
        if ($shipment->driver_id !== null || in_array($shipment->status, ['assigned', 'in_transit'])) {
            return redirect()->back()->with('error', 'لا يمكن حذف شحنة مسندة لسائق. يمكنك إلغاء الإسناد أولاً أو إلغاء الشحنة.');
        }

        // يُسمح بالحذف فقط للشحنات التي أُضيفت بالخطأ بحالة pending وبدون سائق
        $shipment->delete();

        return redirect()->route('shipments.index')->with('success', 'تم حذف الشحنة المضافة بالخطأ بنجاح.');
    }

    public function exportExcel()
    {
        $filename = "shipments_" . date('Y-m-d') . ".csv";
        $shipments = Shipment::with(['destination', 'driver'])->get();

        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use($shipments) {
            $file = fopen('php://output', 'w');
            // إضافة دعم اللغة العربية في ملفات إكسل
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // رؤوس الأعمدة
            fputcsv($file, ['رقم الشحنة', 'نوع البضاعة', 'الكمية', 'الوزن', 'ميناء المنشأ', 'الوجهة', 'السائق', 'الحالة', 'تاريخ الإنشاء']);

            // تعبئة البيانات
            foreach ($shipments as $s) {
                // إظهار التاريخ فقط بدون الوقت (السنة-الشهر-اليوم)
                $formattedDate = $s->created_at ? "\t" . $s->created_at->format('Y-m-d') : '—';

                fputcsv($file, [
                    $s->shipment_number,
                    $s->cargo_type,
                    $s->quantity ? $s->quantity . ' ' . $s->unit : '—',
                    $s->weight ? $s->weight . ' طن' : '—',
                    $s->origin_port,
                    $s->destination?->name ?? '—',
                    $s->driver?->name ?? 'غير معين',
                    Shipment::statusLabel($s->status),
                    $formattedDate
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function board(Request $request)
    {
        $query = Shipment::with(['destination', 'driver'])->orderBy('created_at', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            $query->whereIn('status', ['pending', 'assigned']);
        }

        $shipments = $query->paginate(15)->withQueryString();

        // جلب السائقين المتاحين فقط (لا يوجد لديهم شحنة نشطة)
        $drivers = Driver::with('shipments')
            ->where('status', 'available')
            ->whereDoesntHave('shipments', function ($q) {
                $q->whereIn('status', ['assigned', 'in_transit']);
            })
            ->orderBy('name')
            ->get();

        return view('shipments.board', compact('shipments', 'drivers'));
    }
    /**
     * عرض صفحة تعديل الشحنة.
     */
    public function edit(Shipment $shipment)
    {
        $destinations = Destination::orderBy('name')->get();
        $drivers = Driver::where('status', '!=', 'inactive')->orderBy('name')->get();
        
        return view('shipments.edit', compact('shipment', 'destinations', 'drivers'));
    }

    /**
     * حفظ التعديلات الخاصة بالشحنة مع توثيق سجل التغييرات التفصيلي.
     */
    public function update(Request $request, Shipment $shipment)
    {
        $data = $request->validate([
            'cargo_type'     => 'required|string|max:255',
            'weight'         => 'nullable|numeric|min:0',
            'quantity'       => 'nullable|integer|min:0',
            'unit'           => 'required|string|max:50',
            'origin_port'    => 'required|string|max:255',
            'destination_id' => 'required|exists:destinations,id',
            'notes'          => 'nullable|string',
        ]);

        $fieldLabels = [
            'cargo_type'     => 'نوع البضاعة',
            'weight'         => 'الوزن',
            'quantity'       => 'الكمية',
            'unit'           => 'الوحدة',
            'origin_port'    => 'ميناء المنشأ',
            'destination_id' => 'الوجهة',
            'notes'          => 'الملاحظات',
        ];

        $changes = [];
        foreach ($data as $field => $newVal) {
            $oldVal = $shipment->{$field};
            if ((string)$oldVal !== (string)$newVal) {
                if ($field === 'destination_id') {
                    $oldDest = Destination::find($oldVal)?->name ?? $oldVal;
                    $newDest = Destination::find($newVal)?->name ?? $newVal;
                    $changes[] = "الوجهة: من [{$oldDest}] إلى [{$newDest}]";
                } else {
                    $label = $fieldLabels[$field] ?? $field;
                    $changes[] = "{$label}: من [{$oldVal}] إلى [{$newVal}]";
                }
            }
        }

        $shipment->update($data);

        $changeNote = count($changes) > 0 
            ? 'تم تعديل بيانات الشحنة: (' . implode('، ', $changes) . ')'
            : 'تم حفظ بيانات الشحنة دون تغييرات جوهرية';

        ShipmentLog::create([
            'shipment_id' => $shipment->id,
            'old_status'  => $shipment->status,
            'new_status'  => $shipment->status,
            'user_id'     => Auth::id(),
            'notes'       => $changeNote,
        ]);

        return redirect()->route('shipments.show', $shipment)
            ->with('success', "تم تحديث بيانات الشحنة رقم {$shipment->shipment_number} وتوثيق التعديل في السجل بنجاح.");
    }
}