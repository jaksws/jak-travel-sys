<?php

namespace Tests\Feature\Admin;

use App\Models\Request as TravelRequest;
use App\Models\Service;
use App\Models\User;

class AdminRequestManagementTest extends AdminTestCase
{
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
        
        // Create requests with different statuses
        TravelRequest::factory()->count(2)->create(['status' => 'pending']);
        TravelRequest::factory()->count(3)->create(['status' => 'in_progress']);
        TravelRequest::factory()->count(4)->create(['status' => 'completed']);
        
        // Filter by 'in_progress' status
        $response = $this->get(route('admin.requests.index', ['status' => 'in_progress']));
        $response->assertStatus(200);
        
        // Get the requests variable
        $requests = $response->viewData('requests');
        
        // Should only show in_progress requests
        $this->assertEquals(3, $requests->total());
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
        
        // Create services
        $service1 = Service::factory()->create();
        $service2 = Service::factory()->create();
        
        // Create requests for different services
        TravelRequest::factory()->count(2)->create(['service_id' => $service1->id]);
        TravelRequest::factory()->count(3)->create(['service_id' => $service2->id]);
        
        // Filter by service1
        $response = $this->get(route('admin.requests.index', ['service_id' => $service1->id]));
        $response->assertStatus(200);
        
        // Get the requests variable
        $requests = $response->viewData('requests');
        
        // Should only show requests for service1
        $this->assertEquals(2, $requests->total());
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
        
        // Create a request with a unique title
        $uniqueTitle = 'Unique Request Title ' . uniqid();
        TravelRequest::factory()->create(['title' => $uniqueTitle]);
        
        // Create some other requests
        TravelRequest::factory()->count(3)->create();
        
        // Search for the unique title
        $response = $this->get(route('admin.requests.index', ['search' => $uniqueTitle]));
        $response->assertStatus(200);
        
        // Get the requests variable
        $requests = $response->viewData('requests');
        
        // Should only show the request with the unique title
        $this->assertEquals(1, $requests->total());
        $this->assertEquals($uniqueTitle, $requests->first()->title);
    }
    
    /**
     * Test that requests are sorted correctly by default (latest first).
     *
     * @return void
     */
    public function test_requests_are_sorted_by_created_at_desc_by_default()
    {
        $this->loginAsAdmin();
        
        // Create requests with different creation dates
        TravelRequest::factory()->create(['created_at' => now()->subDays(2)]);
        TravelRequest::factory()->create(['created_at' => now()->subDays(1)]);
        $latestRequest = TravelRequest::factory()->create(['created_at' => now()]);
        
        $response = $this->get(route('admin.requests.index'));
        $response->assertStatus(200);
        
        // Get the requests variable
        $requests = $response->viewData('requests');
        
        // First request should be the latest one
        $this->assertEquals($latestRequest->id, $requests->first()->id);
    }
    
    /**
     * Test that admin can see the services list for filtering.
     *
     * @return void
     */
    public function test_admin_can_see_services_list_for_filtering()
    {
        $this->loginAsAdmin();
        
        // Create some services
        $services = Service::factory()->count(3)->create();
        
        $response = $this->get(route('admin.requests.index'));
        $response->assertStatus(200);
        
        // Check if services are passed to the view
        $response->assertViewHas('services');
        
        $viewServices = $response->viewData('services');
        $this->assertEquals($services->count(), $viewServices->count());
    }
}