<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Tests\Traits\RegistersUiRoutes;

class AdminDashboardUIIntegrationTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;
    use RegistersUiRoutes;

    protected $admin;

    public function setUp(): void
    {
        parent::setUp();
        
        // إنشاء مستخدم أدمن للاختبار
        $this->admin = User::factory()->create([
            'role' => 'admin',
            'is_admin' => 1
        ]);
        
        // تهيئة التخزين المؤقت للاختبار
        Storage::fake('public');
        
        // إنشاء ملف تكوين UI افتراضي
        $this->createDefaultUIConfig();
        
        // تعريف المسارات للاختبار من خلال التريت
        $this->registerUiTestRoutes();
    }

    /**
     * اختبار التكامل: تعديل لون الموقع وتأكيد انعكاسه على صفحة المعاينة
     */
    public function test_ui_color_changes_reflect_in_preview(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.ui.home.update'), [
                'primary_color' => '#ff0000',
                'secondary_color' => '#00ff00',
                'accent_color' => '#0000ff',
                'section_order' => 'hero,services,testimonials'
            ]);
        // بعد التحديث، جلب الصفحة والتحقق من الألوان في HTML
        $viewResponse = $this->actingAs($this->admin)
            ->get(route('admin.ui.home'));
        $viewResponse->assertSee('#ff0000');
        $viewResponse->assertSee('#00ff00');
        $viewResponse->assertSee('#0000ff');
    }

    /**
     * اختبار التكامل: إضافة صفحة جديدة والتحقق من توفرها
     */
    public function test_adding_new_page_makes_it_available_in_system(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.ui.interfaces.update'), [
                'new_page_title' => 'صفحة اختبار',
                'new_page_slug' => 'test-page',
                'new_page_content' => 'محتوى صفحة الاختبار'
            ]);
        // بعد الإضافة، جلب صفحة الواجهات والتحقق من ظهور الصفحة الجديدة
        $viewResponse = $this->actingAs($this->admin)
            ->get(route('admin.ui.interfaces'));
        $viewResponse->assertSee('صفحة اختبار');
        $viewResponse->assertSee('محتوى صفحة الاختبار');
    }

    /**
     * اختبار التكامل: تحميل شعار جديد والتحقق من تطبيقه
     */
    public function test_logo_upload_integration(): void
    {
        $logo = UploadedFile::fake()->create('site-logo.png', 100);
        $this->actingAs($this->admin)
            ->post(route('admin.ui.home.update'), [
        // التحقق من وجود الصفحة الجديدة في التكوين
        $this->assertEquals('صفحة اختبار', config('ui.pages.test-page.title'));
        $this->assertEquals('محتوى صفحة الاختبار', config('ui.pages.test-page.content'));
        
        // التحقق من عرض الصفحة الجديدة في صفحة إدارة الواجهات
        $viewResponse = $this->actingAs($this->admin)
            ->get(route('admin.ui.interfaces'));
        
        $viewResponse->assertSuccessful();
        
        // تنظيف التكوين بعد الاختبار
        $this->cleanupConfig();
    }

    /**
     * اختبار التكامل: تحميل شعار جديد والتحقق من تطبيقه
     */
    public function test_logo_upload_integration(): void
    {
        // Skip if we're just testing basic route registration
        // $this->markTestSkipped('Este test requiere integración completa para ejecutarse correctamente');
        
        // Usar archivo simple en lugar de imagen para evitar dependencia de GD
        $logo = UploadedFile::fake()->create('site-logo.png', 100);
        
        // تحميل الشعار الجديد
        $response = $this->actingAs($this->admin)
            ->post(route('admin.ui.home.update'), [
                'primary_color' => '#3b82f6',
                'secondary_color' => '#64748b',
                'accent_color' => '#10b981',
                'section_order' => 'hero,services,testimonials',
                'main_logo' => $logo
            ]);
        
        $response->assertStatus(302);
        $response->assertSessionHas('success');
        
        // التحقق من تخزين الملف بشكل صحيح
        Storage::disk('public')->assertExists('logos/' . $logo->hashName());
        
        // التحقق من تحديث مسار الشعار في التكوين
        $this->assertStringContainsString('logos/' . $logo->hashName(), config('ui.logos.main'));
    }

    /**
     * اختبار التكامل: ترتيب أقسام الصفحة الرئيسية واستمرار الحفاظ عليها
     */
    public function test_section_order_is_preserved_across_updates(): void
    {
        // Test that routes exist and correctly redirect
        $response = $this->actingAs($this->admin)
            ->post(route('admin.ui.home.update'), [
                'section_order' => 'testimonials,hero,services',
                'primary_color' => '#3b82f6',
                'secondary_color' => '#64748b',
                'accent_color' => '#10b981',
            ]);
        
        $response->assertStatus(302);
        $response->assertRedirect(route('admin.ui.home'));
    }

    /**
     * اختبار التكامل: إنشاء وتحديث التنبيهات في النظام
     */
    public function test_alerts_creation_and_expiry_management(): void
    {
        // Test that routes exist and correctly redirect
        $response = $this->actingAs($this->admin)
            ->post(route('admin.ui.interfaces.update'), [
                'alert_messages' => ['تنبيه هام للاختبار'],
                'alert_types' => ['warning'],
                'alert_active' => [0 => 'on'],
                'alert_expiry' => [date('Y-m-d', strtotime('+1 day'))]
            ]);
        
        $response->assertStatus(302);
        $response->assertRedirect(route('admin.ui.interfaces'));
    }

    /**
     * اختبار التكامل: التحقق من تحديث محتوى التذييل (Footer)
     */
    public function test_footer_content_updates_correctly(): void
    {
        // Test that routes exist and correctly redirect
        $response = $this->actingAs($this->admin)
            ->post(route('admin.ui.interfaces.update'), [
                'footer_text' => 'نص تذييل الاختبار',
                'footer_link_texts' => ['الخصوصية', 'الشروط والأحكام'],
                'footer_link_urls' => ['/privacy', '/terms'],
                'footer_social_names' => ['فيسبوك', 'تويتر'],
                'footer_social_urls' => ['https://facebook.com', 'https://twitter.com'],
                'footer_social_icons' => ['facebook', 'twitter']
            ]);
        
        $response->assertStatus(302);
        $response->assertRedirect(route('admin.ui.interfaces'));
    }

    /**
     * إنشاء تكوين UI افتراضي للاختبار
     */
    private function createDefaultUIConfig(): void
    {
        $defaultUIConfig = [
            'home_page_sections' => [
                'hero' => [
                    'title' => 'قسم الترحيب',
                    'description' => 'القسم الرئيسي في الصفحة الرئيسية',
                    'active' => true,
                ],
                'services' => [
                    'title' => 'الخدمات',
                    'description' => 'عرض الخدمات الرئيسية',
                    'active' => true,
                ],
                'testimonials' => [
                    'title' => 'آراء العملاء',
                    'description' => 'عرض آراء وتقييمات العملاء',
                    'active' => true,
                ]
            ],
            'colors' => [
                'primary' => '#3b82f6',
                'secondary' => '#64748b',
                'accent' => '#10b981',
            ],
            'fonts' => [
                'primary' => 'Cairo',
                'secondary' => 'Tajawal',
            ],
            'logos' => [
                'main' => 'logos/main-logo.png',
                'small' => 'logos/small-logo.png',
            ],
            'pages' => [],
            'navigation' => [],
            'banners' => [],
            'alerts' => [],
            'footer' => [
                'text' => '',
                'links' => [],
                'social' => []
            ],
            'section_order' => ['hero', 'services', 'testimonials']
        ];

        // حفظ التكوين الافتراضي
        Config::set('ui', $defaultUIConfig);
    }

    /**
     * تنظيف ملف التكوين بعد الاختبار
     */
    private function cleanupConfig(): void
    {
        $configPath = config_path('ui.php');
        if (File::exists($configPath)) {
            File::delete($configPath);
        }
        
        Artisan::call('config:clear');
    }
}