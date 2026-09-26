<?php

namespace Tests\Feature\Settings;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ProfileUpdateTest extends TestCase
{
    use DatabaseTransactions;

    public function test_profile_page_is_displayed()
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get('/settings/profile');

        $response->assertOk();
    }

    public function test_profile_information_can_be_updated()
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/settings/profile', [
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/settings/profile');

        $user->refresh();

        $this->assertSame('Test User', $user->name);
        $this->assertNotSame('test@example.com', $user->email);
        $this->assertNotNull($user->email_verified_at);
    }

    public function test_email_cannot_be_changed_in_profile()
    {
        $user = User::factory()->create();
        $this->actingAs($user)->patch('/settings/profile', ['name' => 'Novo nome', 'email' => 'outro@example.com'])->assertRedirect('/settings/profile');
        $this->assertSame('Novo nome', $user->fresh()->name);
        $this->assertNotSame('outro@example.com', $user->fresh()->email);
    }
}
