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
     * Display system settings page
     */
    public function system()
    {
        return view('admin.settings.system');
    }
}
