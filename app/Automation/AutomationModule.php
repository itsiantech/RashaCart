<?php

namespace App\Automation;

/**
 * Contract every automation module must implement.
 * New module = new folder under app/Automation/Modules + one registry line.
 */
interface AutomationModule
{
    /** Stable key, e.g. 'risk_score'. Matches automation_modules.key. */
    public function key(): string;

    public function name(): string;

    /** Short Bangla description for cards/menus. */
    public function description(): string;

    /** Boxicons class, e.g. 'bx bx-shield-quarter'. */
    public function icon(): string;

    /** 'available' | 'coming_soon'. */
    public function status(): string;

    /** Default per-module settings (stored in automation_modules.settings). */
    public function defaultSettings(): array;

    /**
     * Schema-driven settings form. Each field:
     * ['name','label','type'(text|number|toggle|select|textarea),'default',
     *  'rules','help'(Bangla),'options'(for select)]
     */
    public function settingsSchema(): array;

    /** [['method','uri','name'], ...] mounted under admin/automation (empty for stubs). */
    public function routes(): array;

    /** Spatie permission suffixes, e.g. ['view']. Full: automation.{key}.{suffix}. */
    public function permissions(): array;

    /** Called on app boot (register listeners etc.). Stubs do nothing. */
    public function boot(): void;

    /** [['command','expression'], ...] for the scheduler. Stubs return []. */
    public function scheduledTasks(): array;

    /** ['healthy' => bool, 'message' => string] for the Overview health dot. */
    public function healthCheck(): array;

    /** Collapsible Bangla guide: what it does, safe enabling, verification. */
    public function guide(): array;

    /** Planned features for coming_soon placeholder pages. */
    public function plannedFeatures(): array;
}
