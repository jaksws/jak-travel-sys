<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Agency;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class UserTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_create_a_user()
    {
        $user = User::factory()->create([
            'name' => 'محمد أحمد',
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
            'role' => 'admin'
        ]);

        $this->assertDatabaseHas('users', [
            'name' => 'محمد أحمد',
            'email' => 'test@example.com',
            'role' => 'admin'
        ]);
    }

    #[Test]
    public function it_checks_user_roles()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $agent = User::factory()->create(['role' => 'agent']);
        $subagent = User::factory()->create(['role' => 'subagent']);
        $client = User::factory()->create(['role' => 'client']);

        $this->assertTrue($admin->isAdmin());
        $this->assertFalse($admin->isAgent());

        $this->assertTrue($agent->isAgent());
        $this->assertFalse($agent->isSubagent());

        $this->assertTrue($subagent->isSubagent());
        $this->assertFalse($subagent->isClient());

        $this->assertTrue($client->isClient());
        $this->assertFalse($client->isAdmin());
    }

    #[Test]
    public function it_tests_relationship_with_agency()
    {
        $agency = Agency::factory()->create();
        $user = User::factory()->create([
            'agency_id' => $agency->id
        ]);

        $this->assertEquals($agency->id, $user->agency->id);
    }
}