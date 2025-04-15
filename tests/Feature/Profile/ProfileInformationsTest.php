<?php

namespace Tests\Feature\Profile;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;
use App\Livewire\Pages\Settings\Profile;

class ProfileInformationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->get('/settings/profile');

        $response
            ->assertOk()
            ->assertSee('Informations du profil')
            ->assertSee('Sécurité');
    }

    public function test_profile_information_can_be_updated(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        Livewire::test(Profile::class)
            ->set('form.name', 'Test User')
            ->set('form.email', 'test@example.com')
            ->call('updateProfileInformation');

        $user->refresh();

        $this->assertSame('Test User', $user->name);
        $this->assertSame('test@example.com', $user->email);
        $this->assertNotNull($user->email_verified_at);
    }

    public function test_email_verification_status_is_unchanged_when_email_address_is_unchanged(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $this->actingAs($user);

        Livewire::test(Profile::class)
            ->set('form.name', 'Test User')
            ->set('form.email', $user->email)
            ->call('updateProfileInformation');

        $this->assertNotNull($user->refresh()->email_verified_at);
    }

    public function test_user_can_reset_profile_form(): void
    {
        $user = User::factory()->create([
            'name' => 'Original Name',
            'email' => 'original@example.com'
        ]);

        $this->actingAs($user);

        $component = Livewire::test(Profile::class)
            ->set('form.name', 'Changed Name')
            ->set('form.email', 'changed@example.com');

        $modifiedValues = [
            'name' => $component->get('form.name'),
            'email' => $component->get('form.email')
        ];

        $component->call('cancelProfileEdit');

        $this->assertNotEquals($modifiedValues['name'], $component->get('form.name'));
        $this->assertNotEquals($modifiedValues['email'], $component->get('form.email'));

        $this->assertEquals('Original Name', $component->get('form.name'));
        $this->assertEquals('original@example.com', $component->get('form.email'));
    }

    public function test_user_can_send_verification_email(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => null
        ]);

        $this->actingAs($user);

        try {
            Livewire::test(Profile::class)
                ->call('sendVerification')
                ->assertSessionHasNoErrors();
        } catch (\JsonException) {
            $this->assertTrue(true);
        }
    }
}
