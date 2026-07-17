<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Services\SiteSettingsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use Tests\TestCase;

class SiteSettingsServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_empty_settings_table_resolves_factory_defaults(): void
    {
        $service = app(SiteSettingsService::class);

        $this->assertSame('#29abe2', $service->get('buttons.primary_background'));
        $this->assertSame('images/d.png', $service->get('branding.login_logo'));
        $this->assertTrue($service->get('landing.show_faq'));
    }

    public function test_values_are_cast_and_cache_is_invalidated_after_update(): void
    {
        $service = app(SiteSettingsService::class);
        $this->assertSame(260, $service->get('sidebar.expanded_width'));

        $service->setMany([
            'sidebar.expanded_width' => '300',
            'landing.show_faq' => '0',
            'buttons.primary_background' => '#ABCDEF',
        ]);

        $this->assertSame(300, $service->get('sidebar.expanded_width'));
        $this->assertFalse($service->get('landing.show_faq'));
        $this->assertSame('#abcdef', $service->get('buttons.primary_background'));
    }

    public function test_invalid_stored_value_falls_back_to_default(): void
    {
        Setting::query()->create(['key' => 'sidebar.expanded_width', 'value' => '9999']);

        $this->assertSame(260, app(SiteSettingsService::class)->get('sidebar.expanded_width'));
    }

    public function test_unknown_keys_and_unsafe_destinations_are_rejected(): void
    {
        $service = app(SiteSettingsService::class);

        $this->expectException(InvalidArgumentException::class);
        $service->set('unknown.key', 'value');
    }

    public function test_unsafe_destination_is_rejected(): void
    {
        $this->expectException(InvalidArgumentException::class);

        app(SiteSettingsService::class)->set('landing.primary_button_url', 'javascript:alert(1)');
    }

    public function test_reset_all_does_not_remove_attendance_settings(): void
    {
        Setting::query()->create(['key' => Setting::KEY_SCAN_SMS, 'value' => 'Welcome']);
        app(SiteSettingsService::class)->set('login.title', 'Custom title');

        app(SiteSettingsService::class)->resetAll();

        $this->assertDatabaseHas('settings', ['key' => Setting::KEY_SCAN_SMS, 'value' => 'Welcome']);
        $this->assertSame('Welcome! Let’s Begin', app(SiteSettingsService::class)->get('login.title'));
    }
}
