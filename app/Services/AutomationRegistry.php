<?php

namespace App\Services;

use App\Automation\Modules\DailyReportAlerts\DailyReportAlertsModule;
use App\Automation\Modules\InventoryRetention\InventoryRetentionModule;
use App\Automation\Modules\OrderConfirmationBot\OrderConfirmationBotModule;
use App\Automation\Modules\ProductContent\ProductContentModule;
use App\Automation\Modules\RiskScore\RiskScoreModule;
use App\Automation\Modules\SupportChatbot\SupportChatbotModule;
use App\Models\AutomationModule as ModuleRow;
use Illuminate\Support\Facades\Cache;

/**
 * Single registry for all automation modules.
 * 7th module = new folder + ONE line in $modules below. Nothing else changes.
 */
class AutomationRegistry
{
    public const CACHE_KEY = 'automation.registry';

    /** @var array<class-string> */
    protected static array $modules = [
        RiskScoreModule::class,
        ProductContentModule::class,
        OrderConfirmationBotModule::class,
        SupportChatbotModule::class,
        DailyReportAlertsModule::class,
        InventoryRetentionModule::class,
    ];

    /** All module contracts, keyed by key(). */
    public static function all(): array
    {
        $out = [];
        foreach (self::$modules as $class) {
            $module = app($class);
            $out[$module->key()] = $module;
        }

        return $out;
    }

    public static function get(string $key): ?object
    {
        return self::all()[$key] ?? null;
    }

    protected static function rows(): array
    {
        return Cache::remember(self::CACHE_KEY, 3600, function () {
            return ModuleRow::all()->keyBy('key')->all();
        });
    }

    public static function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    public static function row(string $key): ?ModuleRow
    {
        return self::rows()[$key] ?? null;
    }

    /** Master kill switch (emergency). OFF disables everything at once. */
    public static function masterOn(): bool
    {
        return (bool) setting('automation_master_switch', true);
    }

    public static function mode(string $key): string
    {
        $row = self::row($key);
        if (! $row || ! $row->is_enabled) {
            return ModuleRow::MODE_OFF;
        }

        return in_array($row->mode, [ModuleRow::MODE_SHADOW, ModuleRow::MODE_LIVE], true)
            ? $row->mode
            : ModuleRow::MODE_OFF;
    }

    public static function isEnabled(string $key): bool
    {
        return self::masterOn() && self::mode($key) !== ModuleRow::MODE_OFF;
    }

    public static function isLive(string $key): bool
    {
        return self::masterOn() && self::mode($key) === ModuleRow::MODE_LIVE;
    }

    public static function isShadow(string $key): bool
    {
        return self::masterOn() && self::mode($key) === ModuleRow::MODE_SHADOW;
    }

    /** Merged settings: DB values over module defaults. */
    public static function settings(string $key): array
    {
        $module = self::get($key);
        $defaults = $module ? $module->defaultSettings() : [];

        return array_merge($defaults, self::row($key)?->settings ?? []);
    }

    /** Today's event count for a module (Overview cards). */
    public static function todayCount(string $key): int
    {
        return \App\Models\AutomationActivityLog::where('module_key', $key)
            ->whereDate('created_at', today())
            ->count();
    }

    /** Last activity time for a module (Overview cards). */
    public static function lastActivity(string $key): ?string
    {
        return \App\Models\AutomationActivityLog::where('module_key', $key)
            ->latest('id')
            ->value('created_at');
    }
}
