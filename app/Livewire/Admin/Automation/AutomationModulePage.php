<?php

namespace App\Livewire\Admin\Automation;

use App\Models\AutomationModule as ModuleRow;
use App\Models\AutomationAuditLog;
use App\Services\AutomationRegistry;
use Livewire\Component;

class AutomationModulePage extends Component
{
    public string $key = '';

    public array $form = [];

    protected function module(): object
    {
        $module = AutomationRegistry::get($this->key);
        abort_unless($module, 404);

        return $module;
    }

    protected function canView(): bool
    {
        $user = auth()->user();

        return $user && ($user->hasRole('admin') || $user->can('automation.'.$this->key.'.view'));
    }

    public function mount(string $key): void
    {
        $this->key = $key;
        abort_unless($this->canView(), 403);
        $this->module(); // 404 on unknown key

        $this->form = AutomationRegistry::settings($key);
    }

    protected function ensureSuperAdmin(): void
    {
        abort_unless(auth()->user()?->hasRole('admin'), 403, 'শুধু super-admin বদলাতে পারবে।');
    }

    public function save(): void
    {
        $this->ensureSuperAdmin();
        $module = $this->module();

        $rules = [];
        foreach ($module->settingsSchema() as $field) {
            $rules['form.'.$field['name']] = $field['rules'] ?? 'nullable';
        }
        $this->validate($rules);

        // Checkbox/toggle unchecked values arrive as false — keep types clean
        foreach ($module->settingsSchema() as $field) {
            if (($field['type'] ?? '') === 'toggle') {
                $this->form[$field['name']] = (bool) ($this->form[$field['name']] ?? false);
            }
        }

        $row = ModuleRow::where('key', $this->key)->firstOrFail();
        $old = $row->settings;
        $row->update(['settings' => $this->form]); // observer clears cache instantly
        AutomationAuditLog::record($this->key, 'settings_changed', $old, $this->form);

        $this->dispatch('success', message: 'Settings save হয়েছে।');
    }

    public function render()
    {
        $module = $this->module();
        $row = AutomationRegistry::row($this->key);

        return view('livewire.admin.automation.automation-module', [
            'module' => $module,
            'mode' => $row ? AutomationRegistry::mode($this->key) : 'off',
            'isSuperAdmin' => (bool) auth()->user()?->hasRole('admin'),
        ])->layout('livewire.admin.layouts.app')
            ->layoutData(['page-title' => $module->name()]);
    }
}
