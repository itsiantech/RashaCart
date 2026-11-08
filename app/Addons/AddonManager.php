<?php

namespace App\Addons;

use App\Models\Addon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * Discovers, installs and boots add-ons.
 *
 * Discovery only reads addon.json manifests, so the core keeps working with
 * zero add-ons installed. Providers are instantiated manually (instead of being
 * registered in the container) so a provider that throws can be isolated,
 * deactivated and reported without taking the shop down.
 */
class AddonManager
{
    protected string $path;

    /** @var array<string, array>|null */
    protected ?array $manifests = null;

    protected ?Collection $records = null;

    protected bool $autoloaderRegistered = false;

    /** @var array<string, \Illuminate\Support\ServiceProvider> */
    protected array $bootedProviders = [];

    public function __construct(?string $path = null)
    {
        $this->path = rtrim($path ?: base_path('addons'), '/\\');
    }

    public function path(): string
    {
        return $this->path;
    }

    public function coreVersion(): string
    {
        return (string) config('addons.core_version', '1.0.0');
    }

    /* ---------------------------------------------------------------------
     |  Autoloading
     | ------------------------------------------------------------------- */

    /**
     * Map Addons\{Folder}\... to addons/{Folder}/src/....
     * Registered once per process from AppServiceProvider::register().
     */
    public function registerAutoloader(): void
    {
        if ($this->autoloaderRegistered) {
            return;
        }

        $this->autoloaderRegistered = true;

        spl_autoload_register(function (string $class): void {
            if (! str_starts_with($class, 'Addons\\')) {
                return;
            }

            $segments = explode('\\', substr($class, strlen('Addons\\')));

            if (count($segments) < 2) {
                return;
            }

            $folder = $this->resolveAddonDirectory(array_shift($segments));

            if ($folder === null) {
                return;
            }

            $file = $folder.'/src/'.implode('/', $segments).'.php';

            if (is_file($file)) {
                require_once $file;
            }
        });
    }

    protected function resolveAddonDirectory(string $name): ?string
    {
        if (! is_dir($this->path)) {
            return null;
        }

        $direct = $this->path.'/'.$name;

        if (is_dir($direct)) {
            return $direct;
        }

        foreach (scandir($this->path) ?: [] as $entry) {
            if ($entry === '.' || $entry === '..') {
                continue;
            }

            if (strcasecmp($entry, $name) === 0 && is_dir($this->path.'/'.$entry)) {
                return $this->path.'/'.$entry;
            }
        }

        return null;
    }

    /* ---------------------------------------------------------------------
     |  Discovery
     | ------------------------------------------------------------------- */

    /**
     * @return array<string, array> slug => normalized manifest
     */
    public function discover(): array
    {
        if ($this->manifests !== null) {
            return $this->manifests;
        }

        $this->manifests = [];

        if (! is_dir($this->path)) {
            return $this->manifests;
        }

        foreach (scandir($this->path) ?: [] as $entry) {
            if ($entry === '.' || $entry === '..') {
                continue;
            }

            $folder = $this->path.'/'.$entry;
            $file = $folder.'/addon.json';

            if (! is_dir($folder) || ! is_file($file)) {
                continue;
            }

            $data = json_decode((string) file_get_contents($file), true);

            if (! is_array($data)) {
                $this->manifests[$entry] = $this->normalizeManifest($entry, $folder, [], 'addon.json is not valid JSON.');

                continue;
            }

            $manifest = $this->normalizeManifest($entry, $folder, $data);

            if (isset($this->manifests[$manifest['slug']])) {
                $manifest['error'] = "Duplicate slug [{$manifest['slug']}] — another folder already uses it.";
                $manifest['slug'] = $entry;
            }

            $this->manifests[$manifest['slug']] = $manifest;
        }

        ksort($this->manifests);

        return $this->manifests;
    }

