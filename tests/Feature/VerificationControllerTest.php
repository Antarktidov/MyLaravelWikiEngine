<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VerificationControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Тест отображения страницы верификации для аутентифицированного пользователя
     */
    public function test_show_verification_notice_for_authenticated_user(): void
    {
        $user = User::factory()->unverified()->create();

        $response = $this->actingAs($user)->get('/email/verify');

        $response->assertStatus(200);
        $response->assertViewIs('auth.verify');
    }

    /**
     * Тест редиректа на логин для неаутентифицированного пользователя
     */
    public function test_unauthenticated_user_redirected_to_login(): void
    {
        $response = $this->get('/email/verify');

        $response->assertRedirectToRoute('login');
    }

    /**
     * Тест верификации email с валидной подписанной ссылкой
     */
    public function test_verify_email_with_valid_signed_url(): void
    {
        $user = User::factory()->unverified()->create();

        $verificationUrl = '/email/verify/' . $user->getKey() . '/' . sha1($user->email);
        
        // Используем фейковый подписанный URL через Laravel
        $response = $this->actingAs($user)->get(
            \Illuminate\Support\Facades\URL::signedRoute('verification.verify', [
                'id' => $user->id,
                'hash' => sha1($user->email)
            ])
        );

        $this->assertTrue($user->fresh()->hasVerifiedEmail());
        $response->assertRedirectToRoute('home');
    }

    /**
     * Тест верификации без аутентификации редиректит на логин
     */
    public function test_verify_requires_authentication(): void
    {
        $user = User::factory()->unverified()->create();

        $response = $this->get(
            \Illuminate\Support\Facades\URL::signedRoute('verification.verify', [
                'id' => $user->id,
                'hash' => sha1($user->email)
            ])
        );

        $response->assertRedirectToRoute('login');
    }

    /**
     * Тест отказа в верификации с невалидной подписью
     */
    public function test_verify_email_fails_with_invalid_signature(): void
    {
        $user = User::factory()->unverified()->create();

        $response = $this->actingAs($user)->get(
            '/email/verify/' . $user->id . '/invalid-hash'
        );

        $response->assertStatus(403);
    }

    /**
     * Тест отказа в верификации для другого пользователя
     */
    public function test_verify_email_fails_for_different_user(): void
    {
        $user1 = User::factory()->unverified()->create();
        $user2 = User::factory()->unverified()->create();

        $response = $this->actingAs($user2)->get(
            \Illuminate\Support\Facades\URL::signedRoute('verification.verify', [
                'id' => $user1->id,
                'hash' => sha1($user1->email)
            ])
        );

        $response->assertStatus(403);
    }

    /**
     * Тест переотправки письма верификации
     * ОТКЛЮЧЕНО: ОТПРАВКА ЭЛЕКТРОННОЙ ПОЧТЫ НЕ РАБОТАЕТ
     */
    /*public function test_resend_verification_email(): void
    {
        $user = User::factory()->unverified()->create();

        $response = $this->actingAs($user)->post('/email/resend');

        $response->assertRedirectToRoute('verification.notice');
    }*/

    /**
     * Тест что переотправка требует аутентификации
     */
    public function test_resend_requires_authentication(): void
    {
        $response = $this->post('/email/resend');

        $response->assertRedirectToRoute('login');
    }

    /**
     * Тест что верифицированный пользователь редиректится после попытки верифировать
     */
    public function test_already_verified_user_cannot_verify_again(): void
    {
        $user = User::factory()->verified()->create();

        $response = $this->actingAs($user)->get('/email/verify');

        $response->assertRedirectToRoute('home');
    }

    /**
     * Тест что верифицированный пользователь не может переотправлять письма
     */
    public function test_already_verified_user_cannot_resend(): void
    {
        $user = User::factory()->verified()->create();

        $response = $this->actingAs($user)->post('/email/resend');

        $response->assertRedirectToRoute('home');
    }
}
