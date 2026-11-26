<?php

namespace Tests\Feature;

use App\Livewire\Login;
use Livewire\Livewire;
use Tests\TestCase;

class ProductionSafetyTest extends TestCase
{
    public function test_demo_login_action_is_unavailable_in_production(): void
    {
        $this->app['env'] = 'production';
        Livewire::test(Login::class)
            ->assertDontSee('wire:click="loginDemoUser"', false)
            ->call('loginDemoUser')
            ->assertStatus(404);
        $this->assertGuest();
    }
}
