<?php
namespace Tests\Feature;

use App\Livewire\Home;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Livewire;
use Tests\TestCase;

class HomeNewsletterTest extends TestCase
{
    use DatabaseTransactions;

    public function test_newsletter_validates_and_stores_one_normalized_subscription(): void
    {
        $email = 'codex-newsletter-check@example.invalid';
        Livewire::test(Home::class)->set('newsletterEmail', 'invalid')->call('subscribeNewsletter')->assertHasErrors(['newsletterEmail' => 'email']);
        Livewire::test(Home::class)->set('newsletterEmail', strtoupper($email))->call('subscribeNewsletter')->assertHasNoErrors()->assertSet('newsletterSubscribed', true);
        Livewire::test(Home::class)->set('newsletterEmail', $email)->call('subscribeNewsletter')->assertHasNoErrors();
        $this->assertSame(1, DB::table('newsletter_subscriptions')->where('email', $email)->count());
    }

    public function test_newsletter_throttles_repeated_submissions(): void
    {
        $key = 'newsletter:' . hash('sha256', '127.0.0.1');
        RateLimiter::clear($key);
        for ($i = 0; $i < 5; $i++) { RateLimiter::hit($key, 60); }
        Livewire::test(Home::class)->set('newsletterEmail', 'blocked@example.invalid')->call('subscribeNewsletter')->assertHasErrors('newsletterEmail');
        $this->assertFalse(DB::table('newsletter_subscriptions')->where('email', 'blocked@example.invalid')->exists());
        RateLimiter::clear($key);
    }
}