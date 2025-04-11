<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index()
    {
        // إحصائيات النظام الأساسية
        $stats = [
            'users_count' => User::count(),
            'agencies_count' => $this->getAgenciesCount(),
            'recent_activities' => $this->getRecentActivities(),
        ];
        
        return view('admin.dashboard', compact('stats'));
    }
    
    /**
     * عرض صفحة إدارة المستخدمين
     */
    public function users()
    {
        $users = User::paginate(15);
        return view('admin.users.index', compact('users'));
    }
    
    /**
     * عرض سجلات النظام
     */
    public function logs()
    {
        return view('admin.system.logs');
    }
    
    /**
     * الحصول على عدد الوكالات
     */
    private function getAgenciesCount()
    {
        try {
            return DB::table('agencies')->count();
        } catch (\Exception $e) {
            return 0;
        }
    }
    
    /**
     * الحصول على أحدث النشاطات في النظام
     */
    private function getRecentActivities()
    {
        // يمكن إضافة استعلامات للحصول على أحدث النشاطات
        return [];
    }
}
