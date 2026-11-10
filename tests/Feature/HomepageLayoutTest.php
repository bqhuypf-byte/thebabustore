<?php

namespace Tests\Feature;

use App\Models\Setting;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class HomepageLayoutTest extends TestCase
{
    use DatabaseTransactions;

    public function test_homepage_renders_each_configurable_section_and_hides_disabled_sections(): void
    {
        $setting = Setting::query()->firstOrFail();
        $setting->update(['homepage_sections' => [
            ['layout' => 'split', 'title' => 'Homepage introduction test', 'body' => 'Introduction content'],
            ['layout' => 'banner', 'title' => 'Homepage community test', 'link' => '/shop', 'button_label' => 'Explore test'],
            ['layout' => 'cards', 'title' => 'Homepage cards test', 'items' => [['title' => 'Card item test', 'body' => 'Card body']]],
            ['layout' => 'faq', 'title' => 'Homepage FAQ test', 'items' => [['title' => 'Question test', 'body' => 'Answer test']]],
            ['layout' => 'split', 'title' => 'Hidden section test', 'enabled' => false],
        ]]);

        $response = $this->get('/')->assertOk();
        foreach (['Homepage introduction test', 'Homepage community test', 'Homepage cards test', 'Card item test', 'Homepage FAQ test', 'Answer test', 'home-mchose.css'] as $text) {
            $response->assertSee($text);
        }
        $response->assertDontSee('Hidden section test');
        $this->assertIsArray($setting->fresh()->homepage_sections);
    }

    public function test_editorial_content_is_escaped_and_unknown_layouts_fall_back(): void
    {
        Setting::query()->firstOrFail()->update(['homepage_sections' => [
            ['layout' => 'invalid-layout', 'title' => '<script>alert("test")</script>', 'body' => '<img src=x onerror=alert(1)>'],
        ]]);

        $response = $this->get('/')->assertOk();
        $response->assertSee('mc-layout-split', false);
        $response->assertDontSee('<script>alert("test")</script>', false);
        $response->assertDontSee('<img src=x onerror=alert(1)>', false);
        $response->assertSee('&lt;img src=x onerror=alert(1)&gt;', false);
    }
}