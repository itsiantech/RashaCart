<?php

namespace App\Livewire\Admin\LandingPage;

use App\Models\LandingPage;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class LandingPageIndex extends Component
{
    public $landingPages;

    public function render()
    {
        $this->landingPages = LandingPage::withTrashed()->latest()->get();

        return view('livewire.admin.landing-page.landing-page-index')->layout('livewire.admin.layouts.app');
    }

    public function delete($id)
    {
        $landingPage = LandingPage::findOrFail($id);
        if ($landingPage->hero_image) {
            Storage::disk('public')->delete($landingPage->hero_image);
        }
        if ($landingPage->secondary_image) {
            Storage::disk('public')->delete($landingPage->secondary_image);
        }
        $landingPage->delete();

        $this->dispatch('success', message: 'Landing Page Deleted!');
    }

    public function restore($id)
    {
        $landingPage = LandingPage::withTrashed()->findOrFail($id);
        $landingPage->restore();

        $this->dispatch('success', message: 'Landing Page Restored!');
    }

    public function forceDelete($id)
    {
        $landingPage = LandingPage::withTrashed()->findOrFail($id);
        if ($landingPage->hero_image) {
            Storage::disk('public')->delete($landingPage->hero_image);
        }
        if ($landingPage->secondary_image) {
            Storage::disk('public')->delete($landingPage->secondary_image);
        }
        $landingPage->forceDelete();

        $this->dispatch('success', message: 'Landing Page Permanently Deleted!');
    }

    public function toggleStatus($id)
    {
        $landingPage = LandingPage::findOrFail($id);
        $landingPage->status = !$landingPage->status;
        $landingPage->save();

        $this->dispatch('success', message: 'Status Updated!');
    }
}