    protected function normalizeManifest(string $folder, string $path, array $data, ?string $error = null): array
    {
        $slug = trim((string) ($data['slug'] ?? '')) ?: $folder;

        $dependsOn = $data['depends_on'] ?? [];
        $dependsOn = is_array($dependsOn)
            ? array_values(array_filter(array_map('strval', $dependsOn)))
            : [];

        $provides = $data['provides'] ?? [];
        $provides = is_array($provides)
            ? array_values(array_filter(array_map('strval', $provides)))
            : [];

        return [
            'slug' => $slug,
            'folder' => $folder,
            'name' => trim((string) ($data['name'] ?? '')) ?: $slug,
            'version' => trim((string) ($data['version'] ?? '')) ?: '0.0.0',
            'description' => (string) ($data['description'] ?? ''),
            'author' => (string) ($data['author'] ?? ''),
            'requires_core' => trim((string) ($data['requires_core'] ?? '')) ?: '*',
            'provider' => isset($data['provider']) ? ltrim((string) $data['provider'], '\\') : null,
            'depends_on' => $dependsOn,
            'provides' => $provides,
            'path' => $path,
            'error' => $error,
        ];
    }

    public function manifest(string $slug): ?array
    {
        $slug = $this->resolveSlug($slug);

        return $slug === null ? null : ($this->discover()[$slug] ?? null);
    }

    public function resolveSlug(string $slug): ?string
    {
        $all = $this->discover();

        if (isset($all[$slug])) {
            return $slug;
        }

        foreach (array_keys($all) as $candidate) {
            if (strcasecmp($candidate, $slug) === 0) {
                return $candidate;
            }
        }

        foreach ($this->records()->keys() as $candidate) {
            if (strcasecmp((string) $candidate, $slug) === 0) {
                return (string) $candidate;
            }
        }

        return null;
    }

    /* ---------------------------------------------------------------------
     |  Database state
     | ------------------------------------------------------------------- */

    public function hasTable(): bool
    {
        try {
            return Schema::hasTable('addons');
        } catch (\Throwable) {
            return false;
        }
    }

    /** @return Collection<string, Addon> */
    protected function records(): Collection
    {
        if ($this->records !== null) {
            return $this->records;
        }

        if (! $this->hasTable()) {
            return $this->records = collect();
        }

        try {
            return $this->records = Addon::query()->get()->keyBy('slug');
        } catch (\Throwable) {
            return $this->records = collect();
        }
    }

    public function record(string $slug): ?Addon
    {
        $slug = $this->resolveSlug($slug);

        return $slug === null ? null : $this->records()->get($slug);
    }

    public function isInstalled(string $slug): bool
    {
        return $this->record($slug) !== null;
    }

    public function isActive(string $slug): bool
    {
        foreach ($this->activeSlugs() as $active) {
            if (strcasecmp($active, $slug) === 0) {
                return true;
            }
        }

        return false;
    }

    /** @return list<string> */
    public function activeSlugs(): array
    {
        if (! $this->hasTable()) {
            return [];
        }

        try {
            return Cache::rememberForever(
                (string) config('addons.cache_key', 'addons.active_slugs'),
                fn () => Addon::query()
                    ->where('status', Addon::STATUS_ACTIVE)
                    ->orderBy('slug')
                    ->pluck('slug')
                    ->all()
            );
        } catch (\Throwable) {
            return [];
        }
    }

    public function forgetActiveCache(): void
    {
        try {
            Cache::forget((string) config('addons.cache_key', 'addons.active_slugs'));
        } catch (\Throwable) {
            // Cache store unavailable — nothing to invalidate.
        }
    }

    public function refresh(): void
    {
        $this->manifests = null;
        $this->records = null;
    }

    /**
     * Add-on routes only exist when the provider boots, so a cached route
     * table would keep 404-ing (or keep serving) old add-on URLs.
     */
    protected function afterStatusChange(): void
    {
        $this->forgetActiveCache();

        try {
            if (app()->routesAreCached()) {
                Artisan::call('route:clear');
            }
        } catch (\Throwable) {
            // Route cache could not be cleared; the admin can run route:clear.
        }
    }

    /* ---------------------------------------------------------------------
     |  Boot
     | ------------------------------------------------------------------- */

