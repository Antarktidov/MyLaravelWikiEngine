<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ConfirmPasswordControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Тест: неавторизованный пользователь перенаправляется на страницу входа
     */
    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/password/confirm');

        $response->assertRedirect('/login');
    }

    /**
     * Тест: авторизованный пользователь может просмотреть страницу подтверждения пароля
     */
    public function test_user_can_view_confirm_password_form(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/password/confirm');

        $response->assertStatus(200);
    }

    /**
     * Тест: пользователь может подтвердить пароль с правильным пароль
     */
    public function test_user_can_confirm_password_with_correct_password(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('correct-password'),
        ]);

        $response = $this->actingAs($user)
            ->post('/password/confirm', [
                'password' => 'correct-password',
            ]);

        $response->assertRedirect('/home');
    }

    /**
     * Тест: пользователь не может подтвердить пароль с неправильным пароль
     */
    public function test_user_cannot_confirm_password_with_incorrect_password(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('correct-password'),
        ]);

        $response = $this->actingAs($user)
            ->post('/password/confirm', [
                'password' => 'wrong-password',
            ]);

        $response->assertSessionHasErrors('password');
    }

    /**
     * Тест: пусто поле пароля возвращает ошибку валидации
     */
    public function test_password_field_is_required(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->post('/password/confirm', [
                'password' => '',
            ]);

        $response->assertSessionHasErrors('password');
    }

    /**
     * Тест: пользователь перенаправляется на предполагаемый URL после подтверждения пароля
     */
    public function test_user_is_redirected_to_intended_url_after_password_confirmation(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password'),
        ]);

        $response = $this->actingAs($user)
            ->post('/password/confirm', [
                'password' => 'password',
            ]);

        $response->assertRedirect('/home');
        $this->assertTrue($this->app['auth']->check());
    }

    /**
     * Тест: неавторизованный пользователь не может отправить форму подтверждения пароля
     */
    public function test_guest_cannot_post_to_confirm_password(): void
    {
        $response = $this->post('/password/confirm', [
            'password' => 'password',
        ]);

        $response->assertRedirect('/login');
    }
}
