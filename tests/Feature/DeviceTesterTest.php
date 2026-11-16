<?php

namespace Tests\Feature;

use Tests\TestCase;

class DeviceTesterTest extends TestCase
{
    public function test_device_tester_is_public_and_has_both_panels_and_local_assets(): void
    {
        $this->get('/test-thiet-bi')->assertOk()
            ->assertSee('Test chuột & bàn phím', false)
            ->assertSee('id="dt-keyboard"', false)
            ->assertSee('id="dt-mouse"', false)
            ->assertSee('device-tester.js')->assertSee('device-tester.css')
            ->assertSee('polling rate USB');
    }
}
