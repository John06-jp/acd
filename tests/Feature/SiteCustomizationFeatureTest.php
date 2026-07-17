<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\SettingRevision;
use App\Models\User;
use App\Services\SiteSettingsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SiteCustomizationFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_customization_routes_are_admin_only(): void
    {
        $this->get(route('site-customization.index'))->assertRedirect(route('login'));

        $staff = User::factory()->create(['role' => 'staff']);
        $this->actingAs($staff)->get(route('site-customization.index'))->assertForbidden();

        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin)->get(route('site-customization.index'))->assertForbidden();

        $developer = User::factory()->create(['role' => 'admindeveloper']);
        $this->actingAs($developer)->get(route('site-customization.index'))->assertOk()->assertSee('Site Customization');
    }

    public function test_developer_dashboard_is_separate_from_library_admin(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin)->get(route('developer.dashboard'))->assertForbidden();

        $developer = User::factory()->create(['role' => 'admindeveloper']);
        $this->actingAs($developer)
            ->get(route('developer.dashboard'))
            ->assertOk()
            ->assertSeeText('Developer Dashboard')
            ->assertSee(route('site-customization.index'));
    }

    public function test_admin_can_save_a_section_and_revision_is_recorded(): void
    {
        $admin = User::factory()->create(['role' => 'admindeveloper']);

        $this->actingAs($admin)->put(route('site-customization.update-section', 'login'), [
            'settings' => ['login.title' => 'Welcome librarians'],
        ])->assertSessionHasNoErrors();

        $this->assertDatabaseHas('settings', ['key' => 'login.title', 'value' => 'Welcome librarians']);
        $this->assertDatabaseHas('setting_revisions', [
            'user_id' => $admin->id,
            'action' => 'update',
            'setting_key' => 'login.title',
        ]);
    }

    public function test_unknown_cross_section_and_unsafe_values_are_rejected(): void
    {
        $admin = User::factory()->create(['role' => 'admindeveloper']);

        $this->actingAs($admin)->from(route('site-customization.index'))->put(route('site-customization.update-section', 'landing'), [
            'settings' => [
                'unknown.key' => 'x',
                'login.title' => 'Wrong section',
                'landing.primary_button_url' => 'javascript:alert(1)',
            ],
        ])->assertSessionHasErrors([
            'settings.unknown.key',
            'settings.login.title',
            'settings.landing.primary_button_url',
        ]);

        $this->assertDatabaseCount('settings', 0);
    }

    public function test_unreadable_button_color_pair_is_rejected(): void
    {
        $admin = User::factory()->create(['role' => 'admindeveloper']);

        $this->actingAs($admin)->put(route('site-customization.update-section', 'buttons'), [
            'settings' => [
                'buttons.primary_background' => '#ffffff',
                'buttons.primary_text' => '#ffffff',
            ],
        ])->assertSessionHasErrors('settings.buttons.primary_text');
    }

    public function test_valid_image_can_be_replaced_removed_and_falls_back(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => 'admindeveloper']);

        $this->actingAs($admin)->post(route('site-customization.images.upload', 'branding.login_logo'), [
            'image' => UploadedFile::fake()->image('logo.jpg', 600, 300)->size(100),
        ])->assertSessionHasNoErrors();

        $path = Setting::query()->where('key', 'branding.login_logo')->value('value');
        Storage::disk('public')->assertExists($path);

        $this->actingAs($admin)->delete(route('site-customization.images.remove', 'branding.login_logo'))
            ->assertSessionHasNoErrors();

        Storage::disk('public')->assertMissing($path);
        $this->assertSame('images/d.png', app(SiteSettingsService::class)->get('branding.login_logo'));
    }

    public function test_invalid_and_disguised_uploads_are_rejected(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => 'admindeveloper']);

        $this->actingAs($admin)->post(route('site-customization.images.upload', 'branding.login_logo'), [
            'image' => UploadedFile::fake()->createWithContent('logo.jpg', '<?php echo "unsafe";'),
        ])->assertSessionHasErrors('image');

        $this->actingAs($admin)->post(route('site-customization.images.upload', 'login.title'), [
            'image' => UploadedFile::fake()->image('logo.png'),
        ])->assertSessionHasErrors();
    }

    public function test_section_and_all_resets_preserve_attendance_settings(): void
    {
        $admin = User::factory()->create(['role' => 'admindeveloper']);
        $service = app(SiteSettingsService::class);
        Setting::query()->create(['key' => Setting::KEY_SCAN_SMS, 'value' => 'Keep me']);
        $service->setMany(['login.title' => 'Changed', 'landing.hero_heading' => 'Changed'], $admin->id);

        $this->actingAs($admin)->delete(route('site-customization.reset-section', 'login'));
        $this->assertSame('Welcome! Let’s Begin', $service->get('login.title'));
        $this->assertSame('Changed', $service->get('landing.hero_heading'));

        $this->actingAs($admin)->delete(route('site-customization.reset-all'));
        $this->assertDatabaseHas('settings', ['key' => Setting::KEY_SCAN_SMS, 'value' => 'Keep me']);
    }

    public function test_revision_batch_can_be_restored_and_restore_is_logged(): void
    {
        $admin = User::factory()->create(['role' => 'admindeveloper']);
        $service = app(SiteSettingsService::class);
        $service->set('login.title', 'First', $admin->id);
        $service->set('login.title', 'Second', $admin->id);
        $batch = SettingRevision::query()->where('new_value', 'Second')->value('batch_uuid');

        $this->actingAs($admin)->post(route('site-customization.restore', $batch));

        $this->assertSame('First', $service->get('login.title'));
        $this->assertDatabaseHas('setting_revisions', ['action' => 'restore', 'setting_key' => 'login.title']);
    }

    public function test_saved_content_renders_on_landing_and_login_pages(): void
    {
        app(SiteSettingsService::class)->setMany([
            'landing.hero_heading' => 'Custom library heading',
            'landing.show_hero_content' => true,
            'login.title' => 'Custom login heading',
        ]);

        $this->get(route('home'))->assertOk()->assertSeeText('Custom library heading');
        $this->get(route('login'))->assertOk()->assertSeeText('Custom login heading');
    }

    public function test_existing_attendance_setting_defaults_and_serialization_are_unchanged(): void
    {
        $this->assertTrue(Setting::logoutFeedbackEnabled());
        $this->assertTrue(Setting::sectionPickerEnabled());
        $this->assertSame(Setting::DEFAULT_ATTENDANCE_SECTIONS, Setting::attendanceSections());

        Setting::setLogoutFeedbackEnabled(false);
        Setting::setSectionPickerEnabled(false);
        Setting::setAttendanceSections(['Library', 'Library', ' Archives ']);

        $this->assertFalse(Setting::logoutFeedbackEnabled());
        $this->assertFalse(Setting::sectionPickerEnabled());
        $this->assertSame(['Library', 'Archives'], Setting::attendanceSections());
    }
}
