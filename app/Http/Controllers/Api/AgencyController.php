<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Agency;
use Illuminate\Http\Request;

class AgencyController extends Controller
{
    /**
     * عرض قائمة الوكالات
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Agency::query();
        
        // التصفية حسب الحالة (نشط / معلق)
        if ($request->has('status')) {
            $query->where('status', $request->status);
        } else {
            // عرض الوكالات النشطة فقط بشكل افتراضي
            $query->where('status', 'active');
        }
        
        // البحث حسب الاسم
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('license_number', 'like', '%' . $request->search . '%');
        }
        
        // ترتيب الوكالات حسب الاسم
        $query->orderBy('name');
        
        $agencies = $query->paginate(15);
        
        return response()->json([
            'data' => $agencies->items(),
            'meta' => [
                'current_page' => $agencies->currentPage(),
                'last_page' => $agencies->lastPage(),
                'per_page' => $agencies->perPage(),
                'total' => $agencies->total()
            ]
        ]);
    }

    /**
     * عرض تفاصيل وكالة محددة
     *
     * @param  \App\Models\Agency  $agency
     * @return \Illuminate\Http\Response
     */
    public function show(Agency $agency)
    {
        // تحميل الخدمات النشطة المرتبطة بالوكالة
        $agency->load([
            'services' => function ($query) {
                $query->where('status', 'active')
                      ->orderBy('created_at', 'desc')
                      ->limit(10);
            }
        ]);
        
        return response()->json([
            'data' => $agency
        ]);
    }
}