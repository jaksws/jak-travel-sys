<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Agency;
use App\Models\Service;
use App\Models\Request as TravelRequest;
use App\Models\Quote;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

class DashboardController extends Controller
{
    /**
     * عرض لوحة تحكم المسؤول الرئيسية
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // إحصائيات النظام
        $stats = [
            'users' => User::count(),
            'agencies' => Agency::count(),
            'services' => Service::count(),
            'requests' => TravelRequest::count(),
            'quotes' => Quote::count(),
            'transactions' => Transaction::count(),
        ];

        // إحصائيات المستخدمين حسب النوع
        $userStats = [
            'admins' => User::whereRole('admin')->count(),
            'agencies' => User::whereRole('agency')->count(),
            'subagents' => User::whereRole('subagent')->count(),
            'customers' => User::whereRole('customer')->count(),
        ];

        // إحصائيات الطلبات حسب الحالة
        $requestStats = [
            'pending' => TravelRequest::whereStatus('pending')->count(),
            'in_progress' => TravelRequest::whereStatus('in_progress')->count(),
            'completed' => TravelRequest::whereStatus('completed')->count(),
            'cancelled' => TravelRequest::whereStatus('cancelled')->count(),
        ];

        // أحدث المستخدمين المسجلين
        $latestUsers = User::orderBy('created_at', 'desc')->take(5)->get();

        // أحدث الطلبات
        $latestRequests = TravelRequest::with('user')->orderBy('created_at', 'desc')->take(5)->get();

        // بيانات الإيرادات للرسم البياني (آخر 6 أشهر)
        $revenueData = $this->getRevenueChartData();

        return view('admin.dashboard', compact(
            'stats', 
            'userStats', 
            'requestStats', 
            'latestUsers', 
            'latestRequests',
            'revenueData'
        ));
    }

    /**
     * الحصول على بيانات الإيرادات للرسم البياني
     * 
     * @return array
     */
    private function getRevenueChartData()
    {
        $months = collect([]);
        $revenue = collect([]);

        // الحصول على بيانات آخر 6 أشهر
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months->push($date->format('M Y'));

            $monthlyRevenue = Transaction::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->sum('amount');

            $revenue->push($monthlyRevenue);
        }

        return [
            'months' => $months,
            'revenue' => $revenue,
        ];
    }

    /**
     * عرض صفحة إدارة المستخدمين
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function users(Request $request)
    {
        $query = User::query();

        // البحث حسب الاسم أو البريد الإلكتروني
        if ($request->has('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('email', 'like', "%{$searchTerm}%");
            });
        }

        // التصفية حسب نوع المستخدم
        if ($request->has('role') && !empty($request->role)) {
            $query->where('role', $request->role);
        }

        // الترتيب
        $orderBy = $request->order_by ?? 'created_at';
        $orderDir = $request->order_dir ?? 'desc';
        $query->orderBy($orderBy, $orderDir);

        $users = $query->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    /**
     * عرض صفحة تفاصيل المستخدم
     * 
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function viewUser($id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.show', compact('user'));
    }

    /**
     * عرض صفحة تعديل المستخدم
     * 
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function editUser($id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }

    /**
     * تحديث بيانات المستخدم
     * 
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,agency,subagent,customer',
            'status' => 'required|in:active,inactive',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;
        $user->status = $request->status;
        
        if ($request->filled('password')) {
            $user->password = bcrypt($request->password);
        }
        
        $user->save();
        
        return redirect()->route('admin.users.index')->with('success', 'تم تحديث بيانات المستخدم بنجاح');
    }

    /**
     * تغيير حالة المستخدم (تفعيل/تعطيل)
     * 
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function toggleUserStatus($id)
    {
        $user = User::findOrFail($id);
        $user->status = $user->status === 'active' ? 'inactive' : 'active';
        $user->save();
        
        return redirect()->back()->with('success', 'تم تغيير حالة المستخدم بنجاح');
    }

    /**
     * عرض صفحة إدارة الطلبات
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function requests(Request $request)
    {
        $query = TravelRequest::with(['user', 'service']);

        // البحث والتصفية
        if ($request->has('search') && !empty($request->search)) {
            $query->where('title', 'like', "%{$request->search}%");
        }

        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }

        if ($request->has('service_id') && !empty($request->service_id)) {
            $query->where('service_id', $request->service_id);
        }

        $requests = $query->orderBy('created_at', 'desc')->paginate(15);
        
        // إضافة معلمات المحددة للاستعلام إلى عنوان الصفحات
        $requests->appends($request->all());
        
        // جلب جميع الخدمات للفلترة
        $services = Service::all();

        return view('admin.requests.index', compact('requests', 'services'));
    }

    /**
     * عرض سجلات النظام
     *
     * @return \Illuminate\View\View
     */
    public function logs()
    {
        $logsPath = storage_path('logs');
        $logFiles = File::files($logsPath);
        
        $selectedLog = request()->log ?? (count($logFiles) > 0 ? basename($logFiles[0]) : null);
        $logContent = null;
        
        if ($selectedLog) {
            $fullPath = $logsPath . '/' . $selectedLog;
            if (File::exists($fullPath)) {
                $logContent = File::get($fullPath);
            }
        }
        
        return view('admin.logs', compact('logFiles', 'selectedLog', 'logContent'));
    }
    
    /**
     * عرض صفحة إعدادات النظام
     * 
     * @return \Illuminate\View\View
     */
    public function settings()
    {
        $settings = [
            'multilingual' => config('v1_features.multilingual'),
            'dark_mode' => config('v1_features.dark_mode'),
            'payment_system' => config('v1_features.payment_system'),
            'enhanced_ui' => config('v1_features.enhanced_ui'),
            'ai_features' => config('v1_features.ai_features'),
        ];
        
        return view('admin.settings', compact('settings'));
    }

    /**
     * تحديث إعدادات النظام
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateSettings(Request $request)
    {
        // تحديث الإعدادات
        $settings = [
            'multilingual' => $request->has('multilingual'),
            'dark_mode' => $request->has('dark_mode'),
            'payment_system' => $request->has('payment_system'),
            'enhanced_ui' => $request->has('enhanced_ui'),
            'ai_features' => $request->has('ai_features'),
        ];
        
        // حفظ الإعدادات في ملف التكوين
        $configPath = config_path('v1_features.php');
        $configContent = "<?php\n\nreturn [\n";
        
        foreach ($settings as $key => $value) {
            $configContent .= "    '{$key}' => " . ($value ? 'true' : 'false') . ",\n";
        }
        
        // الحفاظ على الإعدادات الأخرى
        $currentSettings = config('v1_features');
        foreach ($currentSettings as $key => $value) {
            if (!array_key_exists($key, $settings)) {
                $boolValue = $value ? 'true' : 'false';
                if (!is_bool($value)) {
                    $boolValue = is_array($value) ? var_export($value, true) : "'{$value}'";
                }
                $configContent .= "    '{$key}' => {$boolValue},\n";
            }
        }
        
        $configContent .= "];\n";
        
        file_put_contents($configPath, $configContent);
        
        // مسح ذاكرة التخزين المؤقت للتكوين
        \Artisan::call('config:clear');
        
        return redirect('/admin/settings')->with('success', 'تم تحديث إعدادات النظام بنجاح');
    }
}
