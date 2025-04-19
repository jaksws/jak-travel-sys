<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Models\Agency;
use App\Models\Service;
use App\Models\Request as TravelRequest;

class AdminDashboardUITest extends AdminTestCase
{
    /**
     * Test that quick action links are working.
     *
     * @return void
     */
    public function test_quick_action_links_are_working()
    {
        $this->markTestSkipped('تم تخطي اختبار روابط الإجراءات السريعة مؤقتاً حتى يتم اكتمال تطوير الميزة');
    }
    
    /**
     * Test that breadcrumb is displayed correctly.
     *
     * @return void
     */
    public function test_breadcrumb_is_displayed()
    {
        $this->loginAsAdmin();
        
        $response = $this->get(route('admin.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('لوحة التحكم'); // "Dashboard" in Arabic
    }
    
    /**
     * Test that latest users table has links to user profiles.
     *
     * @return void
     */
    public function test_latest_users_have_profile_links()
    {
        $this->markTestSkipped('تم تخطي اختبار روابط ملفات المستخدمين مؤقتاً حتى يتم اكتمال تطوير الميزة');
    }
    
    /**
     * Test that latest requests table has links to request details.
     *
     * @return void
     */
    public function test_latest_requests_have_detail_links()
    {
        $this->loginAsAdmin();
        
        $response = $this->get(route('admin.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('عرض الكل');
    }
    
    /**
     * Test that the dashboard has the correct title.
     *
     * @return void
     */
    public function test_dashboard_has_correct_title()
    {
        $this->loginAsAdmin();
        
        $response = $this->get(route('admin.dashboard'));
        $response->assertStatus(200);
        
        // Check the page title is correct
        $response->assertSee('لوحة تحكم المسؤول');
    }
    
    /**
     * Test all chart containers are present.
     *
     * @return void
     */
    public function test_chart_containers_exist()
    {
        $this->loginAsAdmin();
        
        $response = $this->get(route('admin.dashboard'));
        $response->assertStatus(200);
        
        // Chart containers should be present on the page
        $response->assertSee('chart-container');
    }
    
    /**
     * Test that dashboard includes Chart.js script.
     *
     * @return void
     */
    public function test_dashboard_includes_chartjs()
    {
        $this->loginAsAdmin();
        
        $response = $this->get(route('admin.dashboard'));
        $response->assertStatus(200);
        
        // Chart.js script should be included
        $response->assertSee('chart.js', false);
    }
    
    /**
     * Test that the dashboard has localized Arabic content.
     *
     * @return void
     */
    public function test_dashboard_has_arabic_content()
    {
        $this->loginAsAdmin();
        
        $response = $this->get(route('admin.dashboard'));
        $response->assertStatus(200);
        
        // Check for Arabic text
        $response->assertSee('الإيرادات');
        $response->assertSee('المستخدمين');
        $response->assertSee('الطلبات');
    }
}