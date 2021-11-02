<?php

namespace Tests\Feature;

use App\Mail\UserRegistered;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered()
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register()
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(RouteServiceProvider::HOME);
    }

    /** @test */
    public function registered_event_fires_on_registration()
    {
        Event::fake();

        $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        Event::assertDispatched(Registered::class);
    }

    /** @test */
    public function admins_receive_email_when_any_user_is_registered()
    {
        Mail::fake();

        $user = User::factory(['is_admin' => false, 'email' => 'user@example.com'])
            ->create();
        $admin_1 = User::factory(['is_admin' => true, 'email' => 'admin_1@example.com'])
            ->create();
        $admin_2 = User::factory(['is_admin' => true, 'email' => 'admin_2@example.com'])
            ->create();

        event(new Registered($user));

        Mail::assertQueued(UserRegistered::class, function ($mail) use ($admin_1) {
            return $mail->hasTo($admin_1->email);
        });
        Mail::assertQueued(UserRegistered::class, function ($mail) use ($admin_2) {
            return $mail->hasTo($admin_2->email);
        });
    }

    /** @test */
    public function users_dont_receive_email_when_any_user_is_registered()
    {
        Mail::fake();

        $user = User::factory(['is_admin' => false, 'email' => 'user@example.com'])
            ->create();
        $admin = User::factory(['is_admin' => true, 'email' => 'admin@example.com'])
            ->create();

        event(new Registered($user));

        Mail::assertNotQueued(UserRegistered::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email);
        });
    }
}
