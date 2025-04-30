<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ToggleFeatureTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function user_can_toggle_dark_mode_preference()
    {
        $user = User::factory()->create([
            'theme' => 'light',
            'email_notifications' => false,
        ]);
        $this->actingAs($user);

        // تفعيل الوضع الليلي
        $response = $this->postJson(route('user.preferences.save'), [
            'theme' => 'dark',
            'locale' => 'ar',
        ]);
        $response->assertJson(['success' => true]);
        $user->refresh();
        $this->assertEquals('dark', $user->theme);

        // تعطيل الوضع الليلي
        $response = $this->postJson(route('user.preferences.save'), [
            'theme' => 'light',
            'locale' => 'ar',
        ]);
        $response->assertJson(['success' => true]);
        $user->refresh();
        $this->assertEquals('light', $user->theme);
    }

    #[Test]
    public function user_can_toggle_email_notifications()
    {
        $user = User::factory()->create([
            'email_notifications' => false,
        ]);
        $this->actingAs($user);

        $response = $this->postJson(route('user.preferences.save'), [
            'email_notifications' => 'on',
            'theme' => 'system',
            'locale' => 'ar',
        ]);
        $response->assertJson(['success' => true]);
        $user->refresh();
        $this->assertTrue((bool)$user->email_notifications);
    }

    #[Test]
    public function admin_can_toggle_user_status()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['status' => 'active']);
        $this->actingAs($admin);

        $response = $this->patch(route('admin.users.toggle-status', $user->id));
        $response->assertRedirect();
        $user->refresh();
        $this->assertEquals('inactive', $user->status);
    }
}
