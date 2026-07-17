<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Throwable;

class SiteCustomizationImageService
{
    public function __construct(private readonly SiteSettingsService $settings) {}

    public function replace(string $key, UploadedFile $file, ?int $userId = null, array $metadata = []): string
    {
        $definition = $this->settings->definition($key);
        if ($definition['type'] !== 'image') {
            throw new InvalidArgumentException("Site customization key [{$key}] is not an image.");
        }

        $oldPath = $this->settings->get($key);
        $extension = strtolower($file->extension());
        $extension = $extension === 'jpeg' ? 'jpg' : $extension;
        $directory = config('site-customization.image_directory');
        $path = $directory.'/'.Str::uuid().'.'.$extension;

        $stored = Storage::disk('public')->putFileAs($directory, $file, basename($path));
        if ($stored !== $path) {
            throw new InvalidArgumentException('The customization image could not be stored.');
        }

        try {
            $this->settings->setMany([$key => $path], $userId, 'update', $metadata);
        } catch (Throwable $exception) {
            Storage::disk('public')->delete($path);
            throw $exception;
        }

        if ($this->settings->isCustomizationImagePath($oldPath) && $oldPath !== $path) {
            Storage::disk('public')->delete($oldPath);
        }

        return $path;
    }

    public function remove(string $key, ?int $userId = null, array $metadata = []): void
    {
        $definition = $this->settings->definition($key);
        if ($definition['type'] !== 'image') {
            throw new InvalidArgumentException("Site customization key [{$key}] is not an image.");
        }

        $oldPath = $this->settings->get($key);
        $this->settings->reset($key, $userId, 'remove_image', $metadata);

        if ($this->settings->isCustomizationImagePath($oldPath)) {
            Storage::disk('public')->delete($oldPath);
        }
    }

    public function resetGroup(string $group, ?int $userId = null, array $metadata = []): void
    {
        $paths = $this->activeImagePaths(array_keys($this->settings->definitions($group)));
        $this->settings->resetGroup($group, $userId, $metadata);
        Storage::disk('public')->delete($paths);
    }

    public function resetAll(?int $userId = null, array $metadata = []): void
    {
        $paths = $this->activeImagePaths(array_keys($this->settings->definitions()));
        $this->settings->resetAll($userId, $metadata);
        Storage::disk('public')->delete($paths);
    }

    /** @param list<string> $keys @return list<string> */
    private function activeImagePaths(array $keys): array
    {
        $paths = [];
        foreach ($keys as $key) {
            $definition = $this->settings->definition($key);
            if ($definition['type'] !== 'image') {
                continue;
            }

            $path = $this->settings->get($key);
            if ($this->settings->isCustomizationImagePath($path)) {
                $paths[] = $path;
            }
        }

        return $paths;
    }
}
