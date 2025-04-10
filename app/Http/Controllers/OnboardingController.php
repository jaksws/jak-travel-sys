<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class OnboardingController extends Controller
{
    /**
     * عرض نصائح الاستخدام للمستخدمين
     */
    public function tips()
    {
        $user = Auth::user();
        $role = $this->getUserRole();
        
        // تحديد النصائح حسب نوع المستخدم
        switch ($role) {
            case 'admin':
                $tips = $this->getAdminTips();
                break;
            case 'agency':
                $tips = $this->getAgencyTips();
                break;
            case 'subagent':
                $tips = $this->getSubagentTips();
                break;
            case 'customer':
                $tips = $this->getCustomerTips();
                break;
            default:
                $tips = $this->getGuestTips();
        }
        
        // إضافة روابط الإجراءات للنصائح
        $tips = $this->addActionLinks($tips, $role);
        
        // إضافة رابط دليل المساعدة الخاص بنوع المستخدم
        $helpLinks = $this->getHelpLinks($role);
        
        return view('onboarding.tips', [
            'tips' => $tips,
            'userRole' => $role,
            'helpUrl' => route('help.index'),
            'helpLinks' => $helpLinks
        ]);
    }
    
    /**
     * إضافة روابط إجراءات للنصائح
     */
    private function addActionLinks($tips, $role)
    {
        foreach ($tips as $key => $tip) {
            switch ($role) {
                case 'admin':
                    if ($tip['title'] === 'إدارة المستخدمين') {
                        $tips[$key]['action_url'] = route('admin.users.index');
                        $tips[$key]['action_text'] = 'إدارة المستخدمين';
                    } elseif ($tip['title'] === 'مراقبة النظام') {
                        $tips[$key]['action_url'] = route('admin.system.logs');
                        $tips[$key]['action_text'] = 'عرض السجلات';
                    }
                    break;
                    
                case 'agency':
                    if ($tip['title'] === 'إدارة السبوكلاء') {
                        $tips[$key]['action_url'] = route('agency.subagents.index');
                        $tips[$key]['action_text'] = 'إدارة السبوكلاء';
                    } elseif ($tip['title'] === 'إدارة الخدمات') {
                        $tips[$key]['action_url'] = route('agency.services.index');
                        $tips[$key]['action_text'] = 'إدارة الخدمات';
                    }
                    break;
                    
                case 'subagent':
                    if ($tip['title'] === 'استعراض الطلبات') {
                        $tips[$key]['action_url'] = route('subagent.requests.index');
                        $tips[$key]['action_text'] = 'عرض الطلبات';
                    } elseif ($tip['title'] === 'تقديم العروض') {
                        $tips[$key]['action_url'] = route('subagent.quotes.index');
                        $tips[$key]['action_text'] = 'عرض العروض';
                    }
                    break;
                    
                case 'customer':
                    if ($tip['title'] === 'استكشاف الخدمات') {
                        $tips[$key]['action_url'] = route('customer.services.index');
                        $tips[$key]['action_text'] = 'عرض الخدمات';
                    } elseif ($tip['title'] === 'تقديم طلب') {
                        $tips[$key]['action_url'] = route('customer.requests.create');
                        $tips[$key]['action_text'] = 'طلب جديد';
                    }
                    break;
            }
        }
        
        return $tips;
    }
    
    /**
     * تحديد روابط المساعدة السريعة حسب نوع المستخدم
     */
    private function getHelpLinks($role)
    {
        $commonLinks = [
            [
                'title' => 'دليل المستخدم الكامل',
                'url' => route('help.index'),
                'icon' => 'fas fa-book'
            ],
            [
                'title' => 'الأسئلة الشائعة',
                'url' => route('help.index') . '?section=faq',
                'icon' => 'fas fa-question-circle'
            ]
        ];
        
        $roleSpecificLinks = [];
        
        switch ($role) {
            case 'admin':
                $roleSpecificLinks = [
                    [
                        'title' => 'إدارة المستخدمين',
                        'url' => route('help.index') . '?section=user-management',
                        'icon' => 'fas fa-users-cog'
                    ],
                    [
                        'title' => 'مراقبة النظام',
                        'url' => route('help.index') . '?section=system-monitoring',
                        'icon' => 'fas fa-tachometer-alt'
                    ]
                ];
                break;
            case 'agency':
                $roleSpecificLinks = [
                    [
                        'title' => 'إدارة السبوكلاء',
                        'url' => route('help.index') . '?section=subagent-management',
                        'icon' => 'fas fa-user-tie'
                    ],
                    [
                        'title' => 'إدارة الخدمات',
                        'url' => route('help.index') . '?section=service-management',
                        'icon' => 'fas fa-briefcase'
                    ]
                ];
                break;
            case 'subagent':
                $roleSpecificLinks = [
                    [
                        'title' => 'تقديم عروض الأسعار',
                        'url' => route('help.index') . '?section=quotation',
                        'icon' => 'fas fa-hand-holding-usd'
                    ],
                    [
                        'title' => 'إدارة الطلبات',
                        'url' => route('help.index') . '?section=request-management',
                        'icon' => 'fas fa-clipboard-list'
                    ]
                ];
                break;
            case 'customer':
                $roleSpecificLinks = [
                    [
                        'title' => 'كيفية طلب خدمة',
                        'url' => route('help.index') . '?section=service-request',
                        'icon' => 'fas fa-file-alt'
                    ],
                    [
                        'title' => 'مراجعة عروض الأسعار',
                        'url' => route('help.index') . '?section=quote-review',
                        'icon' => 'fas fa-balance-scale'
                    ]
                ];
                break;
        }
        
        return array_merge($roleSpecificLinks, $commonLinks);
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
     * نصائح للأدمن
     */
    private function getAdminTips()
    {
        return [
            [
                'title' => 'إدارة المستخدمين',
                'content' => 'يمكنك إدارة جميع أنواع المستخدمين والصلاحيات من لوحة التحكم',
                'icon' => 'fas fa-users-cog',
            ],
            [
                'title' => 'مراقبة النظام',
                'content' => 'استعرض سجلات النظام والأداء من قسم مراقبة النظام',
                'icon' => 'fas fa-tachometer-alt',
            ],
            [
                'title' => 'الإعدادات العامة',
                'content' => 'قم بضبط إعدادات النظام العامة من قسم الإعدادات',
                'icon' => 'fas fa-cog',
            ],
        ];
    }
    
    /**
     * نصائح للوكالة
     */
    private function getAgencyTips()
    {
        return [
            [
                'title' => 'إدارة السبوكلاء',
                'content' => 'أضف وأدر السبوكلاء التابعين لوكالتك لتوزيع العمل بشكل فعال',
                'icon' => 'fas fa-user-tie',
            ],
            [
                'title' => 'إدارة الخدمات',
                'content' => 'قم بإعداد الخدمات التي تقدمها وكالتك مع تحديد الأسعار والتفاصيل',
                'icon' => 'fas fa-briefcase',
            ],
            [
                'title' => 'التقارير',
                'content' => 'استعرض تقارير الأداء والمبيعات للوكالة وللسبوكلاء',
                'icon' => 'fas fa-chart-bar',
            ],
        ];
    }
    
    /**
     * نصائح للسبوكيل
     */
    private function getSubagentTips()
    {
        return [
            [
                'title' => 'استعراض الطلبات',
                'content' => 'يمكنك الاطلاع على الطلبات المتاحة وتقديم عروض الأسعار',
                'icon' => 'fas fa-clipboard-list',
            ],
            [
                'title' => 'تقديم العروض',
                'content' => 'قدم عروض أسعار تنافسية وتابع حالتها بعد تقديمها',
                'icon' => 'fas fa-hand-holding-usd',
            ],
            [
                'title' => 'متابعة الطلبات',
                'content' => 'تابع حالة الطلبات الموافق عليها وحدثها باستمرار',
                'icon' => 'fas fa-tasks',
            ],
        ];
    }
    
    /**
     * نصائح للعملاء
     */
    private function getCustomerTips()
    {
        return [
            [
                'title' => 'استكشاف الخدمات',
                'content' => 'تصفح الخدمات المتاحة واختر ما يناسب احتياجاتك',
                'icon' => 'fas fa-search',
            ],
            [
                'title' => 'تقديم طلب',
                'content' => 'قدم طلب الخدمة مع إرفاق جميع المستندات المطلوبة',
                'icon' => 'fas fa-file-alt',
            ],
            [
                'title' => 'مراجعة العروض',
                'content' => 'قارن بين عروض الأسعار المقدمة واختر العرض المناسب',
                'icon' => 'fas fa-balance-scale',
            ],
        ];
    }
    
    /**
     * نصائح للزوار
     */
    private function getGuestTips()
    {
        return [
            [
                'title' => 'إنشاء حساب',
                'content' => 'قم بإنشاء حساب للوصول إلى جميع خدمات النظام',
                'icon' => 'fas fa-user-plus',
            ],
            [
                'title' => 'استكشاف الخدمات',
                'content' => 'تعرف على الخدمات المتاحة قبل التسجيل',
                'icon' => 'fas fa-info-circle',
            ],
        ];
    }
}
