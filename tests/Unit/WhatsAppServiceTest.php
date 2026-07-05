<?php

namespace Tests\Unit;

use App\Services\WhatsAppService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class WhatsAppServiceTest extends TestCase
{
    public function test_returns_false_gracefully_when_not_configured(): void
    {
        config(['services.fonnte.token' => null]);
        $service = new WhatsAppService();

        $this->assertFalse($service->isConfigured());
        $this->assertFalse($service->send('081234567890', 'Test message'));
    }

    public function test_normalizes_phone_numbers_and_sends_when_configured(): void
    {
        config(['services.fonnte.token' => 'fake-token']);
        Http::fake([
            'api.fonnte.com/*' => Http::response(['status' => true], 200),
        ]);

        $service = new WhatsAppService();
        $result = $service->send('081234567890', 'Test message');

        $this->assertTrue($result);
        Http::assertSent(function ($request) {
            return $request['target'] === '6281234567890'
                && $request['message'] === 'Test message'
                && $request->hasHeader('Authorization', 'fake-token');
        });
    }

    public function test_returns_false_when_fonnte_api_fails(): void
    {
        config(['services.fonnte.token' => 'fake-token']);
        Http::fake([
            'api.fonnte.com/*' => Http::response(['status' => false], 200),
        ]);

        $service = new WhatsAppService();
        $result = $service->send('081234567890', 'Test message');

        $this->assertFalse($result);
    }

    public function test_returns_false_for_invalid_phone_number(): void
    {
        config(['services.fonnte.token' => 'fake-token']);
        $service = new WhatsAppService();

        $this->assertFalse($service->send('not-a-phone', 'Test message'));
    }
}
