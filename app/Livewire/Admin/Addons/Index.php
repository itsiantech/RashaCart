<?php

namespace App\Livewire\Admin\Addons;

use App\Addons\AddonException;
use App\Addons\AddonManager;
use Livewire\Component;

class Index extends Component
{
    public ?string $uninstallSlug = null;

    public bool $dropData = false;

    public ?string $actionError = null;

    public function install(string $slug): void
    {
        $this->run(
            fn (AddonManager $manager) => $manager->install($slug),
            "[{$slug}] installed. Activate it when you are ready."
        );
    }

    public function activate(string $slug): void
    {
        $this->run(
            fn (AddonManager $manager) => $manager->activate($slug),
            "[{$slug}] is now active."
        );
    }

    public function deactivate(string $slug): void
    {
        $this->run(
            fn (AddonManager $manager) => $manager->deactivate($slug),
            "[{$slug}] is now inactive. Its data is kept and returns when you activate it again."
        );
    }

    public function toggle(string $slug): void
    {
        $deactivating = app(AddonManager::class)->isActive($slug);

        $this->run(
            fn (AddonManager $manager) => $deactivating
                ? $manager->deactivate($slug)
                : $manager->activate($slug),
            $deactivating
                ? "[{$slug}] is now inactive. Its data is kept and returns when you activate it again."
                : "[{$slug}] is now active."
        );
    }

    public function confirmUninstall(string $slug): void
    {
        $this->uninstallSlug = $slug;
        $this->dropData = false;
        $this->actionError = null;
    }

    public function cancelUninstall(): void
    {
        $this->uninstallSlug = null;
        $this->dropData = false;
    }

    public function uninstall(): void
    {
        $slug = $this->uninstallSlug;

        if ($slug === null) {
            return;
        }

        $dropData = $this->dropData;
        $message = $dropData
            ? "[{$slug}] was uninstalled and its data was deleted."
            : "[{$slug}] was uninstalled. Its data was kept in the database.";

        $this->uninstallSlug = null;
        $this->dropData = false;

        $this->run(fn (AddonManager $manager) => $manager->uninstall($slug, $dropData), $message);
    }

    protected function run(callable $action, ?string $successMessage): void
    {
        $this->actionError = null;

        try {
            $action(app(AddonManager::class));
        } catch (AddonException $e) {
            $this->actionError = $e->getMessage();
            $this->dispatch('error', message: $e->getMessage());

            return;
        } catch (\Throwable $e) {
            report($e);
            $this->actionError = 'Something went wrong: '.$e->getMessage();
            $this->dispatch('error', message: $this->actionError);

            return;
        }

        if ($successMessage !== null) {
            session()->flash('addon_status', $successMessage);
            $this->dispatch('success', message: $successMessage);
        }

        // Always land back on the list so the sidebar, tabs and cards are rebuilt
        // from the new active state.
        $this->redirect(route('admin.addons.index'), navigate: true);
    }

    public function render()
    {
        $manager = app(AddonManager::class);
        $addons = $manager->all();

        return view('livewire.admin.addons.index', [
            'addons' => $addons,
            'tableMissing' => ! $manager->hasTable(),
            'addonsPath' => $manager->path(),
            'coreVersion' => $manager->coreVersion(),
            'failedAddons' => array_values(array_filter($addons, fn ($addon) => ! empty($addon['last_error']))),
            'statusLabels' => [
                'not_installed' => 'Not installed',
                'installed' => 'Installed',
                'active' => 'Active',
                'inactive' => 'Inactive',
            ],
        ])->layout('livewire.admin.layouts.app');
    }
}
