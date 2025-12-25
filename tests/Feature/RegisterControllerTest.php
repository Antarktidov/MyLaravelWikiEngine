<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RegisterControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_page_is_accessible(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_user_can_register_and_is_redirected_and_authenticated(): void
    {
        $password = 'secret1234';

        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'testuser@example.com',
            'password' => $password,
            'password_confirmation' => $password,
        ]);

        $response->assertRedirect('/home');

        $this->assertDatabaseHas('users', [
            'email' => 'testuser@example.com',
            'name' => 'Test User',
        ]);

        $user = User::where('email', 'testuser@example.com')->first();
        $this->assertNotNull($user);
        $this->assertTrue(Hash::check($password, $user->password));
        $this->assertAuthenticatedAs($user);
    }

    public function test_registration_validation_errors_are_returned(): void
    {
        $response = $this->post('/register', []);

        $response->assertSessionHasErrors(['name', 'email', 'password']);
    }

    public function test_cannot_register_with_existing_email(): void
    {
        User::factory()->create(['email' => 'exists@example.com']);

        $response = $this->post('/register', [
            'name' => 'Another User',
            'email' => 'exists@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');

        $this->assertEquals(1, User::where('email', 'exists@example.com')->count());
    }
}
