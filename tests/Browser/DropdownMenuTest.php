<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class DropdownMenuTest extends DuskTestCase
{
    use DatabaseMigrations;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'role' => 'customer', // Or any role that has the header dropdowns
            'password' => bcrypt('password'),
        ]);
    }

    /**
     * Test user dropdown.
     * @return void
     */
    #[\PHPUnit\Framework\Attributes\Group('dropdown')]
    public function testUserCanOpenUserDropdownMenuInHeader(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                    ->visit('/') // Or the page where the header exists
                    ->waitFor('@user-dropdown-toggle') // Use a specific dusk selector for the trigger
                    ->click('@user-dropdown-toggle')
                    ->waitFor('@user-dropdown-menu') // Use a specific dusk selector for the menu
                    ->assertVisible('@user-dropdown-menu'); // Assert the menu is visible
        });
    }

     /**
     * Test notifications dropdown.
     * @return void
     */
    #[\PHPUnit\Framework\Attributes\Group('dropdown')]
    public function testUserCanOpenNotificationsDropdownMenuInHeader(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                    ->visit('/') // Or the page where the header exists
                    ->waitFor('@notifications-dropdown-toggle') // Use a specific dusk selector
                    ->click('@notifications-dropdown-toggle')
                    ->waitFor('@notifications-dropdown-menu') // Use a specific dusk selector
                    ->assertVisible('@notifications-dropdown-menu');
        });
    }

    /**
     * Test user dropdown menu links.
     * @return void
     */
    #[\PHPUnit\Framework\Attributes\Group('dropdown')]
    public function testUserDropdownMenuShowsProfileAndLogoutLinks(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                    ->visit('/') // Or the page where the header exists
                    ->waitFor('@user-dropdown-toggle')
                    ->click('@user-dropdown-toggle')
                    ->waitFor('@user-dropdown-menu')
                    ->assertVisible('@user-dropdown-menu')
                    // Use specific dusk selectors for the links
                    ->assertSeeIn('@user-dropdown-menu', __('v2.profile_settings')) // Check for profile link text
                    ->assertVisible('@user-dropdown-profile-link') // Check profile link exists
                    ->assertSeeIn('@user-dropdown-menu', __('v2.logout')) // Check for logout button text
                    ->assertVisible('@user-dropdown-logout-btn'); // Check logout button exists
        });
    }
}