    /**
     * Register + boot the service providers of ACTIVE add-ons only.
     */
    public function boot(): void
    {
        $active = $this->activeSlugs();

        // Keep the booted map in sync so an add-on deactivated and re-activated
        // inside the same process (tests, queue workers) boots again.
        foreach (array_keys($this->bootedProviders) as $booted) {
            if (! in_array($booted, $active, true)) {
                unset($this->bootedProviders[$booted]);
            }
        }

        foreach ($active as $slug) {
            if (isset($this->bootedProviders[$slug])) {
                continue;
            }

            $manifest = $this->discover()[$slug] ?? null;

            if ($manifest === null) {
                $this->failBoot($slug, 'Folder or addon.json is missing while booting.');

                continue;
            }

            if ($manifest['error'] !== null) {
                $this->failBoot($slug, $manifest['error']);

                continue;
            }

            $provider = $this->makeProvider($manifest);

            if ($provider === null) {
                $this->failBoot($slug, "Provider class [{$manifest['provider']}] was not found.");

                continue;
            }

            try {
                if (method_exists($provider, 'register')) {
                    $provider->register();
                }

                if (method_exists($provider, 'boot')) {
                    $provider->boot();
                }

                $this->bootedProviders[$slug] = $provider;
            } catch (\Throwable $e) {
                $this->failBoot($slug, $e);
            }
        }
    }

    /** @return array<string, \Illuminate\Support\ServiceProvider> */
    public function bootedProviders(): array
    {
        return $this->bootedProviders;
    }

    protected function makeProvider(array $manifest): ?\Illuminate\Support\ServiceProvider
    {
        $class = $manifest['provider'];

        if (! is_string($class) || $class === '') {
            return null;
        }

        try {
            if (! class_exists($class)) {
                return null;
            }

            $instance = new $class(app());

            return $instance instanceof \Illuminate\Support\ServiceProvider ? $instance : null;
        } catch (\Throwable) {
            return null;
        }
    }

    protected function failBoot(string $slug, \Throwable|string $error): void
    {
        $message = $error instanceof \Throwable ? $error->getMessage() : $error;

        Log::error("Add-on [{$slug}] failed to boot and was deactivated: {$message}", $error instanceof \Throwable ? ['exception' => $error] : []);

        try {
            if ($this->hasTable()) {
                Addon::query()->where('slug', $slug)->update([
                    'status' => Addon::STATUS_INACTIVE,
                    'last_error' => Str::limit($message, 500),
                ]);
                $this->refresh();
                $this->forgetActiveCache();
            }
        } catch (\Throwable $e) {
            Log::error("Could not deactivate broken add-on [{$slug}]: ".$e->getMessage());
        }
    }

    /* ---------------------------------------------------------------------
     |  Lifecycle
     | ------------------------------------------------------------------- */

    public function install(string $slug): Addon
    {
        $manifest = $this->requireManifest($slug);
        $slug = $manifest['slug'];

        if ($this->records()->has($slug)) {
            throw new AddonException("[{$slug}] is already installed. Activate it instead.");
        }

        $this->assertManifestUsable($manifest);
        $this->assertCoreCompatible($manifest);
        $this->assertDependenciesInstalled($manifest);

        $this->runMigrations($manifest);

        $provider = $this->makeProvider($manifest);

        if ($provider !== null) {
            try {
                if (method_exists($provider, 'register')) {
                    $provider->register();
                }

                if (method_exists($provider, 'onInstall')) {
                    $provider->onInstall();
                }
            } catch (\Throwable $e) {
                throw new AddonException("Install hook failed for [{$slug}]: ".$e->getMessage());
            }
        }

        $record = Addon::create([
            'slug' => $slug,
            'name' => $manifest['name'],
            'version' => $manifest['version'],
            'description' => $manifest['description'],
            'author' => $manifest['author'],
            'provider' => $manifest['provider'],
            'status' => Addon::STATUS_INSTALLED,
            'provides' => $manifest['provides'],
            'installed_at' => now(),
        ]);

        $this->refresh();
        $this->afterStatusChange();

        return $record;
    }

    public function activate(string $slug): Addon
    {
        $manifest = $this->requireManifest($slug);
        $record = $this->records()->get($manifest['slug']);

        if ($record === null) {
            throw new AddonException("[{$manifest['slug']}] is not installed yet.");
        }

        if ($record->isActive()) {
            return $record;
        }

        $this->assertManifestUsable($manifest);
        $this->assertCoreCompatible($manifest);
        $this->assertDependenciesActive($manifest);

        $record->forceFill([
            'status' => Addon::STATUS_ACTIVE,
            'activated_at' => now(),
            'last_error' => null,
            'version' => $manifest['version'],
            'provides' => $manifest['provides'],
        ])->save();

        $this->refresh();
        $this->afterStatusChange();

        return $record;
    }

