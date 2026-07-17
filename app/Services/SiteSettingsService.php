<?php

namespace App\Services;

use App\Models\Setting;
use App\Models\SettingRevision;
use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Contracts\Validation\Factory as ValidationFactory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use InvalidArgumentException;

class SiteSettingsService
{
    private const CACHE_PREFIX = 'site-customization.resolved.';

    /** @var array<string, array<string, mixed>> */
    private array $definitions;

    public function __construct(
        private readonly CacheRepository $cache,
        private readonly ValidationFactory $validator,
    ) {
        $this->definitions = config('site-customization.definitions', []);
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->definitions);
    }

    /** @return array<string, mixed> */
    public function definition(string $key): array
    {
        if (! $this->has($key)) {
            throw new InvalidArgumentException("Unknown site customization key [{$key}].");
        }

        return $this->definitions[$key];
    }

    /** @return array<string, array<string, mixed>> */
    public function definitions(?string $group = null): array
    {
        if ($group === null) {
            return $this->definitions;
        }

        $this->assertGroup($group);

        return array_filter(
            $this->definitions,
            static fn (array $definition): bool => $definition['group'] === $group
        );
    }

    public function get(string $key): mixed
    {
        $this->definition($key);

        return $this->all()[$key];
    }

    /** @return array<string, mixed> */
    public function group(string $group): array
    {
        $keys = array_keys($this->definitions($group));

        return array_intersect_key($this->all(), array_flip($keys));
    }

    /** @return array<string, mixed> */
    public function all(): array
    {
        if (! Schema::hasTable('settings')) {
            return array_map(
                static fn (array $definition): mixed => $definition['default'],
                $this->definitions
            );
        }

        return $this->cache->rememberForever($this->cacheKey(), function (): array {
            $stored = Setting::query()
                ->whereIn('key', array_keys($this->definitions))
                ->pluck('value', 'key');

            $resolved = [];
            foreach ($this->definitions as $key => $definition) {
                $resolved[$key] = $this->resolve(
                    $key,
                    $stored->has($key) ? $stored->get($key) : null,
                    $stored->has($key)
                );
            }

            return $resolved;
        });
    }

    public function set(string $key, mixed $value, ?int $userId = null, array $metadata = []): mixed
    {
        return $this->setMany([$key => $value], $userId, 'update', $metadata)[$key];
    }

    /**
     * @param  array<string, mixed>  $values
     * @return array<string, mixed>
     */
    public function setMany(
        array $values,
        ?int $userId = null,
        string $action = 'update',
        array $metadata = [],
    ): array {
        $validated = [];
        foreach ($values as $key => $value) {
            $validated[$key] = $this->validateAndCast($key, $value);
        }

        DB::transaction(function () use ($validated, $userId, $action, $metadata): void {
            $batch = (string) Str::uuid();
            foreach ($validated as $key => $value) {
                $previous = Setting::query()->where('key', $key)->value('value');
                $serialized = $this->serialize($this->definition($key)['type'], $value);
                if ($previous === $serialized) {
                    continue;
                }

                Setting::query()->updateOrCreate(
                    ['key' => $key],
                    ['value' => $serialized]
                );
                $this->recordRevision($batch, $action, $key, $previous, $serialized, $userId, $metadata);
            }
        });

        $this->clearCache();

        return $validated;
    }

    public function reset(string $key, ?int $userId = null, string $action = 'reset', array $metadata = []): void
    {
        $this->definition($key);
        $this->resetKeys([$key], $userId, $action, $metadata);
        $this->clearCache();
    }

    public function resetGroup(string $group, ?int $userId = null, array $metadata = []): void
    {
        $keys = array_keys($this->definitions($group));
        $this->resetKeys($keys, $userId, 'reset_section', $metadata);
        $this->clearCache();
    }

    public function resetAll(?int $userId = null, array $metadata = []): void
    {
        $this->resetKeys(array_keys($this->definitions), $userId, 'reset_all', $metadata);
        $this->clearCache();
    }

    public function restoreBatch(string $batchUuid, ?int $userId = null, array $metadata = []): void
    {
        $revisions = SettingRevision::query()
            ->where('batch_uuid', $batchUuid)
            ->orderByDesc('id')
            ->get();

        if ($revisions->isEmpty()) {
            throw new InvalidArgumentException('The requested customization revision does not exist.');
        }

        DB::transaction(function () use ($revisions, $userId, $metadata): void {
            $restoreBatch = (string) Str::uuid();
            foreach ($revisions as $revision) {
                if (! $this->has($revision->setting_key)) {
                    continue;
                }

                $current = Setting::query()->where('key', $revision->setting_key)->value('value');
                if ($revision->previous_value === null) {
                    Setting::query()->where('key', $revision->setting_key)->delete();
                } else {
                    $this->resolve($revision->setting_key, $revision->previous_value, true);
                    Setting::query()->updateOrCreate(
                        ['key' => $revision->setting_key],
                        ['value' => $revision->previous_value]
                    );
                }
                $this->recordRevision($restoreBatch, 'restore', $revision->setting_key, $current, $revision->previous_value, $userId, $metadata);
            }
        });

        $this->clearCache();
    }

    public function clearCache(): void
    {
        $this->cache->forget($this->cacheKey());
        $this->cache->forget(self::CACHE_PREFIX.'v1');
    }

    public function publicUrl(string $key): string
    {
        $definition = $this->definition($key);
        if ($definition['type'] !== 'image') {
            throw new InvalidArgumentException("Site customization key [{$key}] is not an image.");
        }

        $path = $this->get($key);

        return str_starts_with($path, config('site-customization.image_directory').'/')
            ? Storage::disk('public')->url($path)
            : asset($path);
    }

    public function isCustomizationImagePath(?string $path): bool
    {
        if ($path === null) {
            return false;
        }

        return preg_match(
            '/^'.preg_quote(config('site-customization.image_directory'), '/').'\/[A-Za-z0-9._-]+$/',
            $path
        ) === 1;
    }

    public function isSafeDestination(string $destination): bool
    {
        if (str_starts_with($destination, '/') && ! str_starts_with($destination, '//')) {
            return ! str_contains($destination, "\0");
        }

        $parts = parse_url($destination);
        if ($parts === false || empty($parts['scheme']) || empty($parts['host'])) {
            return false;
        }

        return in_array(strtolower($parts['scheme']), config('site-customization.allowed_destination_schemes', []), true)
            && ! isset($parts['user'])
            && ! isset($parts['pass']);
    }

    public function contrastRatio(string $first, string $second): float
    {
        $luminance = static function (string $color): float {
            $channels = [substr($color, 1, 2), substr($color, 3, 2), substr($color, 5, 2)];
            $channels = array_map(static function (string $channel): float {
                $value = hexdec($channel) / 255;

                return $value <= 0.04045 ? $value / 12.92 : (($value + 0.055) / 1.055) ** 2.4;
            }, $channels);

            return 0.2126 * $channels[0] + 0.7152 * $channels[1] + 0.0722 * $channels[2];
        };

        [$lighter, $darker] = [$luminance($first), $luminance($second)];
        if ($lighter < $darker) {
            [$lighter, $darker] = [$darker, $lighter];
        }

        return ($lighter + 0.05) / ($darker + 0.05);
    }

    private function resolve(string $key, mixed $stored, bool $exists): mixed
    {
        $definition = $this->definition($key);
        if (! $exists) {
            return $definition['default'];
        }

        try {
            return $this->validateAndCast($key, $stored);
        } catch (InvalidArgumentException) {
            return $definition['default'];
        }
    }

    private function validateAndCast(string $key, mixed $value): mixed
    {
        $definition = $this->definition($key);
        $cast = $this->cast($definition['type'], $value);
        $validation = $this->validator->make(['value' => $cast], ['value' => $definition['rules']]);

        if ($validation->fails()) {
            throw new InvalidArgumentException($validation->errors()->first('value'));
        }

        if (($definition['url_policy'] ?? null) === 'safe_destination' && ! $this->isSafeDestination($cast)) {
            throw new InvalidArgumentException('The destination must be a safe internal path or an HTTP(S) URL.');
        }

        return $cast;
    }

    private function cast(string $type, mixed $value): mixed
    {
        return match ($type) {
            'boolean' => $this->castBoolean($value),
            'integer' => filter_var($value, FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE)
                ?? throw new InvalidArgumentException('The value must be an integer.'),
            'json' => $this->castJson($value),
            'color' => strtolower(trim((string) $value)),
            default => is_scalar($value) || $value === null
                ? (string) $value
                : throw new InvalidArgumentException('The value must be a string.'),
        };
    }

    private function castBoolean(mixed $value): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        $cast = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

        return $cast ?? throw new InvalidArgumentException('The value must be a boolean.');
    }

    /** @return array<mixed> */
    private function castJson(mixed $value): array
    {
        if (is_array($value)) {
            return $value;
        }

        $decoded = json_decode((string) $value, true);

        return is_array($decoded)
            ? $decoded
            : throw new InvalidArgumentException('The value must be valid JSON.');
    }

    private function serialize(string $type, mixed $value): string
    {
        return match ($type) {
            'boolean' => $value ? '1' : '0',
            'json' => (string) json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR),
            default => (string) $value,
        };
    }

    private function assertGroup(string $group): void
    {
        if (! array_key_exists($group, config('site-customization.groups', []))) {
            throw new InvalidArgumentException("Unknown site customization group [{$group}].");
        }
    }

    private function cacheKey(): string
    {
        return self::CACHE_PREFIX.hash('sha256', serialize($this->definitions));
    }

    /** @param list<string> $keys */
    private function resetKeys(array $keys, ?int $userId, string $action, array $metadata): void
    {
        DB::transaction(function () use ($keys, $userId, $action, $metadata): void {
            $stored = Setting::query()->whereIn('key', $keys)->pluck('value', 'key');
            $batch = (string) Str::uuid();
            Setting::query()->whereIn('key', $keys)->delete();

            foreach ($stored as $key => $previous) {
                $this->recordRevision($batch, $action, $key, $previous, null, $userId, $metadata);
            }
        });
    }

    private function recordRevision(
        string $batch,
        string $action,
        string $key,
        ?string $previous,
        ?string $new,
        ?int $userId,
        array $metadata,
    ): void {
        SettingRevision::query()->create([
            'user_id' => $userId,
            'batch_uuid' => $batch,
            'action' => $action,
            'setting_key' => $key,
            'previous_value' => $previous,
            'new_value' => $new,
            'ip_address' => isset($metadata['ip_address']) ? mb_substr((string) $metadata['ip_address'], 0, 45) : null,
            'user_agent' => isset($metadata['user_agent']) ? mb_substr((string) $metadata['user_agent'], 0, 500) : null,
        ]);
    }
}
