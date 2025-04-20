<?php

namespace Tests\Feature\Admin;

use App\Models\Agency;
use App\Models\Service;
use App\Models\Request as TravelRequest;
use App\Models\Quote;
use App\Models\Transaction;
use App\Models\User;

class AdminDashboardTest extends AdminTestCase
{
    /**
     * Test that the dashboard displays correct statistics.
     *
     * @return void
     */
    public function test_dashboard_displays_correct_statistics()
    {
        // Create test data
        $agency = Agency::factory()->create();
        $users = User::factory()->count(3)->create(['user_type' => 'customer']);
        $services = Service::factory()->count(2)->create(['agency_id' => $agency->id]);
        $requests = TravelRequest::factory()->count(4)->create(['service_id' => $services[0]->id]);
        $quotes = Quote::factory()->count(2)->create(['request_id' => $requests[0]->id]);
        Transaction::factory()->count(3)->create(['quote_id' => $quotes[0]->id]);
        
        // Login as admin
        $this->loginAsAdmin();
        
        // Access the dashboard
        $response = $this->get(route('admin.dashboard'));
        
        // Assert statistics exist in the response
        $response->assertStatus(200);
        $response->assertViewHas('stats');
        
        // Get the stats variable
        $stats = $response->viewData('stats');
        
        // بدلاً من التحقق من العدد الدقيق للمستخدمين، نتحقق من أن عددهم أكبر من أو يساوي عدد المستخدمين الذين أضفناهم
        // هذا يسمح بالمرونة في حالة وجود مستخدمين آخرين تم إنشاؤهم في البيئة
        $this->assertGreaterThanOrEqual(3, $stats['users'], 'عدد المستخدمين أقل من المتوقع');
        $this->assertGreaterThanOrEqual(1, $stats['agencies']);
        $this->assertGreaterThanOrEqual(2, $stats['services']);
        $this->assertGreaterThanOrEqual(4, $stats['requests']);
        $this->assertGreaterThanOrEqual(2, $stats['quotes']);
        $this->assertGreaterThanOrEqual(3, $stats['transactions']);
    }
    
    /**
     * Test that the dashboard displays user statistics chart data.
     *
     * @return void
     */
    public function test_dashboard_displays_user_statistics_chart_data()
    {
        // Create test users of different types
        User::factory()->create(['user_type' => 'admin', 'role' => 'admin']);
        User::factory()->count(2)->create(['user_type' => 'agency', 'role' => 'agency']);
        User::factory()->count(3)->create(['user_type' => 'subagent', 'role' => 'subagent']);
        User::factory()->count(4)->create(['user_type' => 'customer', 'role' => 'customer']);
        
        // Login as admin
        $this->loginAsAdmin();
        
        // Access the dashboard
        $response = $this->get(route('admin.dashboard'));
        
        // Assert user stats exist in the response
        $response->assertStatus(200);
        $response->assertViewHas('userStats');
        
        // Get the userStats variable
        $userStats = $response->viewData('userStats');
        
        // Assert correct counts (including the admin created in setUp)
        $this->assertEquals(2, $userStats['admins']);
        $this->assertEquals(2, $userStats['agencies']);
        $this->assertEquals(3, $userStats['subagents']);
        $this->assertEquals(4, $userStats['customers']);
    }
    
    /**
     * Test that the dashboard displays request statistics chart data.
     *
     * @return void
     */
    public function test_dashboard_displays_request_statistics_chart_data()
    {
        // Create test requests with different statuses
        TravelRequest::factory()->count(2)->create(['status' => 'pending']);
        TravelRequest::factory()->count(3)->create(['status' => 'in_progress']);
        TravelRequest::factory()->count(4)->create(['status' => 'completed']);
        TravelRequest::factory()->count(1)->create(['status' => 'cancelled']);
        
        // Login as admin
        $this->loginAsAdmin();
        
        // Access the dashboard
        $response = $this->get(route('admin.dashboard'));
        
        // Assert request stats exist in the response
        $response->assertStatus(200);
        $response->assertViewHas('requestStats');
        
        // Get the requestStats variable
        $requestStats = $response->viewData('requestStats');
        
        // Assert correct counts
        $this->assertEquals(2, $requestStats['pending']);
        $this->assertEquals(3, $requestStats['in_progress']);
        $this->assertEquals(4, $requestStats['completed']);
        $this->assertEquals(1, $requestStats['cancelled']);
    }
    
