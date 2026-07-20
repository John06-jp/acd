<?php

namespace App\Http\Controllers;

use App\Http\Requests\SiteCustomization\CustomizationActionRequest;
use App\Http\Requests\SiteCustomization\SaveCustomizationRequest;
use App\Http\Requests\SiteCustomization\UploadCustomizationImageRequest;
use App\Models\SettingRevision;
use App\Services\SiteCustomizationImageService;
use App\Services\SiteSettingsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SiteCustomizationController extends Controller
{
    public function __construct(
        private readonly SiteSettingsService $settings,
        private readonly SiteCustomizationImageService $images,
    ) {}

    public function index(): View
    {
        $historyPage = SettingRevision::query()
            ->selectRaw('batch_uuid, MAX(created_at) as latest_at')
            ->groupBy('batch_uuid')
            ->orderByDesc('latest_at')
            ->paginate(10, ['*'], 'history_page')
            ->withQueryString();

        $batchOrder = array_flip($historyPage->pluck('batch_uuid')->all());
        $revisionBatches = SettingRevision::query()
            ->with('user')
            ->whereIn('batch_uuid', $historyPage->pluck('batch_uuid'))
            ->orderBy('id')
            ->get()
            ->groupBy('batch_uuid')
            ->sortBy(static fn ($revisions, $batch) => $batchOrder[$batch] ?? PHP_INT_MAX);

        return view('admin.site-customization.index', [
            'groups' => config('site-customization.groups'),
            'definitions' => $this->settings->definitions(),
            'values' => $this->settings->all(),
            'revisionBatches' => $revisionBatches,
            'historyPage' => $historyPage,
        ]);
    }

    public function updateSection(SaveCustomizationRequest $request, string $group): RedirectResponse
    {
        abort_unless(array_key_exists($group, config('site-customization.groups')), 404);
        $this->settings->definitions($group);
        $this->settings->setMany($request->settings(), $request->user()->id, 'update', $this->metadata($request));

        return back()->with('status', config("site-customization.groups.{$group}").' settings saved.');
    }

    public function updateAll(SaveCustomizationRequest $request): RedirectResponse
    {
        $this->settings->setMany($request->settings(), $request->user()->id, 'update', $this->metadata($request));

        return back()->with('status', 'All site customization settings saved.');
    }

    public function upload(UploadCustomizationImageRequest $request, string $key): RedirectResponse
    {
        $this->images->replace($key, $request->file('image'), $request->user()->id, $this->metadata($request));

        return back()->with('status', 'Image updated successfully.');
    }

    public function removeImage(CustomizationActionRequest $request, string $key): RedirectResponse
    {
        abort_unless($this->settings->has($key) && $this->settings->definition($key)['type'] === 'image', 404);
        $this->images->remove($key, $request->user()->id, $this->metadata($request));

        return back()->with('status', 'Image restored to its factory default.');
    }

    public function resetSection(CustomizationActionRequest $request, string $group): RedirectResponse
    {
        abort_unless(array_key_exists($group, config('site-customization.groups')), 404);
        $this->images->resetGroup($group, $request->user()->id, $this->metadata($request));

        return back()->with('status', config("site-customization.groups.{$group}").' restored to factory defaults.');
    }

    public function resetAll(CustomizationActionRequest $request): RedirectResponse
    {
        $this->images->resetAll($request->user()->id, $this->metadata($request));

        return back()->with('status', 'All site customization settings restored to factory defaults.');
    }

    public function restore(CustomizationActionRequest $request, string $batch): RedirectResponse
    {
        abort_unless(SettingRevision::query()->where('batch_uuid', $batch)->exists(), 404);
        $this->settings->restoreBatch($batch, $request->user()->id, $this->metadata($request));

        return back()->with('status', 'The selected customization revision was restored.');
    }

    private function metadata(Request $request): array
    {
        return [
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ];
    }
}
