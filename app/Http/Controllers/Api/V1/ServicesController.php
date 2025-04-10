<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;
use App\Http\Resources\ServiceResource;
use App\Http\Resources\ServiceCollection;

class ServicesController extends Controller
{
    /**
     * عرض قائمة الخدمات
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // فلترة الخدمات حسب معايير البحث
        $query = Service::query();

        if ($request->has('agency_id')) {
            $query->where('agency_id', $request->agency_id);
        }

        if ($request->has('type')) {
            $query->where('service_type', $request->type);
        }

        if ($request->has('active')) {
            $query->where('is_active', $request->boolean('active'));
        }

        // التحقق من صلاحيات المستخدم
        if (!$request->user()->isAdmin()) {
            $query->where('agency_id', $request->user()->agency_id);
        }

        $services = $query->paginate($request->per_page ?? 15);
        
        return new ServiceCollection($services);
    }

    /**
     * عرض تفاصيل خدمة محددة
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $service = Service::findOrFail($id);
        
        // التحقق من صلاحيات المستخدم
        if (!auth()->user()->isAdmin() && $service->agency_id !== auth()->user()->agency_id) {
            return response()->json(['message' => 'غير مصرح لك بالوصول إلى هذه الخدمة'], 403);
        }
        
        return new ServiceResource($service);
    }

    // ... المزيد من العمليات على الخدمات
}