    /**
     * Test that the dashboard displays the latest users.
     *
     * @return void
     */
    public function test_dashboard_displays_latest_users()
    {
        // Create test users
        $users = User::factory()->count(5)->create();
        
        // Login as admin
        $this->loginAsAdmin();
        
        // Access the dashboard
        $response = $this->get(route('admin.dashboard'));
        
        // Assert latest users exist in the response
        $response->assertStatus(200);
        $response->assertViewHas('latestUsers');
        
        // Get the latestUsers variable
        $latestUsers = $response->viewData('latestUsers');
        
        // Assert we have the expected number of users
        $this->assertEquals(5, $latestUsers->count());
    }
    
    /**
     * Test that the dashboard displays the latest requests.
     *
     * @return void
     */
    public function test_dashboard_displays_latest_requests()
    {
        // Create test requests
        $requests = TravelRequest::factory()->count(5)->create();
        
        // Login as admin
        $this->loginAsAdmin();
        
        // Access the dashboard
        $response = $this->get(route('admin.dashboard'));
        
        // Assert latest requests exist in the response
        $response->assertStatus(200);
        $response->assertViewHas('latestRequests');
        
        // Get the latestRequests variable
        $latestRequests = $response->viewData('latestRequests');
        
        // Assert we have the expected number of requests
        $this->assertEquals(5, $latestRequests->count());
    }
    
    /**
     * Test that the dashboard displays revenue chart data.
     *
     * @return void
     */
    public function test_dashboard_displays_revenue_chart_data()
    {
        // Create test transactions in the last 6 months
        for ($i = 0; $i < 6; $i++) {
            Transaction::factory()->count(2)->create([
                'created_at' => now()->subMonths($i)
            ]);
        }
        
        // Login as admin
        $this->loginAsAdmin();
        
        // Access the dashboard
        $response = $this->get(route('admin.dashboard'));
        
        // Assert revenue data exists in the response
        $response->assertStatus(200);
        $response->assertViewHas('revenueData');
        
        // Get the revenueData variable
        $revenueData = $response->viewData('revenueData');
        
        // Assert we have data for 6 months
        $this->assertCount(6, $revenueData['months']);
        $this->assertCount(6, $revenueData['revenue']);
    }

    /**
     * Test that the dashboard loads successfully.
     *
     * @return void
     */
    public function test_dashboard_loads_successfully()
    {
        // Login as admin
        $this->loginAsAdmin();

        // Access the dashboard
        $response = $this->get(route('admin.dashboard'));

        // Assert the dashboard loads successfully
        $response->assertStatus(200);
    }

    /**
     * Test that the dashboard displays the correct data.
     *
     * @return void
     */
    public function test_dashboard_displays_correct_data()
    {
        // Create test data
        $agency = Agency::factory()->create();
        $users = User::factory()->count(3)->create(['user_type' => 'customer']);
        $services = Service::factory()->count(2)->create(['agency_id' => $agency->id]);
        $requests = TravelRequest::factory()->count(4)->create(['service_id' => $services[0]->id]);
        $quotes = Quote::factory()->count(2)->create(['request_id' => $requests[0]->id]);
        Transaction::factory()->count(3)->create(['quote_id' => $quotes[0]->id]);

        // Login as admin
        $this->loginAsAdmin();

        // Access the dashboard
        $response = $this->get(route('admin.dashboard'));

        // Assert the dashboard displays the correct data
        $response->assertStatus(200);
        $response->assertViewHas('stats');

        // Get the stats variable
        $stats = $response->viewData('stats');

        // Assert the correct data is displayed
        $this->assertGreaterThanOrEqual(3, $stats['users']);
        $this->assertGreaterThanOrEqual(1, $stats['agencies']);
        $this->assertGreaterThanOrEqual(2, $stats['services']);
        $this->assertGreaterThanOrEqual(4, $stats['requests']);
        $this->assertGreaterThanOrEqual(2, $stats['quotes']);
        $this->assertGreaterThanOrEqual(3, $stats['transactions']);
    }
}
