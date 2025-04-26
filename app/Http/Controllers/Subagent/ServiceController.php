<?php

namespace App\Http\Controllers\Subagent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\Agency;

class ServiceController extends Controller
{
    /**
     * عرض قائمة الخدمات المتاحة للسبوكيل.
     */
    public function index()
    {
        // الحصول على الخدمات المتاحة للسبوكيل المسجل الدخول
        $services = Service::join('service_subagent', 'services.id', '=', 'service_subagent.service_id')
                        ->where('service_subagent.user_id', auth()->id())
                        ->where('service_subagent.is_active', true)
                        ->where('services.status', 'active')
                        ->select('services.*', 'service_subagent.custom_commission_rate')
                        ->get()
                        ->groupBy('type');
        
        return view('subagent.services.index', compact('services'));
    }

    /**
     * عرض نموذج إضافة خدمة جديدة للسبوكيل.
     */
    public function create()
    {
        return view('subagent.services.create');
    }

    /**
     * عرض تفاصيل خدمة معينة.
     */
    public function show(Service $service)
    {
        // التحقق من أن الخدمة متاحة للسبوكيل
        $serviceSubagent = $service->subagents()
                                ->where('users.id', auth()->id())
                                ->where('service_subagent.is_active', true)
                                ->first();
        
        if (!$serviceSubagent || $service->status !== 'active') {
            abort(403, 'غير مصرح لك بالوصول إلى هذه الخدمة');
        }
        
        // الحصول على تاريخ الطلبات المتعلقة بهذه الخدمة
        $requestsHistory = $service->requests()
                                ->whereHas('quotes', function($query) {
                                    $query->where('subagent_id', auth()->id());
                                })
                                ->with('quotes')
                                ->latest()
                                ->take(10)
                                ->get();
        
        return view('subagent.services.show', compact('service', 'serviceSubagent', 'requestsHistory'));
    }

    /**
     * إضافة خدمة جديدة.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string',
            'description' => 'nullable|string',
            // أضف أي حقول أخرى مطلوبة للخدمة
        ]);

        $user = auth()->user();
        $agencyId = $user->agency_id;
        if (!$agencyId) {
            return back()->withErrors(['agency_id' => 'لا يمكن إضافة خدمة لأن السبوكيل غير مرتبط بأي وكالة.']);
        }

        $service = new \App\Models\Service();
        $service->name = $request->name;
        $service->type = $request->type;
        $service->description = $request->description;
        $service->agency_id = $agencyId; // ربط الخدمة بالوكالة الأساسية
        $service->status = 'active';
        $service->save();

        // ربط الخدمة بالسبوكيل في جدول pivot إذا كان ذلك مطلوباً
        $service->subagents()->attach($user->id);

        return redirect()->route('subagent.services.index')->with('success', 'تمت إضافة الخدمة بنجاح وستظهر للوكالة الأساسية والعملاء.');
    }
}
