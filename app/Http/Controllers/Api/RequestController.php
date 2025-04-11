<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Request as ServiceRequest;
use App\Models\Service;
use App\Models\Quote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class RequestController extends Controller
{
    /**
     * عرض قائمة الطلبات
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $query = ServiceRequest::query();
        
        // العميل يرى طلباته فقط
        if ($request->user()->isClient()) {
            $query->where('user_id', $request->user()->id);
        }
        
        // الوكيل يرى طلبات وكالته فقط
        elseif ($request->user()->isAgent() && $request->user()->agency_id) {
            $query->whereHas('service', function($q) use ($request) {
                $q->where('agency_id', $request->user()->agency_id);
            });
        }
        
        // التصفية حسب نوع الخدمة
        if ($request->has('service_type')) {
            $query->whereHas('service', function($q) use ($request) {
                $q->where('type', $request->service_type);
            });
        }
        
        // التصفية حسب الحالة
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        
        $serviceRequests = $query->with(['user', 'service', 'quotes'])->latest()->paginate($request->per_page ?? 10);
        
        return response()->json($serviceRequests);
    }

    /**
     * تخزين طلب جديد
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'service_id' => 'required|exists:services,id',
            'details' => 'required|string',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'adults' => 'required|integer|min:1',
            'children' => 'nullable|integer|min:0',
            'additional_requirements' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'خطأ في البيانات المدخلة',
                'errors' => $validator->errors()
            ], 422);
        }

        // التحقق من وجود الخدمة ومن أنها نشطة
        $service = Service::findOrFail($request->service_id);
        if ($service->status !== 'active') {
            return response()->json([
                'message' => 'الخدمة غير متاحة حالياً'
            ], 400);
        }

        // إنشاء الطلب
        $serviceRequest = new ServiceRequest();
        $serviceRequest->user_id = Auth::id();
        $serviceRequest->service_id = $request->service_id;
        $serviceRequest->details = $request->details;
        $serviceRequest->start_date = $request->start_date;
        $serviceRequest->end_date = $request->end_date;
        $serviceRequest->adults = $request->adults;
        $serviceRequest->children = $request->children ?? 0;
        $serviceRequest->additional_requirements = $request->additional_requirements;
        $serviceRequest->status = 'pending';
        $serviceRequest->save();

        return response()->json([
            'message' => 'تم إنشاء الطلب بنجاح',
            'request' => $serviceRequest->load(['user', 'service'])
        ], 201);
    }

    /**
     * عرض معلومات طلب محدد
     *
     * @param  ServiceRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(ServiceRequest $request)
    {
        // التحقق من الصلاحيات
        $this->authorize('view', $request);
        
        $request->load(['user', 'service', 'quotes']);
        
        return response()->json([
            'request' => $request
        ]);
    }

    /**
     * تحديث معلومات طلب محدد
     *
     * @param  Request  $httpRequest
     * @param  ServiceRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $httpRequest, ServiceRequest $request)
    {
        // التحقق من الصلاحيات
        $this->authorize('update', $request);
        
        // التحقق من إمكانية تحديث الطلب (فقط الطلبات في حالة الانتظار)
        if ($request->status !== 'pending') {
            return response()->json([
                'message' => 'لا يمكن تعديل الطلب في الحالة الحالية'
            ], 403);
        }
        
        $validator = Validator::make($httpRequest->all(), [
            'details' => 'sometimes|required|string',
            'start_date' => 'sometimes|required|date|after_or_equal:today',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'adults' => 'sometimes|required|integer|min:1',
            'children' => 'nullable|integer|min:0',
            'additional_requirements' => 'nullable|string',
            'status' => 'sometimes|in:pending,cancelled',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'خطأ في البيانات المدخلة',
                'errors' => $validator->errors()
            ], 422);
        }

        // تحديث الطلب
        if ($httpRequest->has('details')) $request->details = $httpRequest->details;
        if ($httpRequest->has('start_date')) $request->start_date = $httpRequest->start_date;
        if ($httpRequest->has('end_date')) $request->end_date = $httpRequest->end_date;
        if ($httpRequest->has('adults')) $request->adults = $httpRequest->adults;
        if ($httpRequest->has('children')) $request->children = $httpRequest->children;
        if ($httpRequest->has('additional_requirements')) $request->additional_requirements = $httpRequest->additional_requirements;
        if ($httpRequest->has('status')) $request->status = $httpRequest->status;
        
        $request->save();

        return response()->json([
            'message' => 'تم تحديث الطلب بنجاح',
            'request' => $request->load(['user', 'service'])
        ]);
    }

    /**
     * إلغاء طلب محدد
     *
     * @param  ServiceRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function cancel(ServiceRequest $request)
    {
        // التحقق من الصلاحيات
        $this->authorize('update', $request);
        
        // التحقق من إمكانية إلغاء الطلب (فقط الطلبات في حالة الانتظار)
        if ($request->status !== 'pending') {
            return response()->json([
                'message' => 'لا يمكن إلغاء الطلب في الحالة الحالية'
            ], 403);
        }
        
        $request->status = 'cancelled';
        $request->save();

        return response()->json([
            'message' => 'تم إلغاء الطلب بنجاح',
            'request' => $request
        ]);
    }

    /**
     * تقديم عرض سعر على طلب
     *
     * @param  Request  $httpRequest
     * @param  ServiceRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function submitQuote(Request $httpRequest, ServiceRequest $request)
    {
        // التحقق من الصلاحيات (يجب أن يكون وكيلاً أو وكيلاً فرعياً)
        if (!$httpRequest->user()->isAgent() && !$httpRequest->user()->isSubAgent()) {
            return response()->json([
                'message' => 'غير مصرح لك بتنفيذ هذه العملية'
            ], 403);
        }
        
        // التحقق من حالة الطلب
        if ($request->status !== 'pending') {
            return response()->json([
                'message' => 'لا يمكن تقديم عرض سعر على هذا الطلب في الحالة الحالية'
            ], 400);
        }
        
        $validator = Validator::make($httpRequest->all(), [
            'price' => 'required|numeric|min:0',
            'currency_id' => 'required|exists:currencies,id',
            'description' => 'required|string',
            'valid_until' => 'required|date|after:today',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'خطأ في البيانات المدخلة',
                'errors' => $validator->errors()
            ], 422);
        }

        // إنشاء عرض السعر
        $quote = new Quote();
        $quote->request_id = $request->id;
        $quote->subagent_id = $httpRequest->user()->id;
        $quote->price = $httpRequest->price;
        $quote->currency_id = $httpRequest->currency_id;
        $quote->description = $httpRequest->description;
        $quote->valid_until = $httpRequest->valid_until;
        $quote->status = 'pending';
        $quote->save();

        return response()->json([
            'message' => 'تم تقديم عرض السعر بنجاح',
            'quote' => $quote
        ], 201);
    }

    /**
     * الحصول على عروض الأسعار لطلب محدد
     *
     * @param  ServiceRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getQuotes(ServiceRequest $request)
    {
        // التحقق من الصلاحيات
        $this->authorize('view', $request);
        
        $quotes = $request->quotes()->with('currency')->get();
        
        return response()->json([
            'quotes' => $quotes
        ]);
    }
}