<?php

namespace App\Http\Requests\SiteCustomization;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class CustomizationActionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('isAdminDeveloper');
    }

    public function rules(): array
    {
        return [];
    }
}