    public function deactivate(string $slug): Addon
    {
        $resolved = $this->resolveSlug($slug);
        $record = $resolved === null ? null : $this->records()->get($resolved);

        if ($record === null) {
            throw new AddonException("[{$slug}] is not installed.");
        }

        $dependents = $this->activeDependents($resolved);

        if ($dependents !== []) {
            throw new AddonException(
                "[{$resolved}] cannot be deactivated: ".implode(', ', $dependents).' depends on it.'
            );
        }

        if ($record->status !== Addon::STATUS_INACTIVE) {
            $record->forceFill(['status' => Addon::STATUS_INACTIVE])->save();
        }

        $this->refresh();
        $this->afterStatusChange();

        return $record;
    }

    public function uninstall(string $slug, bool $dropData = false): void
    {
        $resolved = $this->resolveSlug($slug);
        $record = $resolved === null ? null : $this->records()->get($resolved);

        if ($record === null) {
            throw new AddonException("[{$slug}] is not installed.");
        }

        $slug = $resolved;

        if ($record->isActive()) {
            $this->deactivate($slug);
        }

        $dependents = $this->activeDependents($slug);

        if ($dependents !== []) {
            throw new AddonException(
                "[{$slug}] cannot be uninstalled: ".implode(', ', $dependents).' is active and depends on it.'
            );
        }

        // A record whose folder was deleted can still be removed, but there is
        // nothing to roll back or call.
        $manifest = $this->discover()[$slug] ?? null;

        if ($manifest === null) {
            $record->delete();
            $this->refresh();
            $this->afterStatusChange();

            return;
        }

        if ($dropData) {
            $provider = $this->makeProvider($manifest);

            if ($provider !== null && method_exists($provider, 'onUninstall')) {
                try {
                    $provider->onUninstall();
                } catch (\Throwable $e) {
                    Log::error("Uninstall hook failed for [{$slug}]: ".$e->getMessage());
                }
            }

            $this->runMigrations($manifest, rollback: true);
        }

        $record->delete();

        $this->refresh();
        $this->afterStatusChange();
    }

    /* ---------------------------------------------------------------------
     |  Validation
     | ------------------------------------------------------------------- */

    protected function requireManifest(string $slug): array
    {
        $resolved = $this->resolveSlug($slug);

        if ($resolved === null || ! isset($this->discover()[$resolved])) {
            throw new AddonException("Add-on [{$slug}] was not found in ".$this->path.'.');
        }

        return $this->discover()[$resolved];
    }

    protected function assertManifestUsable(array $manifest): void
    {
        if ($manifest['error'] !== null) {
            throw new AddonException('Manifest problem: '.$manifest['error']);
        }

        if (! preg_match('/^[A-Za-z][A-Za-z0-9_]*$/', $manifest['slug'])) {
            throw new AddonException("Invalid slug [{$manifest['slug']}]. Use letters, digits and underscores, starting with a letter.");
        }

        if (strcasecmp($manifest['slug'], $manifest['folder']) !== 0) {
            throw new AddonException("Slug [{$manifest['slug']}] must match its folder name [{$manifest['folder']}].");
        }

        if ($this->makeProvider($manifest) === null) {
            throw new AddonException("Provider class [{$manifest['provider']}] was not found.");
        }
    }

    protected function assertCoreCompatible(array $manifest): void
    {
        if (! $this->satisfies($manifest['requires_core'], $this->coreVersion())) {
            throw new AddonException(
                "[{$manifest['slug']}] requires core {$manifest['requires_core']}, but this core is {$this->coreVersion()}."
            );
        }
    }

    protected function assertDependenciesInstalled(array $manifest): void
    {
        foreach ($manifest['depends_on'] as $dependency) {
            if (! $this->records()->has($dependency)) {
                throw new AddonException("[{$manifest['slug']}] needs [{$dependency}] to be installed first.");
            }
        }
    }

    protected function assertDependenciesActive(array $manifest): void
    {
        foreach ($manifest['depends_on'] as $dependency) {
            if (! $this->records()->has($dependency)) {
                throw new AddonException("[{$manifest['slug']}] needs [{$dependency}] to be installed first.");
            }

            if (! $this->isActive($dependency)) {
                throw new AddonException("[{$manifest['slug']}] needs [{$dependency}] to be active first.");
            }
        }
    }

