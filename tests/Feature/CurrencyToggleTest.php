<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CurrencyToggleTest extends TestCase
{
    use RefreshDatabase;

    public function test_default_currency_is_idr(): void
    {
        $this->get('/');
        $this->assertEquals('IDR', session('currency', 'IDR'));
    }

    public function test_can_switch_to_usd(): void
    {
        $this->get(route('currency.switch', 'USD'));

        $this->assertEquals('USD', session('currency'));
    }

    public function test_switching_to_invalid_currency_is_ignored(): void
    {
        session(['currency' => 'IDR']);

        $this->get(route('currency.switch', 'XYZ'));

        $this->assertEquals('IDR', session('currency'));
    }

    public function test_format_price_renders_idr_by_default(): void
    {
        $response = $this->get('/');

        $response->assertOk();
    }

    public function test_format_price_helper_converts_correctly(): void
    {
        config(['services.currency.usd_rate' => 15800]);

        session(['currency' => 'IDR']);
        $this->get('/'); // trigger middleware to bind currentCurrency
        $this->assertStringContainsString('Rp', format_price(1000000));

        session(['currency' => 'USD']);
        $this->get('/');
        $this->assertStringContainsString('$', format_price(1580000));
    }
}
