<?php

namespace Tests\Feature;

use App\Livewire\RepairServices;
use App\Mail\RepairRequest;
use App\Models\Setting;
use App\Support\RepairServicesContent;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Livewire;
use RuntimeException;
use Tests\TestCase;

class RepairServicesTest extends TestCase
{
    use DatabaseTransactions;

    private function requestForm()
    {
        return Livewire::test(RepairServices::class)
            ->set('name', 'Khách kiểm thử')
            ->set('phone', '0901234567')
            ->set('deviceType', 'mouse')
            ->set('description', 'Chuột bị double click ở nút trái.');
    }

    public function test_page_keeps_route_four_groups_and_nine_faqs_without_unapproved_prices(): void
    {
        $this->get('/sua-chua-gear')->assertOk()
            ->assertSee('Chăm lại gear.')->assertSee('care-request-form')
            ->assertSee('Liên hệ báo giá')->assertSee('Liên hệ xác nhận')
            ->assertDontSee('0787.955.888')->assertDontSee('0995.521.999')
            ->assertDontSee('90%')->assertDontSee('15–30 phút');
        $this->assertCount(4, RepairServicesContent::groups());
        $this->assertCount(9, RepairServicesContent::faqs());
        foreach (RepairServicesContent::groups() as $group) {
            foreach ($group['items'] as $item) {
                $this->assertNull($item['price_from']);
                $this->assertNull($item['warranty']);
            }
        }
    }

    public function test_required_fields_and_invalid_device_are_rejected_without_mail(): void
    {
        Mail::fake();
        Livewire::test(RepairServices::class)->call('submit')
            ->assertHasErrors(['name', 'phone', 'deviceType', 'description']);
        $this->requestForm()->set('deviceType', 'laptop')->set('phone', 'abc')
            ->call('submit')->assertHasErrors(['phone', 'deviceType']);
        $this->requestForm()->set('phone', '0.........')->call('submit')->assertHasErrors('phone');
        Mail::assertNothingSent();
    }

    public function test_form_sends_to_configured_shop_email_and_prevents_repeat_submission(): void
    {
        Setting::query()->firstOrFail()->update(['email' => 'repair-shop@example.test']);
        Mail::fake();
        $component = $this->requestForm()->call('submit')->assertHasNoErrors()
            ->assertSet('submitted', true)->assertSet('phone', '');
        $component->call('submit');
        Mail::assertSent(RepairRequest::class, fn ($mail) => $mail->hasTo('repair-shop@example.test')
            && $mail->data['deviceType'] === 'mouse' && $mail->data['phone'] === '0901234567');
        Mail::assertSentCount(1);
    }

    public function test_transport_failure_keeps_input_and_shows_retry_message(): void
    {
        Mail::shouldReceive('to')->once()->andThrow(new RuntimeException('Test transport unavailable'));
        $this->requestForm()->call('submit')->assertHasErrors('request')
            ->assertSet('submitted', false)->assertSet('phone', '0901234567')
            ->assertSet('description', 'Chuột bị double click ở nút trái.');
    }

    public function test_missing_recipient_does_not_send_or_claim_success(): void
    {
        Setting::query()->firstOrFail()->update(['email' => '']);
        Mail::fake();
        $this->requestForm()->call('submit')->assertHasErrors('request')->assertSet('submitted', false);
        Mail::assertNothingSent();
    }

    public function test_rate_limit_prevents_delivery(): void
    {
        Mail::fake();
        RateLimiter::shouldReceive('tooManyAttempts')->once()->andReturn(true);
        $this->requestForm()->call('submit')->assertHasErrors('request')->assertSet('submitted', false);
        Mail::assertNothingSent();
    }

    public function test_mail_template_escapes_customer_content(): void
    {
        $mail = new RepairRequest(['name' => '<script>alert(1)</script>', 'phone' => '0901234567',
            'deviceType' => 'keyboard', 'description' => '<img src=x onerror=alert(1)>']);
        $html = $mail->render();
        $this->assertStringNotContainsString('<script>', $html);
        $this->assertStringNotContainsString('<img src=x', $html);
        $this->assertStringContainsString('&lt;script&gt;', $html);
    }
}
