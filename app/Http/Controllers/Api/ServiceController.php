<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ServiceTypeHelper;
use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ServiceController extends Controller
{
    /**
     * عرض قائمة الخدمات
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $query = Service::query();
        
        // التصفية حسب نوع الخدمة
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }
        
        // التصفية حسب الوكالة
        if ($request->has('agency_id')) {
            $query->where('agency_id', $request->agency_id);
        }
        
        // التصفية حسب الحالة
        $query->where('status', 'active');
        
        $services = $query->paginate($request->per_page ?? 10);

        return response()->json(['data' => $services]);
    }

    /**
     * تخزين خدمة جديدة
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'agency_id' => 'required|exists:agencies,id',
            'name' => 'required|string|max:255',
            'type' => 'required|string',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'currency_id' => 'required|exists:currencies,id',
            'image' => 'nullable|image|max:2048', // حد أقصى 2 ميجابايت
            'status' => 'required|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'خطأ في البيانات المدخلة',
                'errors' => $validator->errors()
            ], 422);
        }

        // التحقق من صحة نوع الخدمة
        if (!ServiceTypeHelper::isValidType($request->type)) {
            return response()->json([
                'message' => 'نوع الخدمة غير صالح',
                'errors' => ['type' => ['نوع الخدمة المحدد غير مدعوم']]
            ], 422);
        }

        $data = $validator->validated();

        // معالجة الصورة إذا تم تحميلها
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('services', 'public');
            $data['image'] = $path;
        }

        $service = Service::create($data);

        return response()->json([
            'message' => 'تم إضافة الخدمة بنجاح',
            'service' => $service
        ], 201);
    }

    /**
     * عرض معلومات خدمة محددة
     *
     * @param  Service  $service
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Service $service)
    {
        return response()->json(['data' => $service]);
    }

    /**
     * تحديث معلومات خدمة محددة
     *
     * @param  Request  $request
     * @param  Service  $service
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Service $service)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'type' => 'sometimes|required|string',
            'description' => 'nullable|string',
            'price' => 'sometimes|required|numeric|min:0',
            'currency_id' => 'sometimes|required|exists:currencies,id',
            'image' => 'nullable|image|max:2048',
            'status' => 'sometimes|required|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'خطأ في البيانات المدخلة',
                'errors' => $validator->errors()
            ], 422);
        }

        // التحقق من صحة نوع الخدمة إذا تم تحديثه
        if ($request->has('type') && !ServiceTypeHelper::isValidType($request->type)) {
            return response()->json([
                'message' => 'نوع الخدمة غير صالح',
                'errors' => ['type' => ['نوع الخدمة المحدد غير مدعوم']]
            ], 422);
        }

        $data = $validator->validated();

        // معالجة الصورة إذا تم تحميلها
        if ($request->hasFile('image')) {
            // حذف الصورة القديمة إذا وجدت
            if ($service->image && Storage::disk('public')->exists($service->image)) {
                Storage::disk('public')->delete($service->image);
            }

            $path = $request->file('image')->store('services', 'public');
            $data['image'] = $path;
        }

        $service->update($data);

        return response()->json([
            'message' => 'تم تحديث الخدمة بنجاح',
            'service' => $service
        ]);
    }

    /**
     * حذف خدمة محددة
     *
     * @param  Service  $service
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Service $service)
    {
        // التحقق من وجود عروض أسعار مرتبطة
        if ($service->quotes()->exists()) {
            return response()->json([
                'message' => 'لا يمكن حذف الخدمة لأنها مرتبطة بعروض أسعار',
            ], 403);
        }

        // حذف الصورة إذا وجدت
        if ($service->image && Storage::disk('public')->exists($service->image)) {
            Storage::disk('public')->delete($service->image);
        }

        $service->delete();

        return response()->json([
            'message' => 'تم حذف الخدمة بنجاح'
        ]);
    }

    /**
     * الحصول على قائمة أنواع الخدمات
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getServiceTypes()
    {
        $types = ServiceTypeHelper::getTypeOptions();
        
        return response()->json([
            'types' => $types
        ]);
    }
}