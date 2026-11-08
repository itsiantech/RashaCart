<?php

namespace App\Support;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\HtmlString;

class Hooks
{
    /** @var array<string, array<int, list<callable>>> */
    protected static array $filters = [];

    /** @var array<string, array<int, list<callable>>> */
    protected static array $actions = [];

    /** @var array<string, array<int, list<string|callable>>> */
    protected static array $slots = [];

    public static function addFilter(string $name, callable $callback, int $priority = 10): void
    {
        self::$filters[$name][$priority][] = $callback;
    }

    public static function filter(string $name, mixed $value, mixed ...$args): mixed
    {
        if (empty(self::$filters[$name])) {
            return $value;
        }

        $callbacks = self::$filters[$name];
        ksort($callbacks);

        foreach ($callbacks as $group) {
            foreach ($group as $callback) {
                $value = $callback($value, ...$args);
            }
        }

        return $value;
    }

    public static function addAction(string $name, callable $callback, int $priority = 10): void
    {
        self::$actions[$name][$priority][] = $callback;
    }

    public static function doAction(string $name, mixed ...$args): void
    {
        if (empty(self::$actions[$name])) {
            return;
        }

        $callbacks = self::$actions[$name];
        ksort($callbacks);

        foreach ($callbacks as $group) {
            foreach ($group as $callback) {
                try {
                    $callback(...$args);
                } catch (\Throwable $e) {
                    // A failing add-on must never break the core flow.
                    Log::error("Hook action [{$name}] failed: ".$e->getMessage(), ['exception' => $e]);
                }
            }
        }
    }

    /**
     * Register a UI slot. $view is either a Blade view name or a closure
     * receiving the data array and returning a string.
     */
    public static function addSlot(string $name, string|callable $view, int $priority = 10): void
    {
        self::$slots[$name][$priority][] = $view;
    }

    public static function hasSlot(string $name): bool
    {
        return ! empty(self::$slots[$name]);
    }

    public static function renderSlot(string $name, array $data = []): HtmlString
    {
        if (empty(self::$slots[$name])) {
            return new HtmlString('');
        }

        $slots = self::$slots[$name];
        ksort($slots);

        $html = '';

        foreach ($slots as $group) {
            foreach ($group as $slot) {
                try {
                    $html .= is_string($slot)
                        ? view($slot, $data)->render()
                        : (string) $slot($data);
                } catch (\Throwable $e) {
                    Log::error("Add-on slot [{$name}] failed: ".$e->getMessage(), ['exception' => $e]);
                }
            }
        }

        return new HtmlString($html);
    }

    /**
     * Drop every registered callback/slot. Used by the test-suite so one
     * test's add-on registrations never leak into the next.
     */
    public static function flush(): void
    {
        self::$filters = [];
        self::$actions = [];
        self::$slots = [];
    }
}