    /** @return list<string> */
    protected function activeDependents(string $slug): array
    {
        $active = $this->activeSlugs();
        $dependents = [];

        foreach ($active as $activeSlug) {
            $manifest = $this->discover()[$activeSlug] ?? null;

            if ($manifest !== null && in_array($slug, $manifest['depends_on'], true)) {
                $dependents[] = $activeSlug;
            }
        }

        return $dependents;
    }

    /* ---------------------------------------------------------------------
     |  Migrations
     | ------------------------------------------------------------------- */

    protected function runMigrations(array $manifest, bool $rollback = false): void
    {
        $path = $manifest['path'].'/database/migrations';

        if (! is_dir($path)) {
            return;
        }

        $params = [
            '--path' => realpath($path) ?: $path,
            '--realpath' => true,
            '--force' => true,
        ];

        try {
            $exitCode = Artisan::call($rollback ? 'migrate:rollback' : 'migrate', $params);
        } catch (\Throwable $e) {
            throw new AddonException("Migration failed for [{$manifest['slug']}]: ".$e->getMessage());
        }

        if ($exitCode !== 0) {
            throw new AddonException(
                "Migration failed for [{$manifest['slug']}]: ".trim(Artisan::output())
            );
        }
    }

    /* ---------------------------------------------------------------------
     |  Version constraints (composer/semver is not installed)
     | ------------------------------------------------------------------- */

    public function satisfies(string $constraint, string $version): bool
    {
        $constraint = trim($constraint);

        if ($constraint === '' || $constraint === '*') {
            return true;
        }

        foreach (preg_split('/\s*\|\|\s*/', $constraint) ?: [] as $orPart) {
            $orPart = trim($orPart);

            if ($orPart === '' || $orPart === '*') {
                return true;
            }

            $tokens = preg_split('/\s*,\s*|\s+/', $orPart, -1, PREG_SPLIT_NO_EMPTY) ?: [];

            $ok = true;

            foreach ($tokens as $token) {
                if (! $this->satisfiesToken($token, $version)) {
                    $ok = false;

                    break;
                }
            }

            if ($ok) {
                return true;
            }
        }

        return false;
    }

    protected function satisfiesToken(string $token, string $version): bool
    {
        $token = trim($token);

        if ($token === '' || $token === '*') {
            return true;
        }

        if (! preg_match('/^(>=|<=|>|<|=|\^|~)?v?(\d+(?:\.\d+){0,2})$/', $token, $matches)) {
            // Unknown syntax: fail closed so a typo never silently passes.
            return false;
        }

        $operator = $matches[1] !== '' ? $matches[1] : '=';
        $target = $matches[2];
        $current = $this->normalizeVersion($version);
        $targetVersion = $this->normalizeVersion($target);

        return match ($operator) {
            '>' => version_compare($current, $targetVersion, '>'),
            '>=' => version_compare($current, $targetVersion, '>='),
            '<' => version_compare($current, $targetVersion, '<'),
            '<=' => version_compare($current, $targetVersion, '<='),
            '^' => version_compare($current, $targetVersion, '>=')
                && version_compare($current, $this->caretUpperBound($target), '<'),
            '~' => version_compare($current, $targetVersion, '>=')
                && version_compare($current, $this->tildeUpperBound($target), '<'),
            default => version_compare($current, $targetVersion, '=='),
        };
    }

    protected function normalizeVersion(string $version): string
    {
        $parts = array_pad(explode('.', ltrim(trim($version), 'vV')), 3, '0');

        return implode('.', array_slice($parts, 0, 3));
    }

    protected function caretUpperBound(string $version): string
    {
        $parts = array_pad(array_map('intval', explode('.', $version)), 3, 0);

        if ($parts[0] > 0) {
            return ($parts[0] + 1).'.0.0';
        }

        if ($parts[1] > 0) {
            return '0.'.($parts[1] + 1).'.0';
        }

        return '0.0.'.($parts[2] + 1);
    }

    protected function tildeUpperBound(string $version): string
    {
        $parts = array_map('intval', explode('.', $version));

        if (count($parts) === 1) {
            return ($parts[0] + 1).'.0.0';
        }

        return $parts[0].'.'.($parts[1] + 1).'.0';
    }

