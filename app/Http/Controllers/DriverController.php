<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;     // <--- مهم لاستخدام نموذج المستخدم
use App\Models\Driver;   // <--- مهم لاستخدام نموذج السائق
use App\Models\Truck;    // <--- مهم لاستخدام نموذج الشاحنة
use Illuminate\Support\Facades\DB;     // <--- مهم لاستخدام DB Transaction
use Illuminate\Support\Facades\Hash;   // <--- مهم لتشفير كلمة المرور
class DriverController extends Controller
{
    public function index(Request $request)
    {
        $query = Driver::with('truck')->orderBy('name');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('phone', 'like', "%{$request->search}%")
                  ->orWhere('license_number', 'like', "%{$request->search}%");
            });
        }

        $drivers = $query->paginate(15)->withQueryString();
        return view('drivers.index', compact('drivers'));
    }

    public function create()
    {
        $trucks = Truck::whereNull('driver_id')->where('status', 'active')->get();
        return view('drivers.create', compact('trucks'));
    }

    public function store(Request $request)
{
    $request->validate([
        'name'           => 'required|string|max:255',
        'email'          => 'required|string|email|max:255|unique:users',
        'password'       => 'required|string|min:8',
        'phone'          => 'required|string|unique:drivers,phone',
        'license_number' => 'required|string|unique:drivers,license_number',
        'national_id'    => 'nullable|string|max:50',
        'status'         => 'required|in:available,busy,inactive',
        'notes'          => 'nullable|string',
        'truck_id'       => 'nullable|exists:trucks,id',
    ]);

    DB::transaction(function () use ($request) {
        // 1. إنشاء حساب المستخدم للسائق
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'driver', // تعيين دوره كـ سائق ليعمل مع الـ Middleware
        ]);

        // 2. إنشاء سجل السائق وربطه بحساب المستخدم
        $driver = Driver::create([
            'user_id'        => $user->id, // تأكدي أن عمود user_id موجود في جدول drivers
            'name'           => $request->name,
            'phone'          => $request->phone,
            'license_number' => $request->license_number,
            'national_id'    => $request->national_id,
            'status'         => $request->status,
            'notes'          => $request->notes,
        ]);

        // 3. ربط الشاحنة إن وجدت
        if ($request->filled('truck_id')) {
            Truck::where('id', $request->truck_id)->update(['driver_id' => $driver->id]);
        }
    });

    return redirect()->route('drivers.index')->with('success', 'تم إضافة السائق وحساب تسجيل الدخول بنجاح.');
}

    public function show(Driver $driver)
    {
        $driver->load(['truck', 'shipments.destination']);
        return view('drivers.show', compact('driver'));
    }

    public function edit(Driver $driver)
    {
        $trucks = Truck::where(function ($q) use ($driver) {
            $q->whereNull('driver_id')->orWhere('driver_id', $driver->id);
        })->where('status', 'active')->get();

        return view('drivers.edit', compact('driver', 'trucks'));
    }

    public function update(Request $request, Driver $driver)
    {
        $data = $request->validate([
            'name'           => 'required|string|max:255',
            'phone'          => "required|string|unique:drivers,phone,{$driver->id}",
            'license_number' => "required|string|unique:drivers,license_number,{$driver->id}",
            'national_id'    => 'nullable|string|max:50',
            'status'         => 'required|in:available,busy,inactive',
            'notes'          => 'nullable|string',
            'truck_id'       => 'nullable|exists:trucks,id',
        ]);

        DB::transaction(function () use ($driver, $data, $request) {
            $driver->update($data);

            // مزامنة اسم السائق في حساب تسجيل الدخول المرتبط
            if ($driver->user) {
                $driver->user->update(['name' => $data['name']]);
            }

            // Detach old truck
            Truck::where('driver_id', $driver->id)->update(['driver_id' => null]);
            if ($request->filled('truck_id')) {
                Truck::where('id', $request->truck_id)->update(['driver_id' => $driver->id]);
            }
        });

        return redirect()->route('drivers.index')->with('success', 'تم تحديث بيانات السائق بنجاح');
    }

    public function destroy(Driver $driver)
    {
        if ($driver->activeShipments()->exists()) {
            return back()->with('error', 'لا يمكن حذف سائق لديه شحنات نشطة');
        }

        DB::transaction(function () use ($driver) {
            // فك ارتباط الشاحنة
            Truck::where('driver_id', $driver->id)->update(['driver_id' => null]);

            // حذف حساب تسجيل الدخول المرتبط حتى لا يبقى حساب يتيم يمكنه الدخول
            $user = $driver->user;
            $driver->delete();
            if ($user) {
                $user->delete();
            }
        });

        return redirect()->route('drivers.index')->with('success', 'تم حذف السائق وحساب تسجيل الدخول المرتبط به');
    }
}
