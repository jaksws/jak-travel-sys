<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * تسجيل الدخول وإنشاء توكن API
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['بيانات الاعتماد المقدمة غير صحيحة.'],
            ]);
        }

        // التحقق من حالة المستخدم
        if ($user->status !== 'active') {
            return response()->json([
                'message' => 'الحساب غير نشط. يرجى الاتصال بمسؤول النظام.',
            ], 403);
        }

        // تسجيل تاريخ آخر تسجيل دخول
        $user->last_login_at = now();
        $user->save();

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'agency_id' => $user->agency_id,
                'status' => $user->status,
                'profile_photo' => $user->profile_photo,
            ],
            'token' => $token,
        ]);
    }

    /**
     * تسجيل مستخدم جديد
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'required|string',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'address' => $request->address,
            'role' => 'client',  // الدور الافتراضي للمستخدمين الجدد هو عميل
            'status' => 'pending',  // يحتاج إلى موافقة من المشرف
        ]);

        return response()->json([
            'message' => 'تم إنشاء الحساب بنجاح. يرجى انتظار موافقة المسؤول.',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ]
        ], 201);
    }

    /**
     * تسجيل الخروج (إبطال التوكن)
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'تم تسجيل الخروج بنجاح'
        ]);
    }

    /**
     * الحصول على بيانات المستخدم الحالي
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function user(Request $request)
    {
        $user = $request->user();
        
        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'agency_id' => $user->agency_id,
                'phone' => $user->phone,
                'address' => $user->address,
                'status' => $user->status,
                'profile_photo' => $user->profile_photo,
            ]
        ]);
    }
}