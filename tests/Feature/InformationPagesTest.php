<?php

namespace Tests\Feature;

use App\Livewire\Contact;
use App\Models\Setting;
use App\Support\InformationPages;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Tests\TestCase;

class InformationPagesTest extends TestCase
{
    use DatabaseTransactions;

    public function test_information_pages_share_navigation_titles_and_admin_content(): void
    {
        $fields = ['about' => 'introduction', 'chinh-sach-thanh-toan' => 'payment_guide', 'chinh-sach-van-chuyen' => 'shipping_policy', 'chinh-sach-bao-mat' => 'privacy_policy', 'chinh-sach-bao-hanh' => 'warranty_policy', 'chinh-sach-doi-tra-hang-hoan-tien' => 'return_policy'];
        $setting = Setting::query()->firstOrFail();
        foreach ($fields as $path => $field) {
            $setting->setAttribute($field, '<p>Preserved admin content '.$field.'</p>');
        }
        $setting->save();
        foreach (InformationPages::all() as $path => $title) {
            $response = $this->get('/'.$path)->assertOk()->assertSee('info-sidebar')->assertSee('aria-current="page"', false)->assertSee('<h1>'.$title.'</h1>', false)->assertDontSee('class="evo-blog-header"', false);
            if (isset($fields[$path])) {
                $response->assertSee('<p>Preserved admin content '.$fields[$path].'</p>', false);
            } else {
                $response->assertSee('wire:submit.prevent="submit"', false)->assertSee('contact-email');
            }
        }
    }

    public function test_contact_validation_remains_active_without_sending_mail(): void
    {
        Mail::fake();
        Livewire::test(Contact::class)->call('submit')->assertHasErrors(['name', 'email', 'phone', 'body']);
        Mail::assertNothingSent();
    }
}
