<?php

namespace Tests\Browser;

use App\Models\User;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\DuskTestCase;

class DropdownMenuTest extends DuskTestCase
{
    use DatabaseMigrations;

    /** @test */
    public function user_can_open_user_dropdown_menu_in_header()
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'testuser@example.com',
            'password' => bcrypt('password'),
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/')
                ->pause(500)
                ->click('@user-dropdown-toggle')
                ->pause(500)
                ->assertVisible('@user-dropdown-menu');
        });
    }

    /** @test */
    public function user_can_open_notifications_dropdown_menu_in_header()
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'testuser2@example.com',
            'password' => bcrypt('password'),
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/')
                ->pause(500)
                ->click('@notifications-dropdown-toggle')
                ->pause(500)
                ->assertVisible('@notifications-dropdown-menu');
        });
    }
}
