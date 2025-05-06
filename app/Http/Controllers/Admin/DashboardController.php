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
use Illuminate\Support\Facades\Artisan; // Import Artisan facade

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

        $users = $query->orderBy('created_at', 'desc')->paginate(15);

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
        // ربط السبوكيل بالوكالة المختارة عند اختيار الدور سبوكيل
        if ($request->role === 'subagent' && $request->filled('agency_id')) {
            $user->agency_id = $request->agency_id;
        } elseif ($request->role !== 'subagent') {
            $user->agency_id = null;
        }
        $user->save();

        // تحديث أو إنشاء معلومات الوكالة إذا كان الدور وكالة
        if ($request->role === 'agency' && $request->filled('agency_name')) {
            $agency = $user->agency ?? new \App\Models\Agency();
            $agency->user_id = $user->id;
            $agency->name = $request->agency_name;
            $agency->address = $request->agency_address;
            $agency->phone = $request->agency_phone;
            $agency->license_number = $request->agency_license_number;
            $agency->email = $user->email; // إصلاح: تمرير البريد الإلكتروني للوكالة
            $agency->save();
        }

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
     * إنشاء مستخدم جديد من لوحة تحكم الأدمن
     */
    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|in:admin,agency,subagent,customer',
        ]);
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->role = $request->role;
        $user->status = 'active';
        $user->save();
        return redirect()->route('admin.users.index')->with('success', 'تم إنشاء المستخدم بنجاح');
    }

    /**
     * حذف مستخدم من لوحة تحكم الأدمن
     */
    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'تم حذف المستخدم بنجاح');
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
     * Store a new request created by admin
     */
    public function storeRequest(Request $req)
    {
        $data = $req->validate([
            'service_id' => 'required|exists:services,id',
            'user_id'    => 'required|exists:users,id',
            'title'      => 'required|string|max:255',
            'description'=> 'nullable|string',
            'required_date' => 'nullable|date',
            'notes'      => 'nullable|string',
            'status'     => 'nullable|string',
        ]);
        TravelRequest::create($data);
        return redirect()->route('admin.requests.index');
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
        // Fetch all required settings with defaults
        $settings = [
            // v1_features settings
            'multilingual' => config('v1_features.multilingual', false),
            'dark_mode' => config('v1_features.dark_mode', false),
            'payment_system' => config('v1_features.payment_system', false),
            'enhanced_ui' => config('v1_features.enhanced_ui', false),
            'ai_features' => config('v1_features.ai_features', false),
            'role_based_settings' => config('v1_features.role_based_settings', false),
            'audit_logs' => config('v1_features.audit_logs', false),
            'customizable_themes' => config('v1_features.customizable_themes', false),
            'footer_preview' => config('v1_features.footer_preview', false),
            'drag_and_drop_links' => config('v1_features.drag_and_drop_links', false),
            'additional_contact_methods' => config('v1_features.additional_contact_methods', false),

            // ui settings (footer contact)
            'contact_phone' => config('ui.footer.contact.phone', ''),
            'contact_email' => config('ui.footer.contact.email', ''),
            'contact_address' => config('ui.footer.contact.address', ''),
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
        Log::info('Received settings update request', $request->all());
        Log::info('Footer settings before update', config('ui.footer'));

        $validated = $request->validate([
            // v1_features validation
            'multilingual' => 'nullable|in:on',
            'dark_mode' => 'nullable|in:on',
            'payment_system' => 'nullable|in:on',
            'enhanced_ui' => 'nullable|in:on',
            'ai_features' => 'nullable|in:on',

            // Footer validation
            'footer_text' => 'nullable|string',
            'footer_link_texts.*' => 'nullable|string',
            'footer_link_urls.*' => 'nullable|url',
            'footer_service_link_texts.*' => 'nullable|string',
            'footer_service_link_urls.*' => 'nullable|url',
            'footer_social_names.*' => 'nullable|string',
            'footer_social_urls.*' => 'nullable|url',
            'footer_social_icons.*' => 'nullable|string',
            'contact_phone' => 'nullable|string',
            'contact_email' => 'nullable|email',
            'contact_address' => 'nullable|string',
        ]);

        // تحديث إعدادات v1_features
        $v1Settings = [
            'multilingual' => $request->has('multilingual'),
            'dark_mode' => $request->has('dark_mode'),
            'payment_system' => $request->has('payment_system'),
            'enhanced_ui' => $request->has('enhanced_ui'),
            'ai_features' => $request->has('ai_features'),
            'role_based_settings' => config('v1_features.role_based_settings', false),
            'audit_logs' => config('v1_features.audit_logs', false),
            'customizable_themes' => config('v1_features.customizable_themes', false),
            'footer_preview' => config('v1_features.footer_preview', false),
            'drag_and_drop_links' => config('v1_features.drag_and_drop_links', false),
            'additional_contact_methods' => config('v1_features.additional_contact_methods', false),
        ];

        Log::info('Processed v1_features settings before saving:', $v1Settings);

        // حفظ الإعدادات في ملف التكوين v1_features.php
        $configPathV1 = config_path('v1_features.php');
        $this->writeConfig($configPathV1, $v1Settings);
        Log::info('Finished writing v1_features.php');

        // تحديث إعدادات الفوتر في ui.php
        $footer = config('ui.footer', []);
        $footer['text'] = $request->input('footer_text', '');

        $existingLinks = $footer['links'] ?? [];
        $footer['links'] = [];
        if ($request->has('footer_link_texts')) {
            foreach ($request->footer_link_texts as $index => $text) {
                if (!empty($text) && isset($request->footer_link_urls[$index]) && !empty($request->footer_link_urls[$index])) {
                    $footer['links'][] = [
                        'text' => $text,
                        'url' => $request->footer_link_urls[$index],
                    ];
                }
            }
        }
        
        $footer['services'] = [];
        if ($request->has('footer_service_link_texts')) {
            foreach ($request->footer_service_link_texts as $index => $text) {
                if (!empty($text) && isset($request->footer_service_link_urls[$index]) && !empty($request->footer_service_link_urls[$index])) {
                    $footer['services'][] = [
                        'text' => $text,
                        'url' => $request->footer_service_link_urls[$index],
                    ];
                }
            }
        }

        $footer['social'] = [];
        if ($request->has('footer_social_names')) {
            foreach ($request->footer_social_names as $index => $name) {
                if (!empty($name) && isset($request->footer_social_urls[$index]) && !empty($request->footer_social_urls[$index])) {
                    $footer['social'][] = [
                        'name' => $name,
                        'url' => $request->footer_social_urls[$index],
                        'icon' => $request->footer_social_icons[$index] ?? 'globe',
                    ];
                }
            }
        }
        
        $footer['contact'] = [
             'phone' => $request->input('contact_phone', ''),
             'email' => $request->input('contact_email', ''),
             'address' => $request->input('contact_address', ''),
        ];

        Log::info('Processed footer settings before saving:', $footer);

        // تحديث ملف ui.php
        $this->updateUIConfig(['footer' => $footer]);

        $newLinks = $footer['links'];

        // حذف الصفحات المرتبطة بالروابط المحذوفة
        foreach ($existingLinks as $existingLink) {
            $existsInNewLinks = collect($newLinks)->firstWhere('url', $existingLink['url']);
            if (!$existsInNewLinks && isset($existingLink['text'])) {
                $pageName = strtolower(str_replace(' ', '-', $existingLink['text']));
                $viewPath = resource_path("views/{$pageName}.blade.php");

                if (File::exists($viewPath)) {
                    File::delete($viewPath);
                    Log::info("Deleted view file: {$viewPath}");
                }

                $routePath = base_path('routes/web.php');
                if (File::exists($routePath)) {
                    $routeContent = File::get($routePath);

                    $quotedPageName = preg_quote($pageName, '/');
                    $pattern = "/Route::view\\(\\s*'\\/{$quotedPageName}'\\s*,\\s*'{$quotedPageName}'\\s*\\)\\s*->name\\(\\s*'{$quotedPageName}'\\s*\\)\\s*;\\s*\\n?/";

                    $newRouteContent = preg_replace($pattern, '', $routeContent);
                    if ($newRouteContent !== $routeContent) {
                        File::put($routePath, $newRouteContent);
                        Log::info("Removed route for page: {$pageName}");
                    }
                }
            }
        }

        // تحديث أو إنشاء الصفحات عند تعديل/إضافة الروابط
        foreach ($newLinks as $newLink) {
            if (isset($newLink['text'])) {
                $pageName = strtolower(str_replace(' ', '-', $newLink['text']));
                $viewPath = resource_path("views/{$pageName}.blade.php");

                if (!File::exists($viewPath)) {
                    $viewContent = "@extends('layouts.app')\n\n@section('title', '{$newLink['text']}')\n\n@section('content')\n<div class=\"container py-5\">\n    <h1 class=\"mb-4\">{$newLink['text']}</h1>\n    <p>This is the {$newLink['text']} page. Add your content here.</p>\n</div>\n@endsection";
                    File::put($viewPath, $viewContent);
                    Log::info("Created view file: {$viewPath}");

                    $routePath = base_path('routes/web.php');
                    if (File::exists($routePath)) {
                        $routeContent = File::get($routePath);
                        $routeDefinition = "Route::view('/{$pageName}', '{$pageName}')->name('{$pageName}');";
                        if (strpos($routeContent, $routeDefinition) === false) {
                            File::append($routePath, "\n" . $routeDefinition);
                            Log::info("Added route for page: {$pageName}");
                        }
                    }
                }
            }
        }

        Log::info('Footer settings after update', $footer);
        Log::info('Settings update completed successfully.');

        Artisan::call('config:clear');

        return redirect('/admin/settings')->with('success', 'تم تحديث إعدادات النظام بنجاح');
    }

    /**
     * تحديث ملف التكوين للواجهة
     * 
     * @param array $data
     * @return void
     */
    private function updateUIConfig($data)
    {
        $configPath = config_path('ui.php');
        $currentConfig = [];
        
        // جلب الإعدادات الحالية
        if (file_exists($configPath)) {
            // Use require to get the array directly, avoiding potential caching issues with config() helper during update
            $currentConfig = require $configPath;
        }
        
        // دمج الإعدادات الجديدة بشكل متكرر للحفاظ على البنية المتداخلة
        $newConfig = array_replace_recursive($currentConfig, $data);
        
        Log::info('Updating UI config with data:', $data);
        Log::info('Current UI config:', $currentConfig);
        Log::info('New UI config:', $newConfig);

        // Use the helper function to write the config file
        $this->writeConfig($configPath, $newConfig);
        
        // إعادة تحميل الإعدادات
        Artisan::call('config:clear');
    }

    /**
     * Helper function to write configuration file.
     *
     * @param string $path
     * @param array $config
     * @return void
     */
    private function writeConfig(string $path, array $config): void
    {
        $content = "<?php\n\nreturn " . var_export($config, true) . ";\n";
        file_put_contents($path, $content);
    }

    /**
     * Handle role-based settings, audit logs, and customizable themes
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateAdvancedSettings(Request $request)
    {
        // Validate only the settings relevant to this form
        $validated = $request->validate([
            'role_based_settings' => 'nullable|in:on',
            'audit_logs' => 'nullable|in:on',
            'customizable_themes' => 'nullable|in:on',
        ]);

        // Fetch current v1_features config
        $configPath = config_path('v1_features.php');
        $currentSettings = file_exists($configPath) ? require $configPath : [];

        // Update only the relevant settings
        $newSettings = array_merge($currentSettings, [
            'role_based_settings' => $request->has('role_based_settings'),
            'audit_logs' => $request->has('audit_logs'),
            'customizable_themes' => $request->has('customizable_themes'),
        ]);

        // Write the updated config
        $this->writeConfig($configPath, $newSettings);
        Artisan::call('config:clear');

        return redirect()->back()->with('success', 'تم تحديث الإعدادات المتقدمة بنجاح');
    }

    /**
     * Handle live preview, drag-and-drop links, and additional contact methods
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateFooterFeatures(Request $request)
    {
        // Validate only the settings relevant to this form
        $validated = $request->validate([
            'footer_preview' => 'nullable|in:on',
            'drag_and_drop_links' => 'nullable|in:on',
            'additional_contact_methods' => 'nullable|in:on',
        ]);

        // Fetch current v1_features config
        $configPath = config_path('v1_features.php');
        $currentSettings = file_exists($configPath) ? require $configPath : [];

        // Update only the relevant settings
        $newSettings = array_merge($currentSettings, [
            'footer_preview' => $request->has('footer_preview'),
            'drag_and_drop_links' => $request->has('drag_and_drop_links'),
            'additional_contact_methods' => $request->has('additional_contact_methods'),
        ]);

        // Write the updated config
        $this->writeConfig($configPath, $newSettings);
        Artisan::call('config:clear');

        return redirect()->back()->with('success', 'تم تحديث ميزات الفوتر بنجاح');
    }

    /**
     * Update the home page settings.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateHomePage(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'banner_image' => 'nullable|image|max:2048',
        ]);

        // Logic to update home page settings
        // Example: Save to a configuration file or database

        return redirect()->back()->with('success', 'Home page settings updated successfully.');
    }

    /**
     * Update the interface settings.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateInterfaces(Request $request)
    {
        $validated = $request->validate([
            'theme' => 'required|string|in:light,dark',
            'layout' => 'required|string|in:default,compact',
        ]);

        // Logic to update interface settings
        // Example: Save to a configuration file or database

        return redirect()->back()->with('success', 'Interface settings updated successfully.');
    }
}
