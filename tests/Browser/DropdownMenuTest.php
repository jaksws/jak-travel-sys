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

    #[\PHPUnit\Framework\Attributes\Test]
    public function user_dropdown_menu_shows_profile_and_logout_links()
    {
        $user = User::factory()->create([
            'name' => 'Dropdown User',
            'email' => 'dropdownuser@example.com',
            'password' => bcrypt('password'),
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/')
                ->pause(1000) // Pause for initial page load
                ->waitUntil('window.bootstrap !== undefined && window.bootstrap.Dropdown !== undefined', 15) // Wait for Bootstrap JS and Dropdown component
                ->waitFor('@user-dropdown-toggle', 10)
                ->assertVisible('@user-dropdown-toggle')
                ->scrollTo('@user-dropdown-toggle'); // Scroll to it

            // Use script to manually trigger the dropdown show event (single string)
            $browser->script(
                'var toggleElement = document.querySelector("[dusk=\'user-dropdown-toggle\']"); '
                . 'if (toggleElement) { '
                . '  var dropdownInstance = bootstrap.Dropdown.getInstance(toggleElement) || new bootstrap.Dropdown(toggleElement); '
                . '  dropdownInstance.show(); '
                . '} else { console.error("Dusk toggle element not found"); }'
            );

            $browser->pause(1000) // Pause after triggering show
                ->waitFor('@user-dropdown-menu', 10) // Wait for menu visibility
                ->assertVisible('@user-dropdown-menu')
                ->waitFor('@user-dropdown-profile-link', 5)
                ->assertVisible('@user-dropdown-profile-link')
                ->assertVisible('@user-dropdown-logout-btn');
        });
    }
}
