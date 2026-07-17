<?php

namespace App\Http\Controllers;

use App\Models\SettingRevision;
use App\Services\SiteSettingsService;
use Illuminate\View\View;

class DeveloperDashboardController extends Controller
{
    public function __invoke(SiteSettingsService $settings): View
    {
        return view('developer.dashboard', [
            'customizedCount' => collect($settings->all())
                ->filter(fn (mixed $value, string $key): bool => $value !== $settings->definition($key)['default'])
                ->count(),
            'settingCount' => count($settings->definitions()),
            'recentChanges' => SettingRevision::query()->latest()->limit(5)->get(),
        ]);
    }
}