    /* ---------------------------------------------------------------------
     |  Admin listing
     | ------------------------------------------------------------------- */

    /**
     * Discovered add-ons merged with their DB state, plus everything the
     * admin UI needs to enable/disable buttons.
     *
     * @return list<array>
     */
    public function all(): array
    {
        $records = $this->records();
        $manifests = $this->discover();
        $slugs = array_unique(array_merge(array_keys($manifests), $records->keys()->all()));
        sort($slugs);

        $entries = [];

        foreach ($slugs as $slug) {
            $manifest = $manifests[$slug] ?? null;
            $record = $records->get($slug);

            if ($manifest === null) {
                $entries[$slug] = [
                    'slug' => $slug,
                    'name' => $record?->name ?? $slug,
                    'version' => $record?->version ?? '0.0.0',
                    'description' => $record?->description ?? '',
                    'author' => $record?->author ?? '',
                    'requires_core' => '*',
                    'depends_on' => [],
                    'provides' => $record?->provides ?? [],
                    'path' => null,
                    'status' => $record?->status ?? 'not_installed',
                    'installed' => $record !== null,
                    'active' => $record?->isActive() ?? false,
                    'last_error' => $record?->last_error,
                    'folder_missing' => true,
                    'problems' => ['The add-on folder was deleted from disk. Uninstall it to clean up.'],
                    'can_install' => false,
                    'can_activate' => false,
                    'can_deactivate' => false,
                    'can_uninstall' => true,
                ];

                continue;
            }

            $status = $record?->status ?? 'not_installed';
            $problems = [];

            if ($manifest['error'] !== null) {
                $problems[] = $manifest['error'];
            } elseif (! preg_match('/^[A-Za-z][A-Za-z0-9_]*$/', $manifest['slug'])) {
                $problems[] = "Invalid slug [{$manifest['slug']}].";
            } elseif (strcasecmp($manifest['slug'], $manifest['folder']) !== 0) {
                $problems[] = "Slug must match the folder name [{$manifest['folder']}].";
            } elseif ($this->makeProvider($manifest) === null) {
                $problems[] = "Provider class [{$manifest['provider']}] was not found.";
            }

            if (! $this->satisfies($manifest['requires_core'], $this->coreVersion())) {
                $problems[] = "Requires core {$manifest['requires_core']} (current {$this->coreVersion()}).";
            }

            foreach ($manifest['depends_on'] as $dependency) {
                if (! $records->has($dependency)) {
                    $problems[] = "Depends on [{$dependency}], which is not installed.";
                } elseif (! $records->get($dependency)?->isActive()) {
                    $problems[] = "Depends on [{$dependency}], which is inactive.";
                }
            }

            $requiredBy = $record?->isActive() ? $this->activeDependents($slug) : [];

            if ($requiredBy !== []) {
                $problems[] = 'Required by active add-on: '.implode(', ', $requiredBy).'.';
            }

            $usable = $manifest['error'] === null
                && $this->makeProvider($manifest) !== null
                && $this->satisfies($manifest['requires_core'], $this->coreVersion());

            $depsInstalled = collect($manifest['depends_on'])->every(fn ($dep) => $records->has($dep));
            $depsActive = collect($manifest['depends_on'])->every(fn ($dep) => $records->get($dep)?->isActive());

            $entries[$slug] = [
                'slug' => $slug,
                'name' => $manifest['name'],
                'version' => $manifest['version'],
                'description' => $manifest['description'],
                'author' => $manifest['author'],
                'requires_core' => $manifest['requires_core'],
                'depends_on' => $manifest['depends_on'],
                'provides' => $manifest['provides'],
                'path' => $manifest['path'],
                'status' => $status,
                'installed' => $record !== null,
                'active' => (bool) $record?->isActive(),
                'last_error' => $record?->last_error,
                'folder_missing' => false,
                'problems' => $problems,
                'can_install' => $record === null && $usable && $depsInstalled,
                'can_activate' => $record !== null && ! $record->isActive() && $usable && $depsActive,
                'can_deactivate' => (bool) $record?->isActive() && $requiredBy === [],
                'can_uninstall' => $record !== null && $requiredBy === [],
            ];
        }

        return array_values($entries);
    }
}
