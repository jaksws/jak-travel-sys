<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class AdminTestCase extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $admin;
    
    /**
     * Setup the test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        
        // Create an admin user for testing
        $this->admin = User::factory()->create([
            'role' => 'admin',
            'is_admin' => 1,
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
        ]);
    }

    /**
     * Login as admin
     * 
     * @return void
     */
    protected function loginAsAdmin()
    {
        $this->actingAs($this->admin);
    }
}