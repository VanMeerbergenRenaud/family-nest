<?php

namespace Tests\Feature\Profile;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;
use App\Livewire\Pages\Settings\Profile;

class ProfilePasswordUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_password_can_be_updated(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('password'),
        ]);

        $this->actingAs($user);

        Livewire::test(Profile::class)
            ->set('passwordForm.current_password', 'password')
            ->set('passwordForm.password', 'new-password')
            ->set('passwordForm.password_confirmation', 'new-password')
            ->call('updatePassword');

        $this->assertTrue(Hash::check('new-password', $user->refresh()->password));
    }

    public function test_correct_password_must_be_provided_to_update_password(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('password'),
        ]);

        $this->actingAs($user);

        Livewire::test(Profile::class)
            ->set('passwordForm.current_password', 'wrong-password')
            ->set('passwordForm.password', 'new-password')
            ->set('passwordForm.password_confirmation', 'new-password')
            ->call('updatePassword')
            ->assertHasErrors(['passwordForm.current_password']);
    }

    public function test_user_can_reset_password_form(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $component = Livewire::test(Profile::class)
            ->set('passwordForm.current_password', 'some-password')
            ->set('passwordForm.password', 'new-password')
            ->set('passwordForm.password_confirmation', 'new-password');

        $previousData = [
            'current_password' => $component->get('passwordForm.current_password'),
            'password' => $component->get('passwordForm.password'),
            'password_confirmation' => $component->get('passwordForm.password_confirmation')
        ];

        $component->call('cancelPasswordEdit');

        $this->assertNotEquals($previousData['current_password'], $component->get('passwordForm.current_password'));
        $this->assertNotEquals($previousData['password'], $component->get('passwordForm.password'));
        $this->assertNotEquals($previousData['password_confirmation'], $component->get('passwordForm.password_confirmation'));

        $this->assertEmpty($component->get('passwordForm.current_password'));
        $this->assertEmpty($component->get('passwordForm.password'));
        $this->assertEmpty($component->get('passwordForm.password_confirmation'));
    }
}
