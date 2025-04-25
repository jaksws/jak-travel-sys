<?php

namespace Tests\Browser;

use App\Models\User;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\DuskTestCase;

class DropdownMenuTest extends DuskTestCase
{
    use DatabaseMigrations;

    #[\PHPUnit\Framework\Attributes\Test]
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
                ->pause(1500)
                ->assertPresent('@user-dropdown-toggle')
                ->click('@user-dropdown-toggle')
                ->pause(1500)
                ->assertPresent('@user-dropdown-menu');
        });
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function user_can_open_notifications_dropdown_menu_in_header()
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'testuser2@example.com',
            'password' => bcrypt('password'),
        ]);
        // إضافة إشعار تجريبي لهذا المستخدم
        $quote = \App\Models\Quote::factory()->create([
            'user_id' => $user->id,
            'status' => 'pending',
        ]);
        $user->notify(new \App\Notifications\QuoteStatusChanged($quote, 'pending'));

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/')
                ->pause(1500)
                ->assertPresent('@notifications-dropdown-toggle')
                ->click('@notifications-dropdown-toggle')
                ->pause(1500)
                ->assertPresent('@notifications-dropdown-menu');
        });
    }
}
