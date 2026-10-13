<?php

namespace App\Livewire\Admin;

use App\Models\TrackingAuditLog;
use App\Models\TrackingPlatform;
use App\Services\Tracking\TrackingManager;
use Livewire\Component;

class ServerSideTracking extends Component
{
    public array $platforms = [];
    public string $activeTab = 'meta';
    public string $setupTab = 'guide'; // guide, progress
    public bool $showGuide = true;

    public function mount()
    {
        $this->loadPlatforms();
    }

    public function loadPlatforms()
    {
        $platforms = TrackingPlatform::all();

        $this->platforms = [
            'meta' => $this->getPlatformData($platforms, 'meta'),
            'tiktok' => $this->getPlatformData($platforms, 'tiktok'),
            'ga4' => $this->getPlatformData($platforms, 'ga4'),
            'google_ads' => $this->getPlatformData($platforms, 'google_ads'),
        ];
    }

    protected function getPlatformData($platforms, $platformKey)
    {
        $platform = $platforms->where('platform', $platformKey)->first();

        if (!$platform) {
            return [
                'id' => null,
                'name' => '',
                'pixel_id' => '',
                'access_token' => '',
                'access_token_masked' => '',
                'test_event_code' => '',
                'is_active' => false,
                'is_test_mode' => false,
                'api_version' => $platformKey === 'meta' ? 'v19.0' : '',
                'extra' => [],
            ];
        }

        return [
            'id' => $platform->id,
            'name' => $platform->name,
            'pixel_id' => $platform->pixel_id,
            'access_token' => '', // Never send to frontend
            'access_token_masked' => $platform->masked_token,
            'test_event_code' => $platform->test_event_code,
            'is_active' => $platform->is_active,
            'is_test_mode' => $platform->is_test_mode,
            'api_version' => $platform->api_version,
            'extra' => $platform->extra ?? [],
        ];
    }

    public function save()
    {
        foreach ($this->platforms as $platformKey => $data) {
            $platform = TrackingPlatform::updateOrCreate(
                ['platform' => $platformKey],
                [
                    'name' => $data['name'],
                    'pixel_id' => $data['pixel_id'],
                    'test_event_code' => $data['test_event_code'],
                    'is_active' => $data['is_active'],
                    'is_test_mode' => $data['is_test_mode'],
                    'api_version' => $data['api_version'],
                    'extra' => $data['extra'],
                ]
            );

            // Update access token only if provided (and not masked)
            if (!empty($data['access_token']) && ($data['access_token'] !== ($data['access_token_masked'] ?? null))) {
                $platform->access_token = $data['access_token'];
                $platform->save();
            }

            // Ensure default event rules exist so events are never silently skipped
            \App\Models\TrackingEventRule::ensureDefaults($platform->id);

            // Log audit
            if ($platform->wasRecentlyCreated || $platform->wasChanged()) {
                TrackingAuditLog::log($platform, 'updated', [
                    'pixel_id' => $data['pixel_id'],
                    'is_active' => $data['is_active'],
                ]);
            }
        }

        // Clear cache
        app(TrackingManager::class)->clearCache();

        $this->loadPlatforms();
        $this->dispatch('success', message: 'Tracking settings saved successfully!');
    }

    public function testConnection(string $platformKey)
    {
        $platform = TrackingPlatform::where('platform', $platformKey)->first();

        if (!$platform) {
            $this->dispatch('error', message: 'Platform not found');
            return;
        }

        $result = app(TrackingManager::class)->testConnection($platform);

        if ($result['success']) {
            $this->dispatch('success', message: $result['message']);
        } else {
            $this->dispatch('error', message: $result['message']);
        }
    }

    public function sendTestEvent(string $platformKey)
    {
        $platform = TrackingPlatform::where('platform', $platformKey)->first();

        if (!$platform) {
            $this->dispatch('error', message: 'Platform not found');
            return;
        }

        $result = app(TrackingManager::class)->sendTestEvent($platform);

        if ($result['success']) {
            $this->dispatch('success', message: $result['message']);
        } else {
            $this->dispatch('error', message: $result['message']);
        }
    }

    public function render()
    {
        return view('livewire.admin.server-side-tracking')
            ->layout('livewire.admin.layouts.app')
            ->layoutData(['page-title' => 'Server Side Tracking']);
    }
}
