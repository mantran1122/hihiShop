<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class MailFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_forgot_password_sends_reset_link_to_customer(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $this->post('/forgot-password', ['email' => $user->email])
            ->assertSessionHasNoErrors();

        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function test_mail_check_command_reports_configuration(): void
    {
        $this->artisan('hihi:mail-check')
            ->expectsOutputToContain('Mailer')
            ->assertSuccessful();
    }

    public function test_mail_check_command_sends_test_email(): void
    {
        Mail::fake();

        $this->artisan('hihi:mail-check', ['email' => 'admin@example.com'])
            ->assertSuccessful();
    }
}
