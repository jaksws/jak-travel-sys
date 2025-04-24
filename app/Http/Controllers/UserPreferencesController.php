<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Config;
use App\Models\User;

class UserPreferencesController extends Controller
{
    /**
     * عرض صفحة إعدادات المستخدم
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::user();
        
        $data = [
            'currentLocale' => $user->locale ?? Session::get('locale', Config::get('v1_features.multilingual.default_locale')),
            'textDirection' => Session::get('textDirection', 'rtl'),
            'currentTheme' => $user->theme ?? Session::get('theme', Config::get('v1_features.dark_mode.default')),
            'preferences' => [
                'email_notifications' => $user->email_notifications ?? true,
                'show_tips' => $user->show_tips ?? true,
            ]
        ];
        
        return view('user.preferences.index', $data);
    }

    /**
     * حفظ تفضيلات المستخدم
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function save(Request $request)
    {
        $user = Auth::user();
        
        // تحديث تفضيلات اللغة
        if ($request->has('locale')) {
            $locale = $request->locale;
            $availableLocales = Config::get('v1_features.multilingual.available_locales', ['ar', 'en', 'fr', 'tr']);
            
            if (in_array($locale, $availableLocales)) {
                Session::put('locale', $locale);
                $user->locale = $locale;
                
                // تحديث اتجاه النص (RTL/LTR)
                $rtlLocales = ['ar', 'ur'];
                Session::put('textDirection', in_array($locale, $rtlLocales) ? 'rtl' : 'ltr');
            }
        }
        
        // تحديث تفضيلات المظهر (فاتح/داكن/نظام)
        if ($request->has('theme')) {
            $theme = $request->theme;
            $validThemes = ['light', 'dark', 'system'];
            
            if (in_array($theme, $validThemes)) {
                Session::put('theme', $theme);
                $user->theme = $theme;
            }
        }

        // تحديث تفضيلات الإشعارات
        if ($request->has('email_notifications')) {
            $user->email_notifications = (bool)$request->email_notifications;
        }

        // تحديث تفضيلات النصائح
        if ($request->has('show_tips')) {
            $user->show_tips = (bool)$request->show_tips;
        }
        
        $user->save();

        // استجابة مختلفة حسب نوع الطلب
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('v2.preferences_updated_successfully', ['default' => 'Preferences updated successfully']),
                'data' => [
                    'locale' => $user->locale ?? Session::get('locale'),
                    'textDirection' => Session::get('textDirection'),
                    'theme' => $user->theme ?? Session::get('theme', 'system')
                ]
            ]);
        } else {
            return redirect()->back()->with('success', __('v2.preferences_updated_successfully', ['default' => 'تم حفظ التفضيلات بنجاح']));
        }
    }
    
    /**
     * الحصول على تفضيلات المستخدم الحالية
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPreferences()
    {
        $user = Auth::user();
        
        return response()->json([
            'success' => true,
            'data' => [
                'show_tips' => $user->show_tips ?? true,
                'email_notifications' => $user->email_notifications ?? true,
                'locale' => $user->locale ?? Session::get('locale', Config::get('v1_features.multilingual.default_locale')),
                'textDirection' => Session::get('textDirection', 'rtl'),
                'theme' => $user->theme ?? Session::get('theme', Config::get('v1_features.dark_mode.default'))
            ]
        ]);
    }
}
