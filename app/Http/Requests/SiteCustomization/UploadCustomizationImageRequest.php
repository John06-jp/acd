<?php

namespace App\Http\Requests\SiteCustomization;

use App\Services\SiteSettingsService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class UploadCustomizationImageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('isAdminDeveloper');
    }

    public function rules(): array
    {
        $service = app(SiteSettingsService::class);
        $key = (string) $this->route('key');
        $definition = $service->has($key) ? $service->definition($key) : null;

        return [
            'key_guard' => [Rule::in($definition && $definition['type'] === 'image' ? [$key] : [])],
            'image' => $definition['upload_rules'] ?? ['required', 'prohibited'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['key_guard' => (string) $this->route('key')]);
    }
}
