<?php

namespace App\Http\Controllers\Agency;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        // يمكنك تمرير بيانات الإعدادات هنا إذا لزم الأمر
        return view('agency.settings.index');
    }

    public function update(Request $request)
    {
        // منطق تحديث الإعدادات (يمكنك تخصيصه لاحقاً)
        return redirect()->route('agency.settings.index')->with('success', 'تم تحديث الإعدادات بنجاح');
    }
}
