<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Quote;
use App\Models\QuoteAttachment;
use App\Models\Request as TravelRequest;
use App\Models\Currency;
use App\Notifications\QuoteStatusChanged;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class QuoteController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * عرض قائمة عروض الأسعار
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $query = Quote::query();
        
        // العميل يرى عروض الأسعار المقدمة على طلباته فقط
        if ($request->user()->isClient()) {
            $query->whereHas('request', function($q) use ($request) {
                $q->where('user_id', $request->user()->id);
            });
        }
        
        // الوكيل السياحي يرى عروض الأسعار التي قدمها فقط
        elseif ($request->user()->isAgent() || $request->user()->isSubAgent()) {
            $query->where('subagent_id', $request->user()->id);
        }
        
        // التصفية حسب الحالة
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        
        $quotes = $query->with(['request', 'currency', 'subagent'])->latest()->paginate($request->per_page ?? 10);
        
        return response()->json($quotes);
    }

    /**
     * إنشاء عرض سعر جديد
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = $request->user();
        
        // التحقق من الصلاحية (يجب أن يكون وكيل أو وكيل فرعي)
        if ($user->role !== 'agency' && $user->role !== 'subagent') {
            return response()->json([
                'message' => 'غير مصرح لك بإنشاء عروض أسعار'
            ], 403);
        }
        
        $request->validate([
            'request_id' => 'required|exists:requests,id',
            'price' => 'required|numeric|min:0',
            'currency_id' => 'required|exists:currencies,id',
            'description' => 'required|string',
            'valid_until' => 'required|date|after:today',
            'notes' => 'nullable|string'
        ]);
        
        // التحقق من أن الطلب مرتبط بالوكالة التي ينتمي إليها المستخدم
        $travelRequest = TravelRequest::findOrFail($request->request_id);
        if ($travelRequest->service->agency_id !== $user->agency_id) {
            return response()->json([
                'message' => 'غير مصرح لك بإنشاء عرض سعر لهذا الطلب'
            ], 403);
        }
        
        // إنشاء عرض السعر
        $quote = Quote::create([
            'request_id' => $request->request_id,
            'user_id' => $user->id,
            'price' => $request->price,
            'currency_id' => $request->currency_id,
            'description' => $request->description,
            'status' => 'pending',
            'valid_until' => $request->valid_until,
            'notes' => $request->notes,
        ]);
        
        // تحميل العلاقات
        $quote->load(['request', 'user', 'currency']);
        
        // إرسال إشعار للعميل
        $travelRequest->user->notify(new QuoteStatusChanged($quote, 'pending'));
        
        return response()->json([
            'message' => 'تم إنشاء عرض السعر بنجاح',
            'data' => $quote
        ], 201);
    }

    /**
     * عرض معلومات عرض سعر محدد
     *
     * @param  Quote  $quote
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Quote $quote)
    {
        // التحقق من الصلاحيات
        $this->authorize('view', $quote);
        
        $quote->load(['request', 'currency', 'subagent', 'attachments']);
        
        return response()->json([
            'quote' => $quote
        ]);
    }

    /**
     * تحديث معلومات عرض سعر محدد
     *
     * @param  Request  $request
     * @param  Quote  $quote
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Quote $quote)
    {
        // التحقق من الصلاحيات
        $this->authorize('update', $quote);
        
        // التحقق من إمكانية تحديث عرض السعر (فقط عروض الأسعار في حالة الانتظار)
        if ($quote->status !== 'pending') {
            return response()->json([
                'message' => 'لا يمكن تعديل عرض السعر في الحالة الحالية'
            ], 403);
        }
        
        $validator = Validator::make($request->all(), [
            'price' => 'sometimes|required|numeric|min:0',
            'currency_id' => 'sometimes|required|exists:currencies,id',
            'description' => 'sometimes|required|string',
            'valid_until' => 'sometimes|required|date|after:today',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'خطأ في البيانات المدخلة',
                'errors' => $validator->errors()
            ], 422);
        }

        // تحديث عرض السعر
        if ($request->has('price')) $quote->price = $request->price;
        if ($request->has('currency_id')) $quote->currency_id = $request->currency_id;
        if ($request->has('description')) $quote->description = $request->description;
        if ($request->has('valid_until')) $quote->valid_until = $request->valid_until;
        
        $quote->save();

        return response()->json([
            'message' => 'تم تحديث عرض السعر بنجاح',
            'quote' => $quote->load(['currency'])
        ]);
    }

    /**
     * إضافة مرفق إلى عرض السعر
     *
     * @param  Request  $request
     * @param  Quote  $quote
     * @return \Illuminate\Http\JsonResponse
     */
    public function addAttachment(Request $request, Quote $quote)
    {
        // التحقق من الصلاحيات
        $this->authorize('update', $quote);
        
        // التحقق من إمكانية تحديث عرض السعر
        if (!in_array($quote->status, ['pending', 'accepted'])) {
            return response()->json([
                'message' => 'لا يمكن إضافة مرفقات لعرض السعر في الحالة الحالية'
            ], 403);
        }
        
        $validator = Validator::make($request->all(), [
            'attachment' => 'required|file|max:10240', // حد أقصى 10 ميجابايت
            'description' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'خطأ في البيانات المدخلة',
                'errors' => $validator->errors()
            ], 422);
        }

        // تخزين الملف
        $path = $request->file('attachment')->store('quote-attachments', 'public');
        
        // إنشاء مرفق جديد
        $attachment = new QuoteAttachment();
        $attachment->quote_id = $quote->id;
        $attachment->file_path = $path;
        $attachment->file_name = $request->file('attachment')->getClientOriginalName();
        $attachment->file_type = $request->file('attachment')->getMimeType();
        $attachment->file_size = $request->file('attachment')->getSize();
        $attachment->description = $request->description;
        $attachment->save();

        return response()->json([
            'message' => 'تم إضافة المرفق بنجاح',
            'attachment' => $attachment
        ], 201);
    }

    /**
     * حذف مرفق من عرض السعر
     *
     * @param  QuoteAttachment  $attachment
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteAttachment(QuoteAttachment $attachment)
    {
        // التحقق من الصلاحيات
        $this->authorize('delete', $attachment->quote);
        
        // التحقق من إمكانية تحديث عرض السعر
        if (!in_array($attachment->quote->status, ['pending', 'accepted'])) {
            return response()->json([
                'message' => 'لا يمكن حذف مرفقات عرض السعر في الحالة الحالية'
            ], 403);
        }
        
        // حذف الملف من التخزين
        if (Storage::disk('public')->exists($attachment->file_path)) {
            Storage::disk('public')->delete($attachment->file_path);
        }
        
        // حذف السجل
        $attachment->delete();

        return response()->json([
            'message' => 'تم حذف المرفق بنجاح'
        ]);
    }

    /**
     * تغيير حالة عرض السعر (قبول أو رفض)
     *
     * @param  Request  $request
     * @param  Quote  $quote
     * @return \Illuminate\Http\JsonResponse
     */
    public function changeStatus(Request $request, Quote $quote)
    {
        // التحقق من الصلاحيات (يجب أن يكون المستخدم هو صاحب الطلب)
        $serviceRequest = $quote->request;
        
        if ($request->user()->id !== $serviceRequest->user_id) {
            return response()->json([
                'message' => 'غير مصرح لك بتنفيذ هذه العملية'
            ], 403);
        }
        
        // التحقق من إمكانية تغيير الحالة
        if ($quote->status !== 'pending') {
            return response()->json([
                'message' => 'لا يمكن تغيير حالة عرض السعر في الحالة الحالية'
            ], 403);
        }
        
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:accepted,rejected',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'خطأ في البيانات المدخلة',
                'errors' => $validator->errors()
            ], 422);
        }

        // تحديث الحالة
        $oldStatus = $quote->status;
        $quote->status = $request->status;
        $quote->client_notes = $request->notes;
        $quote->save();

        // إرسال إشعار للوكيل السياحي بتغيير الحالة
        $subagent = $quote->subagent;
        $this->notificationService->send(
            $subagent,
            new QuoteStatusChanged($quote, $oldStatus)
        );

        return response()->json([
            'message' => 'تم تحديث حالة عرض السعر بنجاح',
            'quote' => $quote->load(['request', 'currency'])
        ]);
    }

    /**
     * قبول عرض السعر
     *
     * @param  \App\Models\Quote  $quote
     * @return \Illuminate\Http\Response
     */
    public function accept(Quote $quote)
    {
        // التحقق من صلاحية العملية (يجب أن يكون العميل صاحب الطلب)
        $user = auth()->user();
        
        if ($user->role !== 'customer' || $quote->request->user_id !== $user->id) {
            return response()->json([
                'message' => 'غير مصرح لك بقبول هذا العرض'
            ], 403);
        }
        
        // التحقق من أن العرض في حالة انتظار
        if ($quote->status !== 'pending') {
            return response()->json([
                'message' => 'لا يمكن قبول هذا العرض في حالته الحالية'
            ], 422);
        }
        
        // تحديث حالة العرض
        $quote->update(['status' => 'accepted']);
        
        // تحديث حالة الطلب
        $quote->request->update(['status' => 'approved']);
        
        // إرسال إشعار للوكيل
        $quote->user->notify(new QuoteStatusChanged($quote, 'accepted'));
        
        return response()->json([
            'message' => 'تم قبول عرض السعر بنجاح',
            'data' => $quote->fresh(['request', 'user', 'currency'])
        ]);
    }

    /**
     * رفض عرض السعر
     *
     * @param  \App\Models\Quote  $quote
     * @return \Illuminate\Http\Response
     */
    public function reject(Quote $quote)
    {
        // التحقق من صلاحية العملية (يجب أن يكون العميل صاحب الطلب)
        $user = auth()->user();
        
        if ($user->role !== 'customer' || $quote->request->user_id !== $user->id) {
            return response()->json([
                'message' => 'غير مصرح لك برفض هذا العرض'
            ], 403);
        }
        
        // التحقق من أن العرض في حالة انتظار
        if ($quote->status !== 'pending') {
            return response()->json([
                'message' => 'لا يمكن رفض هذا العرض في حالته الحالية'
            ], 422);
        }
        
        // تحديث حالة العرض
        $quote->update(['status' => 'rejected']);
        
        // إرسال إشعار للوكيل
        $quote->user->notify(new QuoteStatusChanged($quote, 'rejected'));
        
        return response()->json([
            'message' => 'تم رفض عرض السعر',
            'data' => $quote->fresh(['request', 'user', 'currency'])
        ]);
    }

    /**
     * Delete a quote
     *
     * @param Quote $quote
     * @return \Illuminate\Http\Response
     */
    public function destroy(Quote $quote)
    {
        // Add authorization logic here
        
        $quote->delete();
        
        return response()->json([
            'message' => 'تم حذف عرض السعر بنجاح'
        ]);
    }
}