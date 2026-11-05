<?php

namespace App\Http\Controllers;

use App\Models\Truck;
use App\Models\Driver;
use Illuminate\Http\Request;

class TruckController extends Controller
{
    public function index()
    {
        $trucks = Truck::with('driver')->orderBy('plate_number')->paginate(15);
        return view('trucks.index', compact('trucks'));
    }

    public function create()
    {
        $drivers = Driver::where('status', '!=', 'inactive')->doesntHave('truck')->orderBy('name')->get();
        return view('trucks.create', compact('drivers'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'plate_number' => 'required|string|unique:trucks,plate_number',
            'type'         => 'required|string|max:100',
            'capacity'     => 'nullable|numeric|min:0',
            'driver_id'    => 'nullable|exists:drivers,id',
            'status'       => 'required|in:active,maintenance,inactive',
            'notes'        => 'nullable|string',
        ]);

        Truck::create($data);
        return redirect()->route('trucks.index')->with('success', 'تم إضافة الشاحنة بنجاح');
    }

    public function edit(Truck $truck)
    {
        $drivers = Driver::where('status', '!=', 'inactive')
            ->where(function ($q) use ($truck) {
                $q->doesntHave('truck')->orWhere('id', $truck->driver_id);
            })->orderBy('name')->get();

        return view('trucks.edit', compact('truck', 'drivers'));
    }

    public function update(Request $request, Truck $truck)
    {
        $data = $request->validate([
            'plate_number' => "required|string|unique:trucks,plate_number,{$truck->id}",
            'type'         => 'required|string|max:100',
            'capacity'     => 'nullable|numeric|min:0',
            'driver_id'    => 'nullable|exists:drivers,id',
            'status'       => 'required|in:active,maintenance,inactive',
            'notes'        => 'nullable|string',
        ]);

        $truck->update($data);
        return redirect()->route('trucks.index')->with('success', 'تم تحديث بيانات الشاحنة');
    }

    public function destroy(Truck $truck)
    {
        $truck->delete();
        return redirect()->route('trucks.index')->with('success', 'تم حذف الشاحنة');
    }

    public function show(Truck $truck)
    {
        $truck->load('driver');
        return view('trucks.show', compact('truck'));
    }
}
