<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserPreferencesController extends Controller
{
    /**
     * تحديث تفضيلات المستخدم
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        
        if ($request->has('show_tips')) {
            $user->show_tips = (bool) $request->show_tips;
            $user->save();
        }
        
        // يمكن إضافة المزيد من التفضيلات هنا
        
        return response()->json([
            'success' => true,
            'message' => 'تم تحديث التفضيلات بنجاح'
        ]);
    }
}
