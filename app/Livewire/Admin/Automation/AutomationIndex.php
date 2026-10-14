<?php

namespace App\Livewire\Admin\Automation;

use App\Models\AutomationActivityLog;
use App\Models\AutomationAuditLog;
use App\Models\AutomationModule as ModuleRow;
use App\Models\Settings;
use App\Services\AutomationRegistry;
use Livewire\Component;

class AutomationIndex extends Component
{
    public bool $masterOn = true;

    public function mount(): void
    {
        $this->masterOn = AutomationRegistry::masterOn();
    }

    protected function ensureSuperAdmin(): void
    {
        abort_unless(auth()->user()?->hasRole('admin'), 403, 'শুধু super-admin বদলাতে পারবে।');
    }

    /** Toggle Off <-> Shadow (safe default). Live is set via setMode(). */
    public function toggle(string $key): void
    {
        $this->ensureSuperAdmin();
        $row = ModuleRow::where('key', $key)->firstOrFail();
        $old = ['is_enabled' => $row->is_enabled, 'mode' => $row->mode];

        if ($row->is_enabled) {
            $row->update(['is_enabled' => false, 'mode' => ModuleRow::MODE_OFF]);
            AutomationAuditLog::record($key, 'disabled', $old, ['is_enabled' => false, 'mode' => 'off']);
            $this->dispatch('success', message: 'বন্ধ করা হয়েছে।');
        } else {
            // Risky modules open in Shadow first, never straight to Live
            $row->update([
                'is_enabled' => true,
                'mode' => ModuleRow::MODE_SHADOW,
                'last_enabled_at' => now(),
                'enabled_by' => auth()->id(),
            ]);
            AutomationAuditLog::record($key, 'enabled', $old, ['is_enabled' => true, 'mode' => 'shadow']);
            $this->dispatch('success', message: 'Shadow mode-এ চালু হয়েছে (শুধু log, কোনো action নয়)।');
        }
    }

    public function setMode(string $key, string $mode): void
    {
        $this->ensureSuperAdmin();
        abort_unless(in_array($mode, ['off', 'shadow', 'live'], true), 400);

        $row = ModuleRow::where('key', $key)->firstOrFail();
        $old = ['is_enabled' => $row->is_enabled, 'mode' => $row->mode];
        $row->update([
            'is_enabled' => $mode !== 'off',
            'mode' => $mode,
            'last_enabled_at' => $mode !== 'off' ? now() : $row->last_enabled_at,
            'enabled_by' => $mode !== 'off' ? auth()->id() : $row->enabled_by,
        ]);
        AutomationAuditLog::record($key, 'mode_changed', $old, ['is_enabled' => $row->is_enabled, 'mode' => $mode]);
        $this->dispatch('success', message: "Mode: {$mode}।");
    }

    public function toggleMaster(): void
    {
        $this->ensureSuperAdmin();
        $old = $this->masterOn;
        $this->masterOn = ! $this->masterOn;

        Settings::updateOrCreate(['key' => 'automation_master_switch'], ['value' => $this->masterOn ? 1 : 0]);
        Settings::clearCache();
        AutomationAuditLog::record('__master__', $this->masterOn ? 'enabled' : 'disabled',
            ['on' => $old], ['on' => $this->masterOn]);

        $this->dispatch('success', message: $this->masterOn ? 'Master switch ON।' : 'Master switch OFF — সব automation থেমে গেছে।');
    }

    protected function cards(): array
    {
        $out = [];
        foreach (AutomationRegistry::all() as $key => $module) {
            $row = AutomationRegistry::row($key);
            $mode = AutomationRegistry::mode($key);
            try {
                $health = $module->healthCheck();
            } catch (\Throwable $e) {
                $health = ['healthy' => false, 'message' => $e->getMessage()];
            }
            $out[] = [
                'key' => $key,
                'name' => $module->name(),
                'description' => $module->description(),
                'icon' => $module->icon(),
                'status' => $module->status(), // available|coming_soon
                'mode' => $mode, // off|shadow|live
                'healthy' => (bool) ($health['healthy'] ?? false),
                'health_message' => $health['message'] ?? '',
                'today' => AutomationRegistry::todayCount($key),
                'last' => AutomationRegistry::lastActivity($key),
            ];
        }

        return $out;
    }

    protected function budget(): array
    {
        $dailyLimit = (float) (setting('automation_daily_budget') ?? 500);
        $monthlyLimit = (float) (setting('automation_monthly_budget') ?? 5000);
        $today = \App\Models\AiUsageLog::whereDate('created_at', today())->sum('cost');
        $month = \App\Models\AiUsageLog::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)->sum('cost');

        return [
            'today' => (float) $today, 'daily_limit' => $dailyLimit,
            'month' => (float) $month, 'monthly_limit' => $monthlyLimit,
            'warn' => ($dailyLimit > 0 && $today >= 0.8 * $dailyLimit)
                || ($monthlyLimit > 0 && $month >= 0.8 * $monthlyLimit),
        ];
    }

    public function render()
    {
        return view('livewire.admin.automation.automation-index', [
            'cards' => $this->cards(),
            'budget' => $this->budget(),
            'feed' => AutomationActivityLog::latest('id')->take(20)->get(),
            'isSuperAdmin' => (bool) auth()->user()?->hasRole('admin'),
        ])->layout('livewire.admin.layouts.app')
            ->layoutData(['page-title' => 'Automation']);
    }
}
