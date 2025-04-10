<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Parsedown;

class HelpController extends Controller
{
    /**
     * عرض دليل المستخدم المناسب حسب نوع المستخدم
     */
    public function index()
    {
        $user = Auth::user();
        $role = $this->getUserRole();
        $parsedown = new Parsedown();
        
        // تحديد مسار الدليل حسب نوع المستخدم
        switch ($role) {
            case 'admin':
                $guidePath = resource_path('docs/admin-guide.md');
                $title = 'دليل الأدمن';
                break;
            case 'agency':
                $guidePath = resource_path('docs/agency-guide.md');
                $title = 'دليل الوكالة الرئيسية';
                break;
            case 'subagent':
                $guidePath = resource_path('docs/subagent-guide.md');
                $title = 'دليل السبوكيل';
                break;
            case 'customer':
                $guidePath = resource_path('docs/customer-guide.md');
                $title = 'دليل العميل';
                break;
            default:
                $guidePath = resource_path('docs/general-guide.md');
                $title = 'دليل المستخدم';
        }
        
        // التحقق من وجود الملف
        if (!File::exists($guidePath)) {
            return view('help.error', [
                'message' => 'دليل المستخدم غير متوفر حالياً'
            ]);
        }
        
        // قراءة محتوى الدليل وتحويله إلى HTML
        $content = File::get($guidePath);
        $html = $parsedown->text($content);
        
        return view('help.index', [
            'title' => $title,
            'content' => $html,
            'userRole' => $role
        ]);
    }
    
    /**
     * عرض قسم محدد من الدليل
     */
    public function showSection($section)
    {
        $role = $this->getUserRole();
        $parsedown = new Parsedown();
        
        // تحديد مسار الدليل حسب نوع المستخدم
        switch ($role) {
            case 'admin':
                $guidePath = resource_path('docs/admin-guide.md');
                $title = 'دليل الأدمن';
                break;
            case 'agency':
                $guidePath = resource_path('docs/agency-guide.md');
                $title = 'دليل الوكالة الرئيسية';
                break;
            case 'subagent':
                $guidePath = resource_path('docs/subagent-guide.md');
                $title = 'دليل السبوكيل';
                break;
            case 'customer':
                $guidePath = resource_path('docs/customer-guide.md');
                $title = 'دليل العميل';
                break;
            default:
                $guidePath = resource_path('docs/general-guide.md');
                $title = 'دليل المستخدم';
        }
        
        // التحقق من وجود الملف
        if (!File::exists($guidePath)) {
            return view('help.error', [
                'message' => 'دليل المستخدم غير متوفر حالياً'
            ]);
        }
        
        // قراءة محتوى الدليل
        $content = File::get($guidePath);
        
        // بحث عن القسم المطلوب
        $pattern = '/^## ' . preg_quote($section, '/') . '$(.*?)(?=^## |\z)/ms';
        if (preg_match($pattern, $content, $matches)) {
            $sectionContent = $matches[0];
            $html = $parsedown->text($sectionContent);
            
            return view('help.section', [
                'title' => $section,
                'content' => $html,
                'userRole' => $role
            ]);
        }
        
        return redirect()->route('help.index')->with('error', 'القسم المطلوب غير موجود');
    }
    
    /**
     * تحديد نوع المستخدم
     */
    private function getUserRole()
    {
        $user = Auth::user();
        
        if (!$user) {
            return 'guest';
        }
        
        if ($user->is_admin) {
            return 'admin';
        } elseif ($user->role === 'agency') {
            return 'agency';
        } elseif ($user->role === 'subagent') {
            return 'subagent';
        } elseif ($user->role === 'customer') {
            return 'customer';
        }
        
        return 'guest';
    }
    
    /**
     * عرض صفحة البحث في الدليل
     */
    public function search(Request $request)
    {
        $query = $request->get('q', '');
        $role = $this->getUserRole();
        
        // تحديد مسار الدليل حسب نوع المستخدم
        switch ($role) {
            case 'admin':
                $guidePath = resource_path('docs/admin-guide.md');
                break;
            case 'agency':
                $guidePath = resource_path('docs/agency-guide.md');
                break;
            case 'subagent':
                $guidePath = resource_path('docs/subagent-guide.md');
                break;
            case 'customer':
                $guidePath = resource_path('docs/customer-guide.md');
                break;
            default:
                $guidePath = resource_path('docs/general-guide.md');
        }
        
        if (!File::exists($guidePath)) {
            return view('help.search', [
                'query' => $query,
                'results' => [],
                'userRole' => $role
            ]);
        }
        
        $content = File::get($guidePath);
        $results = [];
        
        // البحث عن الكلمات المطلوبة في محتوى الدليل
        if (!empty($query)) {
            $pattern = '/^#+\s+(.*?)$.*?('.preg_quote($query, '/').'.*?)(^#+\s+|$)/mis';
            preg_match_all($pattern, $content, $matches, PREG_SET_ORDER);
            
            foreach ($matches as $match) {
                $sectionTitle = trim($match[1]);
                $snippet = substr(strip_tags($match[2]), 0, 150) . '...';
                
                $results[] = [
                    'title' => $sectionTitle,
                    'snippet' => $snippet,
                    'url' => route('help.section', ['section' => urlencode($sectionTitle)])
                ];
            }
        }
        
        return view('help.search', [
            'query' => $query,
            'results' => $results,
            'userRole' => $role
        ]);
    }
}
