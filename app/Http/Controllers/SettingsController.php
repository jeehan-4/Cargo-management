<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function edit()
    {
        $settings = Setting::allSettings();
        return view('settings.edit', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'company_name'        => ['required', 'string', 'max:255'],
            'company_phone'       => ['nullable', 'string', 'max:50'],
            'company_address'     => ['nullable', 'string', 'max:500'],
            'default_origin_port' => ['required', 'string', 'max:255'],
            'report_footer'       => ['nullable', 'string', 'max:1000'],
        ]);

        foreach ($data as $key => $value) {
            Setting::set($key, $value);
        }

        return redirect()->route('settings.edit')->with('success', 'تم حفظ الإعدادات بنجاح.');
    }
}