<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;
use App\Livewire\Pages\Auth\Register;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response
            ->assertOk()
            ->assertSeeLivewire('pages.auth.register');
    }

    public function test_new_users_can_register(): void
    {
        Livewire::test(Register::class)
            ->set('form.name', 'Test User')
            ->set('form.email', 'test@example.com')
            ->set('form.password', 'password')
            ->call('register')
            ->assertRedirect(route('dashboard', absolute: false));

        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}
