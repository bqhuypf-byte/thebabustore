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
            ->assertSee('Báo giá sau kiểm tra')->assertSee('150.000đ')->assertSee('repair-shop-grid')->assertDontSee('care-service-directory')
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

    public function test_service_selection_prefills_device_and_preserves_customer_description(): void
    {
        Livewire::test(RepairServices::class)->call('chooseService', 'mouse', 0)
            ->assertSet('deviceType', 'mouse')->assertSet('description', 'Cần tư vấn: Thay switch chuột. Model / tình trạng: ')
            ->set('description', 'Tôi dùng G304, nút trái bị double click.')
            ->call('chooseService', 'keyboard', 0)->assertSet('deviceType', 'keyboard')
            ->assertSet('description', 'Tôi dùng G304, nút trái bị double click.');
        Livewire::test(RepairServices::class)->call('chooseService', 'unknown', 999)
            ->assertSet('deviceType', '')->assertSet('description', '');
    }

    public function test_admin_content_is_used_and_unverified_or_unsafe_prices_are_hidden(): void
    {
        $setting = Setting::query()->firstOrFail();
        $groups = RepairServicesContent::defaults();
        $groups[0]['items'][0]['name'] = 'Dịch vụ được chỉnh trong admin';
        $groups[0]['items'][0]['price_from'] = 123456;
        $setting->update(['repair_services' => $groups, 'repair_parts' => [
            ['name' => 'Chưa xác minh', 'price' => 123456, 'verified' => false],
            ['name' => 'URL không an toàn', 'price' => 345678, 'verified' => true, 'source_url' => 'javascript:alert(1)', 'checked_at' => '2026-11-25'],
            ['name' => 'Đã xác minh', 'device' => 'mouse', 'price' => 23456, 'verified' => true, 'source_url' => 'https://example.test/part', 'checked_at' => '2026-11-25'],
        ]]);
        $this->assertSame('Dịch vụ được chỉnh trong admin', RepairServicesContent::groups()[0]['items'][0]['name']);
        $this->assertNull(RepairServicesContent::groups()[0]['items'][0]['price_from']);
        $parts = RepairServicesContent::parts();
        $this->assertNull($parts[0]['price']);
        $this->assertNull($parts[1]['price']);
        $this->assertNull($parts[1]['source_url']);
        $this->assertSame(23456, $parts[2]['price']);
    }

    public function test_admin_can_save_repair_catalog_and_source_prices(): void
    {
        $this->actingAs(\App\Models\User::factory()->create(['is_admin' => true]));
        $setting = Setting::query()->firstOrFail();
        $groups = RepairServicesContent::defaults();
        $groups = [array_merge($groups[0], ['items' => [array_merge($groups[0]['items'][0], ['name' => 'Dịch vụ sửa từ form admin'])]])];
        $setting->update(['repair_services' => $groups, 'repair_parts' => []]);
        Livewire::test(\App\Filament\Pages\RepairCatalog::class)
            ->fillForm(['repair_services' => $groups, 'repair_parts' => RepairServicesContent::partDefaults()])
            ->call('save')->assertHasNoFormErrors();
        $this->assertSame('Dịch vụ sửa từ form admin', $setting->fresh()->repair_services[0]['items'][0]['name']);
        $this->get('/sua-chua-gear')->assertOk()->assertSee('Dịch vụ sửa từ form admin');
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
