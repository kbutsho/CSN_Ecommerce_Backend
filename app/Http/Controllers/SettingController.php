<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        return Setting::values();
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'site_name' => 'required|string|max:60',
            'hero_title' => 'required|string|max:120',
            'hero_subtitle' => 'nullable|string|max:255',
            'currency' => 'required|string|max:5',
            'contact_email' => 'nullable|email|max:120',
            'contact_phone' => 'nullable|string|max:40',
            'contact_address' => 'nullable|string|max:160',
        ]);

        foreach ($data as $name => $value) {
            Setting::updateOrCreate(['name' => $name], ['value' => $value ?? '']);
        }

        return Setting::values();
    }
}
