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
        $settings = [
            'multilingual' => config('v1_features.multilingual'),
            'dark_mode' => config('v1_features.dark_mode'),
            'payment_system' => config('v1_features.payment_system'),
            'enhanced_ui' => config('v1_features.enhanced_ui'),
            'ai_features' => config('v1_features.ai_features'),
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
        \Log::info('Received settings update request', $request->all());
        \Log::info('Footer settings before update', config('ui.footer'));

        $validated = $request->validate([
            'multilingual' => 'nullable|in:on',
            'dark_mode' => 'nullable|in:on',
            'payment_system' => 'nullable|in:on',
            'enhanced_ui' => 'nullable|in:on',
            'ai_features' => 'nullable|in:on',
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

        // تحديث الإعدادات
        $settings = [
            'multilingual' => $request->has('multilingual'),
            'dark_mode' => $request->has('dark_mode'),
            'payment_system' => $request->has('payment_system'),
            'enhanced_ui' => $request->has('enhanced_ui'),
            'ai_features' => $request->has('ai_features'),
        ];

        \Log::info('Processed v1_features settings before saving:', $settings);

        // حفظ الإعدادات في ملف التكوين
        $configPath = config_path('v1_features.php');
        $configContent = "<?php\n\nreturn [\n";

        $currentSettings = config('v1_features');
        $mergedSettings = array_merge($currentSettings, $settings);

        foreach ($mergedSettings as $key => $value) {
            if (is_bool($value)) {
                $configContent .= "    '{$key}' => " . ($value ? 'true' : 'false') . ",\n";
            } elseif (is_array($value)) {
                $configContent .= "    '{$key}' => " . var_export($value, true) . ",\n";
            } else {
                $configContent .= "    '{$key}' => '" . addslashes($value) . "',\n";
            }
        }

        $configContent .= "];\n";

        file_put_contents($configPath, $configContent);
        \Log::info('Finished writing v1_features.php');

        // تحديث إعدادات الفوتر
        $footer = config('ui.footer', []);
        $footer['text'] = $request->input('footer_text', '');

        $existingLinks = $footer['links'] ?? [];
        $existingServiceLinks = $footer['services'] ?? [];
        $existingSocialLinks = $footer['social'] ?? [];

        $footer['links'] = [];
        $footer['services'] = [];
        $footer['social'] = [];

        // Rebuild links from request
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

        // Rebuild service links from request
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

        // Rebuild social links from request
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

        \Log::info('Processed footer settings before saving:', $footer);

        // تحديث ملف ui.php
        $this->updateUIConfig([
            'footer' => $footer,
            'contact' => [
                'phone' => $request->input('contact_phone', ''),
                'email' => $request->input('contact_email', ''),
                'address' => $request->input('contact_address', ''),
            ],
        ]);

        // معالجة الحذف والتعديل للروابط والصفحات المرتبطة
        $newLinks = $footer['links'];

        // حذف الصفحات المرتبطة بالروابط المحذوفة
        foreach ($existingLinks as $existingLink) {
            $existsInNewLinks = collect($newLinks)->firstWhere('url', $existingLink['url']);
            if (!$existsInNewLinks && isset($existingLink['text'])) {
                $pageName = strtolower(str_replace(' ', '-', $existingLink['text']));
                $viewPath = resource_path("views/{$pageName}.blade.php");

                if (File::exists($viewPath)) {
                    File::delete($viewPath);
                    \Log::info("Deleted view file: {$viewPath}");
                }

                $routePath = base_path('routes/web.php');
                if (File::exists($routePath)) {
                    $routeContent = File::get($routePath);

                    $quotedPageName = preg_quote($pageName, '/');
                    $pattern = "/Route::view\\(\\s*'\\/{$quotedPageName}'\\s*,\\s*'{$quotedPageName}'\\s*\\)\\s*->name\\(\\s*'{$quotedPageName}'\\s*\\)\\s*;\\s*\\n?/";

                    $newRouteContent = preg_replace($pattern, '', $routeContent);
                    if ($newRouteContent !== $routeContent) {
                        File::put($routePath, $newRouteContent);
                        \Log::info("Removed route for page: {$pageName}");
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
                    \Log::info("Created view file: {$viewPath}");

                    $routePath = base_path('routes/web.php');
                    if (File::exists($routePath)) {
                        $routeContent = File::get($routePath);
                        $routeDefinition = "Route::view('/{$pageName}', '{$pageName}')->name('{$pageName}');";
                        if (strpos($routeContent, $routeDefinition) === false) {
                            File::append($routePath, "\n" . $routeDefinition);
                            \Log::info("Added route for page: {$pageName}");
                        }
                    }
                }
            }
        }

        \Log::info('Footer settings after update', $footer);
        \Log::info('Settings update completed successfully.');

        \Artisan::call('config:clear');

        return redirect('/admin/settings')->with('success', 'تم تحديث إعدادات النظام بنجاح');
    }

    /**
     * عرض صفحة إدارة الصفحة الرئيسية
     * 
     * @return \Illuminate\View\View
     */
    public function homePageManager()
    {
        // جلب بيانات الصفحة الرئيسية
        $homePageSections = config('ui.home_page_sections', []);
        $colors = config('ui.colors', []);
        $fonts = config('ui.fonts', []);
        $logoSettings = config('ui.logos', []);
        
        return view('admin.ui.home_page', compact('homePageSections', 'colors', 'fonts', 'logoSettings'));
    }
    
    /**
     * تحديث بيانات الصفحة الرئيسية
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateHomePage(Request $request)
    {
        // التحقق من البيانات
        $request->validate([
            'sections' => 'array',
            'section_order' => 'string',
            'primary_color' => 'string|max:7',
            'secondary_color' => 'string|max:7',
            'accent_color' => 'string|max:7',
            'font_primary' => 'string|max:100',
            'font_secondary' => 'string|max:100',
        ]);

        // تحديث بيانات الصفحة الرئيسية
        $sections = $request->sections ?? [];
        $sectionOrder = explode(',', $request->section_order);
        
        // تحديث الألوان
        $colors = [
            'primary' => $request->primary_color,
            'secondary' => $request->secondary_color,
            'accent' => $request->accent_color,
        ];
        
        // تحديث الخطوط
        $fonts = [
            'primary' => $request->font_primary,
            'secondary' => $request->font_secondary,
        ];
        
        // معالجة تحميل الشعارات
        $logoSettings = config('ui.logos', []);
        
        if ($request->hasFile('main_logo')) {
            $mainLogo = $request->file('main_logo');
            $mainLogoPath = $mainLogo->store('logos', 'public');
            $logoSettings['main'] = $mainLogoPath;
        }
        
        if ($request->hasFile('small_logo')) {
            $smallLogo = $request->file('small_logo');
            $smallLogoPath = $smallLogo->store('logos', 'public');
            $logoSettings['small'] = $smallLogoPath;
        }
        
        // حفظ الإعدادات في ملف التكوين
        $this->updateUIConfig([
            'home_page_sections' => $sections,
            'section_order' => $sectionOrder,
            'colors' => $colors,
            'fonts' => $fonts,
            'logos' => $logoSettings,
        ]);
        
        return redirect()->route('admin.ui.home')->with('success', 'تم تحديث الصفحة الرئيسية بنجاح');
    }
    
    /**
     * عرض صفحة إدارة واجهات التطبيق
     * 
     * @return \Illuminate\View\View
     */
    public function interfacesManager()
    {
        $navigation = config('ui.navigation', []);
        $pages = config('ui.pages', []);
        $banners = config('ui.banners', []);
        $alerts = config('ui.alerts', []);
        $footer = config('ui.footer', []);
        
        return view('admin.ui.interfaces', compact('navigation', 'pages', 'banners', 'alerts', 'footer'));
    }
    
    /**
     * تحديث واجهات التطبيق
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateInterfaces(Request $request)
    {
        // تحديث معلومات التنقل
        $navigation = $request->navigation ?? [];
        
        // تحديث معلومات الصفحات
        $pages = config('ui.pages', []);
        
        if ($request->has('page_updates')) {
            foreach ($request->page_updates as $id => $data) {
                if (isset($pages[$id])) {
                    $pages[$id]['title'] = $data['title'] ?? $pages[$id]['title'];
                    $pages[$id]['content'] = $data['content'] ?? $pages[$id]['content'];
                    $pages[$id]['active'] = isset($data['active']);
                }
            }
        }
        
        // إضافة صفحة جديدة
        if ($request->filled('new_page_title') && $request->filled('new_page_slug')) {
            $pages[$request->new_page_slug] = [
                'title' => $request->new_page_title,
                'content' => $request->new_page_content ?? '',
                'active' => true,
            ];
        }
        
        // تحديث البانرات
        $banners = [];
        if ($request->has('banner_titles')) {
            foreach ($request->banner_titles as $index => $title) {
                if (!empty($title)) {
                    $banner = [
                        'title' => $title,
                        'content' => $request->banner_contents[$index] ?? '',
                        'active' => isset($request->banner_active[$index]),
                    ];
                    
                    if (isset($request->file('banner_images')[$index])) {
                        $image = $request->file('banner_images')[$index];
                        $path = $image->store('banners', 'public');
                        $banner['image'] = $path;
                    } elseif (isset($request->banner_existing_images[$index])) {
                        $banner['image'] = $request->banner_existing_images[$index];
                    }
                    
                    $banners[] = $banner;
                }
            }
        }
        
        // تحديث التنبيهات
        $alerts = [];
        if ($request->has('alert_messages')) {
            foreach ($request->alert_messages as $index => $message) {
                if (!empty($message)) {
                    $alerts[] = [
                        'message' => $message,
                        'type' => $request->alert_types[$index] ?? 'info',
                        'active' => isset($request->alert_active[$index]),
                        'expiry' => $request->alert_expiry[$index] ?? null,
                    ];
                }
            }
        }
        
        // تحديث معلومات التذييل
        $footer = [
            'text' => $request->footer_text ?? config('ui.footer.text', ''),
            'links' => [],
            'social' => [],
        ];
        
        if ($request->has('footer_link_texts')) {
            foreach ($request->footer_link_texts as $index => $text) {
                if (!empty($text) && !empty($request->footer_link_urls[$index])) {
                    $footer['links'][] = [
                        'text' => $text,
                        'url' => $request->footer_link_urls[$index],
                    ];
                }
            }
        }
        
        if ($request->has('footer_social_names')) {
            foreach ($request->footer_social_names as $index => $name) {
                if (!empty($name) && !empty($request->footer_social_urls[$index])) {
                    $footer['social'][] = [
                        'name' => $name,
                        'url' => $request->footer_social_urls[$index],
                        'icon' => $request->footer_social_icons[$index] ?? 'globe',
                    ];
                }
            }
        }
        
        // حفظ الإعدادات في ملف التكوين
        $this->updateUIConfig([
            'navigation' => $navigation,
            'pages' => $pages,
            'banners' => $banners,
            'alerts' => $alerts,
            'footer' => $footer,
        ]);
        
        return redirect()->route('admin.ui.interfaces')->with('success', 'تم تحديث واجهات التطبيق بنجاح');
    }
    
    /**
     * عرض صفحة التقارير والإحصائيات
     * 
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function analyticsReports(Request $request)
    {
        // الإحصائيات العامة للزيارات
        $visitorStats = [
            'total' => $this->getRandomStat(10000, 50000), // بيانات وهمية للعرض
            'unique' => $this->getRandomStat(5000, 15000),
            'average_time' => rand(120, 360),
            'bounce_rate' => rand(20, 60),
        ];
        
        // إحصائيات الصفحات الأكثر زيارةً
        $topPages = [
            ['path' => '/', 'title' => 'الصفحة الرئيسية', 'visits' => $this->getRandomStat(1000, 5000)],
            ['path' => '/services', 'title' => 'الخدمات', 'visits' => $this->getRandomStat(800, 3000)],
            ['path' => '/contact', 'title' => 'اتصل بنا', 'visits' => $this->getRandomStat(500, 2000)],
            ['path' => '/about', 'title' => 'من نحن', 'visits' => $this->getRandomStat(400, 1500)],
            ['path' => '/blog', 'title' => 'المدونة', 'visits' => $this->getRandomStat(300, 1000)],
        ];
        
        // بيانات المتصفحات
        $browsers = [
            'Chrome' => $this->getRandomStat(40, 60),
            'Firefox' => $this->getRandomStat(10, 20),
            'Safari' => $this->getRandomStat(10, 25),
            'Edge' => $this->getRandomStat(5, 15),
            'Others' => $this->getRandomStat(1, 10),
        ];
        
        // بيانات الأجهزة
        $devices = [
            'Desktop' => $this->getRandomStat(40, 60),
            'Mobile' => $this->getRandomStat(30, 50),
            'Tablet' => $this->getRandomStat(5, 15),
            'Others' => $this->getRandomStat(1, 5),
        ];
        
        // بيانات تحميلات التطبيق
        $downloads = [
            'android' => $this->getRandomStat(1000, 5000),
            'ios' => $this->getRandomStat(800, 4000),
        ];
        
        // معلومات حالة الخادم
        $serverStatus = [
            'uptime' => rand(95, 100) . '%',
            'response_time' => rand(50, 200) . 'ms',
            'memory_usage' => rand(40, 80) . '%',
            'disk_usage' => rand(30, 70) . '%',
        ];
        
        // بيانات الزيارات لآخر 7 أيام
        $visitorsData = [
            'dates' => collect([]),
            'visits' => collect([]),
        ];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $visitorsData['dates']->push($date->format('Y-m-d'));
            $visitorsData['visits']->push($this->getRandomStat(100, 1000));
        }
        
        return view('admin.ui.analytics', compact(
            'visitorStats',
            'topPages',
            'browsers',
            'devices',
            'downloads',
            'serverStatus',
            'visitorsData'
        ));
    }
    
    /**
     * توليد إحصائية عشوائية للعرض
     * 
     * @param int $min
     * @param int $max
     * @return int
     */
    private function getRandomStat($min, $max)
    {
        return rand($min, $max);
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
            $currentConfig = config('ui');
        }
        
        // دمج الإعدادات الجديدة
        $newConfig = array_merge($currentConfig, $data);
        
        \Log::info('Updating UI config with data:', $data);
        \Log::info('Current UI config:', $currentConfig);
        \Log::info('New UI config:', $newConfig);
        \Log::info('UI config after update:', $newConfig);

        // إنشاء محتوى ملف التكوين
        $configContent = "<?php\n\nreturn " . var_export($newConfig, true) . ";\n";
        
        // حفظ ملف التكوين
        file_put_contents($configPath, $configContent);
        
        // إعادة تحميل الإعدادات
        \Artisan::call('config:clear');
    }

    /**
     * Handle footer settings and contact information
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateFooterSettings(Request $request)
    {
        $validated = $request->validate([
            'footer_text' => 'nullable|string',
            'footer_links' => 'nullable|array',
            'footer_services' => 'nullable|array',
            'footer_social' => 'nullable|array',
            'contact_phone' => 'nullable|string',
            'contact_email' => 'nullable|email',
            'contact_address' => 'nullable|string',
        ]);

        $agency = auth()->user()->agency;

        $agency->footer_text = $validated['footer_text'] ?? $agency->footer_text;
        $agency->footer_links = $validated['footer_links'] ?? $agency->footer_links;
        $agency->footer_services = $validated['footer_services'] ?? $agency->footer_services;
        $agency->footer_social = $validated['footer_social'] ?? $agency->footer_social;
        $agency->contact_phone = $validated['contact_phone'] ?? $agency->contact_phone;
        $agency->contact_email = $validated['contact_email'] ?? $agency->contact_email;
        $agency->contact_address = $validated['contact_address'] ?? $agency->contact_address;

        $agency->save();

        return redirect()->back()->with('success', 'تم تحديث إعدادات الفوتر بنجاح');
    }

    /**
     * Handle role-based settings, audit logs, and customizable themes
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateAdvancedSettings(Request $request)
    {
        $validated = $request->validate([
            'role_based_settings' => 'nullable|array',
            'role_based_settings.*' => 'nullable|boolean', // Ensure each value is a boolean
            'audit_logs' => 'nullable|array',
            'audit_logs.*' => 'nullable|boolean', // Ensure each value is a boolean
            'customizable_themes' => 'nullable|array',
            'customizable_themes.*' => 'nullable|string|max:255', // Ensure each value is a string with a max length
        ]);

        $agency = auth()->user()->agency;

        $agency->role_based_settings = $validated['role_based_settings'] ?? $agency->role_based_settings;
        $agency->audit_logs = $validated['audit_logs'] ?? $agency->audit_logs;
        $agency->customizable_themes = $validated['customizable_themes'] ?? $agency->customizable_themes;

        $agency->save();

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
        $validated = $request->validate([
            'footer_preview' => 'nullable|string',
            'drag_and_drop_links' => 'nullable|array',
            'drag_and_drop_links.*' => 'nullable|string|max:255', // Ensure each link is a string with a max length
            'additional_contact_methods' => 'nullable|array',
            'additional_contact_methods.*' => 'nullable|string|max:255', // Ensure each contact method is a string with a max length
        ]);

        $agency = auth()->user()->agency;

        $agency->footer_preview = $validated['footer_preview'] ?? $agency->footer_preview;
        $agency->drag_and_drop_links = $validated['drag_and_drop_links'] ?? $agency->drag_and_drop_links;
        $agency->additional_contact_methods = $validated['additional_contact_methods'] ?? $agency->additional_contact_methods;

        $agency->save();

        return redirect()->back()->with('success', 'تم تحديث ميزات الفوتر بنجاح');
    }
}
