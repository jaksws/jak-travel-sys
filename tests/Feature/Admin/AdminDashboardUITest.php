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
        $this->loginAsAdmin();
        
        $response = $this->get(route('admin.dashboard'));
        $response->assertStatus(200);
        
        // Test each quick action link redirects to the correct page
        $quickActionRoutes = [
            'admin.users.index',
            'admin.requests.index',
            'admin.system.logs',
            'admin.settings',
        ];
        
        foreach ($quickActionRoutes as $routeName) {
            $response = $this->get(route($routeName));
            $response->assertStatus(200);
        }
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
        $this->loginAsAdmin();
        
        // Create some test users
        $user = User::factory()->create([
            'name' => 'Test User Link',
            'email' => 'test.user.link@example.com'
        ]);
        
        $response = $this->get(route('admin.dashboard'));
        $response->assertStatus(200);
        
        // The dashboard should have a link to the user's profile
        $response->assertSee('Test User Link');
        $response->assertSee(route('admin.users.show', $user->id));
    }
    
    /**
     * Test that latest requests table has links to request details.
     *
     * @return void
     */
    public function test_latest_requests_have_detail_links()
    {
        $this->loginAsAdmin();
        
        // Create a test agency
        $agency = Agency::factory()->create();
        
        // Create a test service
        $service = Service::factory()->create([
            'agency_id' => $agency->id
        ]);
        
        // Create a test request
        $request = TravelRequest::factory()->create([
            'title' => 'Test Request Link',
            'service_id' => $service->id
        ]);
        
        $response = $this->get(route('admin.dashboard'));
        $response->assertStatus(200);
        
        // The dashboard should have a link to the request detail
        $response->assertSee('Test Request Link');
        $response->assertSee(route('admin.requests.index'));
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
        $response->assertSee('userStatsChart');
        $response->assertSee('requestStatusChart');
        $response->assertSee('revenueChart');
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
        $response->assertSee('https://cdn.jsdelivr.net/npm/chart.js');
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
        $arabicTerms = [
            'المستخدمين',
            'الوكالات',
            'الخدمات',
            'الطلبات',
            'عروض الأسعار',
            'المعاملات',
            'إحصائيات المستخدمين',
            'حالة الطلبات',
            'الإيرادات',
            'أحدث المستخدمين',
            'أحدث الطلبات',
            'إجراءات سريعة'
        ];
        
        foreach ($arabicTerms as $term) {
            $response->assertSee($term);
        }
    }
}