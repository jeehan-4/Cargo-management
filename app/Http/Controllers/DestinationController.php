<?php

namespace App\Http\Controllers;

use App\Models\Destination;
use Illuminate\Http\Request;

class DestinationController extends Controller
{
    public function index()
    {
        $destinations = Destination::withCount('shipments')->orderBy('name')->paginate(15);
        return view('destinations.index', compact('destinations'));
    }

    public function create()
    {
        return view('destinations.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'          => 'required|string|max:255',
            'city'          => 'required|string|max:255',
            'address'       => 'nullable|string|max:500',
            'contact_name'  => 'nullable|string|max:255',
            'contact_phone' => 'nullable|string|max:30',
            'notes'         => 'nullable|string',
            'is_active'     => 'boolean',
        ]);

        $data['is_active'] = $request->has('is_active');
        Destination::create($data);

        return redirect()->route('destinations.index')->with('success', 'تم إضافة الوجهة بنجاح');
    }

    public function edit(Destination $destination)
    {
        return view('destinations.edit', compact('destination'));
    }

    public function update(Request $request, Destination $destination)
    {
        $data = $request->validate([
            'name'          => 'required|string|max:255',
            'city'          => 'required|string|max:255',
            'address'       => 'nullable|string|max:500',
            'contact_name'  => 'nullable|string|max:255',
            'contact_phone' => 'nullable|string|max:30',
            'notes'         => 'nullable|string',
        ]);

        $data['is_active'] = $request->has('is_active');
        $destination->update($data);

        return redirect()->route('destinations.index')->with('success', 'تم تحديث الوجهة بنجاح');
    }

    public function destroy(Destination $destination)
    {
        if ($destination->shipments()->exists()) {
            return back()->with('error', 'لا يمكن حذف وجهة مرتبطة بشحنات');
        }
        $destination->delete();
        return redirect()->route('destinations.index')->with('success', 'تم حذف الوجهة');
    }

    public function show(Destination $destination)
    {
        $destination->load(['shipments.driver']);
        return view('destinations.show', compact('destination'));
    }
}
