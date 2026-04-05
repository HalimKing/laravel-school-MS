<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * Display school settings page
     */
    public function school()
    {
        return view('admin.settings.school');
    }

    /**
     * Update school settings
     */
    public function updateSchool(Request $request)
    {
        $validated = $request->validate([
            'school_name' => 'nullable|string|max:255',
            'school_code' => 'nullable|string|max:100',
            'school_motto' => 'nullable|string|max:255',
            'principal_name' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:1000',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'logo' => 'nullable|image|max:2048',
        ]);

        foreach ($validated as $key => $value) {
            setSetting($key, $value);
        }

        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('settings', 'public');
            setSetting('logo', asset('storage/' . $logoPath));
        }

        return redirect()->route('settings.school')->with('success', 'School settings updated successfully.');
    }

    /**
     * Display system settings page
     */
    public function system()
    {
        return view('admin.settings.system');
    }

    /**
     * Update system settings
     */
    public function updateSystem(Request $request)
    {
        $validated = $request->validate([
            'app_name' => 'nullable|string|max:255',
            'timezone' => 'nullable|string|max:100',
            'currency' => 'nullable|string|max:10',
            'date_format' => 'nullable|string|max:20',
            'academic_year_start' => 'nullable|in:1,9',
            'attendance_threshold' => 'nullable|integer|min:0|max:100',
            'late_mark_after_minutes' => 'nullable|integer|min:1|max:1440',
            'email_notifications' => 'nullable|boolean',
            'sms_notifications' => 'nullable|boolean',
            'maintenance_mode' => 'nullable|boolean',
            'maintenance_message' => 'nullable|string|max:1000',
        ]);

        $validated['email_notifications'] = $request->has('email_notifications');
        $validated['sms_notifications'] = $request->has('sms_notifications');
        $validated['maintenance_mode'] = $request->has('maintenance_mode');

        foreach ($validated as $key => $value) {
            setSetting($key, $value);
        }

        return redirect()->route('settings.system')->with('success', 'System settings updated successfully.');
    }
}
