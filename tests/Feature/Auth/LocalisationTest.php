<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Lang;
use Tests\TestCase;

/**
 * The store is Bulgarian, so nothing it hands back to a shopper may arrive in
 * the framework's English. The screens themselves are Vue and are covered by
 * reading them; what these cover is the half that is decided on the server -
 * the locale the app runs in, and the messages a failed sign-in produces.
 */
class LocalisationTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_application_runs_in_bulgarian(): void
    {
        $this->assertSame('bg', config('app.locale'));
    }

    /**
     * Deliberately English: only the strings the store can reach are
     * translated, and everything else has to keep resolving rather than
     * printing a raw translation key at the shopper.
     */
    public function test_untranslated_strings_fall_back_to_english(): void
    {
        $this->assertSame('en', config('app.fallback_locale'));

        $this->assertSame(
            'The :attribute field must be accepted.',
            Lang::get('validation.accepted'),
        );
    }

    public function test_a_failed_sign_in_is_reported_in_bulgarian(): void
    {
        $user = User::factory()->create();

        $response = $this->from(route('login'))->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors([
            'email' => 'Тези данни не съвпадат с профил при нас.',
        ]);
    }

    public function test_a_missing_field_is_reported_in_bulgarian(): void
    {
        $response = $this->from(route('login'))->post(route('login.store'), [
            'email' => '',
            'password' => '',
        ]);

        $response->assertSessionHasErrors([
            'email' => 'Полето имейл е задължително.',
        ]);
    }

    public function test_registering_with_a_taken_email_is_reported_in_bulgarian(): void
    {
        $user = User::factory()->create();

        $response = $this->from(route('register'))->post(route('register.store'), [
            'name' => 'Иван Петров',
            'email' => $user->email,
            'password' => 'new-password-1',
            'password_confirmation' => 'new-password-1',
        ]);

        $response->assertSessionHasErrors([
            'email' => 'Вече съществува профил с този имейл.',
        ]);
    }
}
