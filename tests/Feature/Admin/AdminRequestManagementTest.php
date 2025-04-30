<?php

namespace Tests\Feature\Admin;

use App\Models\Request as TravelRequest;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminRequestManagementTest extends AdminTestCase
{
    use RefreshDatabase;

    /**
     * Test that admin can view the requests index page.
     *
     * @return void
     */
    public function test_admin_can_view_requests_index()
    {
        $this->loginAsAdmin();
        
        $response = $this->get(route('admin.requests.index'));
        $response->assertStatus(200);
        $response->assertViewIs('admin.requests.index');
    }
    
    /**
     * Test that admin can filter requests by status.
     *
     * @return void
     */
    public function test_admin_can_filter_requests_by_status()
    {
        $this->loginAsAdmin();
        TravelRequest::factory()->count(2)->create(['status' => 'pending']);
        TravelRequest::factory()->count(3)->create(['status' => 'in_progress']);
        TravelRequest::factory()->count(4)->create(['status' => 'completed']);
        $response = $this->get(route('admin.requests.index', ['status' => 'in_progress']));
        $response->assertStatus(200);
        $requests = $response->viewData('requests');
        foreach ($requests as $request) {
            $this->assertEquals('in_progress', $request->status);
        }
    }
    
    /**
     * Test that admin can filter requests by service.
     *
     * @return void
     */
    public function test_admin_can_filter_requests_by_service()
    {
        $this->loginAsAdmin();
        $service1 = Service::factory()->create();
        $service2 = Service::factory()->create();
        TravelRequest::factory()->count(2)->create(['service_id' => $service1->id]);
        TravelRequest::factory()->count(3)->create(['service_id' => $service2->id]);
        $response = $this->get(route('admin.requests.index', ['service_id' => $service1->id]));
        $response->assertStatus(200);
        $requests = $response->viewData('requests');
        foreach ($requests as $request) {
            $this->assertEquals($service1->id, $request->service_id);
        }
    }
    
    /**
     * Test that admin can search for requests by title.
     *
     * @return void
     */
    public function test_admin_can_search_for_requests_by_title()
    {
        $this->loginAsAdmin();
        $uniqueTitle = 'Unique Request Title ' . uniqid();
        $uniqueRequest = TravelRequest::factory()->create(['title' => $uniqueTitle]);
        TravelRequest::factory()->count(3)->create();
        $response = $this->get(route('admin.requests.index', ['search' => $uniqueTitle]));
        $response->assertStatus(200);
        $requests = $response->viewData('requests');
        $this->assertTrue($requests->contains(function($req) use ($uniqueTitle) {
            return $req->title === $uniqueTitle;
        }));
    }
    
    /**
     * Test that requests are sorted correctly by default (latest first).
     *
     * @return void
     */
    public function test_requests_are_sorted_by_created_at_desc_by_default()
    {
        $this->loginAsAdmin();
        
        // خلق طلبات بتواريخ مختلفة
        TravelRequest::factory()->create(['created_at' => now()->subDays(2)]);
        TravelRequest::factory()->create(['created_at' => now()->subDays(1)]);
        $latestRequest = TravelRequest::factory()->create(['created_at' => now()]);
        
        $response = $this->get(route('admin.requests.index'));
        $response->assertStatus(200);
        
        // الحصول على متغير الطلبات
        $requests = $response->viewData('requests');
        
        // التحقق من أن الطلب الأحدث هو الأول إذا تم العثور على أي طلبات
        if ($requests && $requests->count() > 0) {
            $this->assertEquals($latestRequest->id, $requests->first()->id);
        }
    }
    
    /**
     * Test that admin can see the services list for filtering.
     *
     * @return void
     */
    public function test_admin_can_see_services_list_for_filtering()
    {
        $this->loginAsAdmin();
        $services = Service::factory()->count(3)->create();
        $response = $this->get(route('admin.requests.index'));
        $response->assertStatus(200);
        $this->assertDatabaseHas('services', [
            'id' => $services->first()->id
        ]);
        // التحقق من وجود متغير الخدمات في بيانات العرض
        $servicesVar = $response->original->getData()['services'] ?? null;
        $this->assertNotNull($servicesVar);
    }

    /**
     * Test that the request management page loads successfully.
     *
     * @return void
     */
    public function test_request_management_page_loads_successfully()
    {
        $this->loginAsAdmin();

        $response = $this->get(route('admin.requests.index'));
        $response->assertStatus(200);
    }

    /**
     * Test that the request management page displays the correct data.
     *
     * @return void
     */
    public function test_request_management_page_displays_correct_data()
    {
        $this->loginAsAdmin();

        // Create test data
        $service = Service::factory()->create();
        $requests = TravelRequest::factory()->count(5)->create(['service_id' => $service->id]);

        $response = $this->get(route('admin.requests.index'));
        $response->assertStatus(200);
        $response->assertViewHas('requests');

        // Get the requests variable
        $viewRequests = $response->viewData('requests');

        // Assert the correct data is displayed
        $this->assertCount(5, $viewRequests);
        foreach ($requests as $request) {
            $this->assertTrue($viewRequests->contains($request));
        }
    }

    /**
     * Test that admin can create a new request with all necessary columns.
     *
     * @return void
     */
    public function test_admin_can_create_request_with_all_columns()
    {
        $this->loginAsAdmin();

        $service = Service::factory()->create();
        $user = User::factory()->create();

        $requestData = [
            'service_id' => $service->id,
            'user_id' => $user->id,
            'title' => 'Test Request Title',
            'description' => 'Test Request Description',
            'required_date' => now()->addDays(10)->format('Y-m-d'),
            'notes' => 'Test Request Notes',
            'status' => 'pending'
        ];

        $response = $this->post(route('admin.requests.store'), $requestData);
        $response->assertStatus(302);
        $response->assertRedirect(route('admin.requests.index'));

        $this->assertDatabaseHas('requests', [
            'service_id' => $service->id,
            'user_id' => $user->id,
            'title' => 'Test Request Title',
            'description' => 'Test Request Description',
            'required_date' => now()->addDays(10)->format('Y-m-d'),
            'notes' => 'Test Request Notes',
            'status' => 'pending'
        ]);
    }
}
