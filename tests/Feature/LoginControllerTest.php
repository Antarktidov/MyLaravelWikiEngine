<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Тест: неавторизованный пользователь может видеть форму входа
     */
    public function test_guest_can_view_login_form(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    /**
     * Тест: неавторизованный пользователь не может видеть форму входа,
     * если уже авторизован (должен быть редирект на /home)
     */
    public function test_authenticated_user_cannot_view_login_form(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/login');

        $response->assertRedirect('/home');
    }

    /**
     * Тест: пользователь может войти с корректными учетными данными
     */
    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/home');
        $this->assertAuthenticatedAs($user);
    }

    /**
     * Тест: пользователь не может войти с неправильным паролем
     */
    public function test_user_cannot_login_with_invalid_password(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('correct_password'),
        ]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'wrong_password',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    /**
     * Тест: пользователь не может войти с несуществующей почтой
     */
    public function test_user_cannot_login_with_nonexistent_email(): void
    {
        $response = $this->post('/login', [
            'email' => 'nonexistent@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    /**
     * Тест: отправка формы входа без email вызывает ошибку валидации
     */
    public function test_login_requires_email(): void
    {
        $response = $this->post('/login', [
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
    }

    /**
     * Тест: отправка формы входа без пароля вызывает ошибку валидации
     */
    public function test_login_requires_password(): void
    {
        $response = $this->post('/login', [
            'email' => 'test@example.com',
        ]);

        $response->assertSessionHasErrors('password');
    }

    /**
     * Тест: авторизованный пользователь может выйти из системы
     */
    public function test_authenticated_user_can_logout(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);
        $this->assertAuthenticatedAs($user);

        $response = $this->post('/logout');

        $response->assertRedirect();
        $this->assertGuest();
    }

    /**
     * Тест: неавторизованный пользователь не может выйти из системы
     */
    public function test_guest_cannot_logout(): void
    {
        $response = $this->post('/logout');

        $response->assertRedirect();
        $this->assertGuest();
    }

    /**
     * Тест: перенаправление на /home после успешного входа
     */
    public function test_redirects_to_home_after_successful_login(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/home');
    }

    /**
     * Тест: "Помни меня" функция сохраняет сессию
     */
    public function test_remember_me_functionality(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
            'remember' => true,
        ]);

        $response->assertRedirect('/home');
        $this->assertAuthenticatedAs($user);
    }
}
