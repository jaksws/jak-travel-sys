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
        $user = auth()->user();
        $agency = $user->agency;
        // إذا لم يكن هناك سجل وكالة، أنشئ واحداً واربطه بالمستخدم
        if (!$agency) {
            $agency = new \App\Models\Agency();
            $agency->name = $request->agency_name;
            $agency->phone = $request->agency_phone;
            $agency->address = $request->agency_address;
            $agency->user_id = $user->id;
            $agency->email = $user->email;
            $agency->status = 'active';
            $agency->save();
            $user->agency_id = $agency->id;
            $user->save();
        } else {
            $agency->name = $request->agency_name;
            $agency->phone = $request->agency_phone;
            $agency->address = $request->agency_address;
            $agency->save();
            if (!$user->agency_id) {
                $user->agency_id = $agency->id;
                $user->save();
            }
        }
        $user->refresh();
        return redirect()->route('agency.settings.index')->with('success', 'تم تحديث الإعدادات بنجاح');
    }
}
