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
            'agencies_count' => DB::table('agencies')->count() ?? 0,
            'recent_activities' => $this->getRecentActivities(),
        ];
        
        return view('admin.dashboard', compact('stats'));
    }
    
    public function users()
    {
        $users = User::paginate(15);
        return view('admin.users.index', compact('users'));
    }
    
    public function logs()
    {
        // يمكن تنفيذ عرض سجلات النظام هنا
        return view('admin.system.logs');
    }
    
    private function getRecentActivities()
    {
        // هنا يمكن إضافة استعلامات للحصول على أحدث النشاطات في النظام
        return [];
    }
}
