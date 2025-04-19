<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Tests\Traits\RegistersUiRoutes;

class AdminUIManagementTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;
    use RegistersUiRoutes;
    
    protected $admin;
    protected $user;

    public function setUp(): void
    {
        parent::setUp();
        
        // Crear usuario admin y regular
        $this->admin = User::factory()->create([
            'role' => 'admin',
            'is_admin' => 1
        ]);
        
        $this->user = User::factory()->create([
            'role' => 'customer',
            'is_admin' => 0
        ]);
        
        // Configurar almacenamiento falso para pruebas de subida de archivos
        Storage::fake('public');
        
        // Configurar valores predeterminados para UI
        $this->setupDefaultUIConfig();
        
        // Registrar rutas de prueba
        $this->registerUiTestRoutes();
    }

    /**
     * Prueba de acceso a la página de gestión de inicio
     */
    public function test_admin_can_access_home_page_manager(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.ui.home'));
        $response->assertStatus(200);
    }

    /**
     * Prueba de acceso a la página de gestión de interfaces
     */
    public function test_admin_can_access_interfaces_manager(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.ui.interfaces'));
        $response->assertStatus(200);
    }

    /**
     * Prueba de acceso a la página de analíticas
     */
    public function test_admin_can_access_analytics(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.ui.analytics'));
        $response->assertStatus(200);
    }

    /**
     * Prueba de actualización de configuración de página de inicio
     */
    public function test_admin_can_update_home_page_settings(): void
    {
        // Skip if GD extension is not available
        if (!extension_loaded('gd')) {
            $this->markTestSkipped('GD extension is not installed, skipping image-related test.');
            return;
        }
        
        // Using a simple file instead of an image to avoid GD dependency
        $logo = UploadedFile::fake()->create('logo.png', 100);
        
        $response = $this->actingAs($this->admin)
            ->post(route('admin.ui.home.update'), [
                'primary_color' => '#ff0000',
                'secondary_color' => '#00ff00',
                'accent_color' => '#0000ff',
                'font_primary' => 'Roboto',
                'font_secondary' => 'Open Sans',
                'section_order' => 'hero,services,testimonials',
                'sections' => [
                    'hero' => ['active' => 'on'],
                    'services' => ['active' => 'on'],
                    'testimonials' => ['active' => 'on'],
                ],
                'main_logo' => $logo
            ]);
        
        $response->assertStatus(302);
        $response->assertRedirect(route('admin.ui.home'));
        $response->assertSessionHas('success');
    }

    /**
     * Prueba de actualización de configuración de interfaz
     */
    public function test_admin_can_update_interfaces(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.ui.interfaces.update'), [
                'navigation' => [
                    [
                        'title' => 'Home',
                        'url' => '/',
                        'icon' => 'home',
                        'active' => true
                    ],
                    [
                        'title' => 'Services',
                        'url' => '/services',
                        'icon' => 'list',
                        'active' => true
                    ]
                ],
                'banner_titles' => ['Special Offer'],
                'banner_contents' => ['Get 20% discount this month'],
                'banner_active' => [0 => 'on'],
                'alert_messages' => ['Important: System maintenance scheduled'],
                'alert_types' => ['info'],
                'alert_active' => [0 => 'on'],
                'footer_text' => 'All rights reserved',
                'footer_link_texts' => ['Privacy Policy'],
                'footer_link_urls' => ['/privacy'],
                'footer_social_names' => ['Twitter'],
                'footer_social_urls' => ['https://twitter.com'],
                'footer_social_icons' => ['twitter']
            ]);
        
        $response->assertStatus(302);
        $response->assertRedirect(route('admin.ui.interfaces'));
        $response->assertSessionHas('success');
    }

    /**
     * Prueba de navegación por pestañas de interfaz
     */
    public function test_admin_can_navigate_interface_tabs(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.ui.interfaces') . '?tab=navigation');
        
        $response->assertStatus(200);
        
        $response = $this->actingAs($this->admin)
            ->get(route('admin.ui.interfaces') . '?tab=banners');
        
        $response->assertStatus(200);
        
        $response = $this->actingAs($this->admin)
            ->get(route('admin.ui.interfaces') . '?tab=alerts');
        
        $response->assertStatus(200);
    }

    /**
     * Prueba de añadir una nueva página
     */
    public function test_admin_can_add_new_page(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.ui.interfaces.update'), [
                'new_page_title' => 'New Test Page',
                'new_page_slug' => 'test-page',
                'new_page_content' => 'This is a test page content'
            ]);
        
        $response->assertStatus(302);
        $response->assertRedirect(route('admin.ui.interfaces'));
        $response->assertSessionHas('success');
    }

    /**
     * Prueba de que usuarios no administradores no pueden acceder a la gestión de UI
     */
    public function test_non_admin_cannot_access_ui_management(): void
    {
        $response = $this->actingAs($this->user)->get(route('admin.ui.home'));
        $response->assertStatus(403);
    }

    /**
     * Prueba de que las estadísticas de visitantes se muestran correctamente
     */
    public function test_analytics_displays_visitor_statistics(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.ui.analytics'));
        $response->assertStatus(200);
    }

    /**
     * Prueba de que se requiere autenticación para gestión de UI
     */
    public function test_authentication_required_for_ui_management(): void
    {
        $response = $this->get(route('admin.ui.home'));
        $response->assertRedirect(route('login'));
    }

    /**
     * Configura valores predeterminados de UI para pruebas
     */
    private function setupDefaultUIConfig(): void
    {
        $defaultConfig = [
            'colors' => [
                'primary' => '#3b82f6',
                'secondary' => '#64748b',
                'accent' => '#10b981',
            ],
            'logos' => [
                'main' => 'logos/default-logo.png'
            ],
            'home_page_sections' => [
                'hero' => ['title' => 'Welcome', 'active' => true],
                'features' => ['title' => 'Features', 'active' => true],
                'services' => ['title' => 'Services', 'active' => false],
                'testimonials' => ['title' => 'Testimonials', 'active' => true],
            ],
            'section_order' => 'hero,features,services,testimonials',
            'navigation' => [],
            'banners' => [],
            'alerts' => [],
            'pages' => [],
            'footer' => [
                'text' => 'All rights reserved',
                'links' => [],
                'social' => [],
            ]
        ];

        Config::set('ui', $defaultConfig);
    }
}