<?php

namespace App\Http\Requests\SiteCustomization;

use App\Services\SiteSettingsService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Validator as LaravelValidator;

class SaveCustomizationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('isAdminDeveloper');
    }

    public function rules(): array
    {
        return ['settings' => ['required', 'array']];
    }

    public function withValidator(LaravelValidator $validator): void
    {
        $validator->after(function (LaravelValidator $validator): void {
            $service = app(SiteSettingsService::class);
            $settings = $this->input('settings', []);
            $group = $this->route('group');

            foreach ($settings as $key => $value) {
                if (! is_string($key) || ! $service->has($key)) {
                    $validator->errors()->add("settings.{$key}", 'This setting is not supported.');

                    continue;
                }

                $definition = $service->definition($key);
                if ($definition['type'] === 'image') {
                    $validator->errors()->add("settings.{$key}", 'Images must use the image upload control.');

                    continue;
                }

                if (is_string($group) && $definition['group'] !== $group) {
                    $validator->errors()->add("settings.{$key}", 'This setting does not belong to the selected section.');

                    continue;
                }

                $fieldValidator = Validator::make(['value' => $value], ['value' => $definition['rules']]);
                if ($fieldValidator->fails()) {
                    $validator->errors()->add("settings.{$key}", $fieldValidator->errors()->first('value'));

                    continue;
                }

                if (($definition['url_policy'] ?? null) === 'safe_destination' && ! $service->isSafeDestination((string) $value)) {
                    $validator->errors()->add("settings.{$key}", 'Use a safe internal path or an HTTP(S) URL.');
                }
            }

            $current = $service->all();
            $resolved = array_replace($current, $settings);
            foreach (['primary', 'secondary', 'success', 'warning', 'danger', 'neutral'] as $variant) {
                if ($this->pairChanged($settings, $current, ["buttons.{$variant}_text", "buttons.{$variant}_background"])) {
                    $this->validateContrast(
                        $validator,
                        "settings.buttons.{$variant}_text",
                        (string) $resolved["buttons.{$variant}_text"],
                        (string) $resolved["buttons.{$variant}_background"],
                        $service
                    );
                }
                if ($this->pairChanged($settings, $current, ["buttons.{$variant}_hover_text", "buttons.{$variant}_hover_background"])) {
                    $this->validateContrast(
                        $validator,
                        "settings.buttons.{$variant}_hover_text",
                        (string) $resolved["buttons.{$variant}_hover_text"],
                        (string) $resolved["buttons.{$variant}_hover_background"],
                        $service
                    );
                }
            }

            if ($this->pairChanged($settings, $current, ['login.button_text_color', 'login.button_background'])) {
                $this->validateContrast(
                    $validator,
                    'settings.login.button_text_color',
                    (string) $resolved['login.button_text_color'],
                    (string) $resolved['login.button_background'],
                    $service
                );
            }
        });
    }

    /** @return array<string, mixed> */
    public function settings(): array
    {
        return $this->input('settings', []);
    }

    private function validateContrast(
        LaravelValidator $validator,
        string $field,
        string $text,
        string $background,
        SiteSettingsService $service,
    ): void {
        if (preg_match('/^#[0-9A-Fa-f]{6}$/', $text) !== 1 || preg_match('/^#[0-9A-Fa-f]{6}$/', $background) !== 1) {
            return;
        }

        if ($service->contrastRatio($text, $background) < 3.0) {
            $validator->errors()->add($field, 'Text and background colors need a contrast ratio of at least 3:1.');
        }
    }

    /** @param array<string, mixed> $submitted @param array<string, mixed> $current @param list<string> $keys */
    private function pairChanged(array $submitted, array $current, array $keys): bool
    {
        foreach ($keys as $key) {
            if (array_key_exists($key, $submitted) && strtolower((string) $submitted[$key]) !== strtolower((string) $current[$key])) {
                return true;
            }
        }

        return false;
    }
}
