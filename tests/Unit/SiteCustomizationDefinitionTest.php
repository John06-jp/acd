<?php

namespace Tests\Unit;

use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class SiteCustomizationDefinitionTest extends TestCase
{
    public function test_registry_defines_valid_and_complete_settings(): void
    {
        $groups = config('site-customization.groups');
        $definitions = config('site-customization.definitions');

        $this->assertSame(
            ['branding', 'landing', 'login', 'sidebar', 'buttons', 'tables', 'advanced_theme'],
            array_keys($groups)
        );
        $this->assertNotEmpty($definitions);

        foreach ($definitions as $key => $definition) {
            $this->assertMatchesRegularExpression('/^[a-z][a-z0-9_]*\.[a-z][a-z0-9_]*$/', $key);
            $this->assertContains($definition['group'], array_keys($groups), "Unknown group for {$key}");
            $this->assertContains($definition['type'], ['string', 'boolean', 'integer', 'color', 'url', 'json', 'image', 'enum']);
            $this->assertIsArray($definition['rules']);
            $this->assertNotSame('', $definition['label']);

            if ($definition['type'] !== 'image') {
                $validator = Validator::make(['value' => $definition['default']], ['value' => $definition['rules']]);
                $this->assertFalse($validator->fails(), "Invalid default for {$key}: {$validator->errors()->first('value')}");
            } else {
                $this->assertArrayHasKey('upload_rules', $definition);
                $this->assertNotEmpty($definition['default']);
            }
        }
    }

    public function test_required_setting_families_are_present(): void
    {
        $definitions = config('site-customization.definitions');

        foreach (['primary', 'secondary', 'success', 'warning', 'danger', 'neutral'] as $variant) {
            foreach (['background', 'text', 'border', 'hover_background', 'hover_text'] as $token) {
                $this->assertArrayHasKey("buttons.{$variant}_{$token}", $definitions);
            }
        }

        foreach (['header_background', 'header_text', 'body_background', 'body_text', 'border', 'stripe_background', 'hover_background', 'selected_background', 'radius', 'spacing'] as $token) {
            $this->assertArrayHasKey("tables.{$token}", $definitions);
        }
    }
}
