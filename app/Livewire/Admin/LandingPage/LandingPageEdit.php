<?php

namespace App\Livewire\Admin\LandingPage;

use App\Models\LandingPage;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;

class LandingPageEdit extends Component
{
    use WithFileUploads;

    public $landingPageId;
    public $title;
    public $slug;
    public $product_ids = [];
    public $main_title;
    public $hero_image;
    public $hero_image_preview;
    public $countdown_end_date;
    public $regular_price;
    public $discount_price;
    public $benefits_title;
    public $benefits_list = [];
    public $benefits_icon = 'bx-check-circle';
    public $trust_title;
    public $trust_list = [];
    public $trust_icon = 'bx-right-arrow-circle';
    public $secondary_image;
    public $secondary_image_preview;
    public $contact_number;
    public $status = true;
    public $show_hero = true;
    public $show_countdown = true;
    public $show_price = true;
    public $show_benefits = true;
    public $show_trust = true;
    public $show_contact = true;
    public $show_checkout = true;
    public $show_footer = true;

    public $availableProducts;

    protected function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'slug' => 'required|string|unique:landing_pages,slug,' . $this->landingPageId,
            'main_title' => 'nullable|string|max:255',
            'hero_image' => 'nullable|image|max:3072',
            'countdown_end_date' => 'nullable|date',
            'regular_price' => 'nullable|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0',
            'benefits_title' => 'nullable|string|max:255',
            'benefits_icon' => 'nullable|string|in:' . implode(',', LandingPage::LIST_ICONS),
            'trust_title' => 'nullable|string|max:255',
            'trust_icon' => 'nullable|string|in:' . implode(',', LandingPage::LIST_ICONS),
            'secondary_image' => 'nullable|image|max:3072',
            'contact_number' => 'nullable|string|max:20',
            'status' => 'boolean',
            'show_hero' => 'boolean',
            'show_countdown' => 'boolean',
            'show_price' => 'boolean',
            'show_benefits' => 'boolean',
            'show_trust' => 'boolean',
            'show_contact' => 'boolean',
            'show_checkout' => 'boolean',
            'show_footer' => 'boolean',
        ];
    }

    public function mount($id)
    {
        $this->landingPageId = $id;
        $landingPage = LandingPage::findOrFail($id);

        $this->title = $landingPage->title;
        $this->slug = $landingPage->slug;
        $this->product_ids = $landingPage->product_ids ?? [];
        $this->main_title = $landingPage->main_title;
        $this->hero_image_preview = $landingPage->hero_image;
        $this->countdown_end_date = $landingPage->countdown_end_date ? $landingPage->countdown_end_date->format('Y-m-d\TH:i') : null;
        $this->regular_price = $landingPage->regular_price;
        $this->discount_price = $landingPage->discount_price;
        $this->benefits_title = $landingPage->benefits_title;
        $this->benefits_list = $landingPage->benefits_list ?? [];
        $this->benefits_icon = $landingPage->benefits_icon ?: 'bx-check-circle';
        $this->trust_title = $landingPage->trust_title;
        $this->trust_list = $landingPage->trust_list ?? [];
        $this->trust_icon = $landingPage->trust_icon ?: 'bx-right-arrow-circle';
        $this->secondary_image_preview = $landingPage->secondary_image;
        $this->contact_number = $landingPage->contact_number;
        $this->status = $landingPage->status;
        $this->show_hero = (bool) $landingPage->show_hero;
        $this->show_countdown = (bool) $landingPage->show_countdown;
        $this->show_price = (bool) $landingPage->show_price;
        $this->show_benefits = (bool) $landingPage->show_benefits;
        $this->show_trust = (bool) $landingPage->show_trust;
        $this->show_contact = (bool) $landingPage->show_contact;
        $this->show_checkout = (bool) $landingPage->show_checkout;
        $this->show_footer = (bool) $landingPage->show_footer;

        $this->availableProducts = Product::where('status', 1)->get();
    }

    public function updatedTitle($value)
    {
        $this->slug = Str::slug($value);
    }

    public function addBenefit()
    {
        $this->benefits_list[] = '';
    }

    public function removeBenefit($index)
    {
        unset($this->benefits_list[$index]);
        $this->benefits_list = array_values($this->benefits_list);
    }

    public function addTrust()
    {
        $this->trust_list[] = '';
    }

    public function removeTrust($index)
    {
        unset($this->trust_list[$index]);
        $this->trust_list = array_values($this->trust_list);
    }

    public function update()
    {
        $this->validate();

        $landingPage = LandingPage::findOrFail($this->landingPageId);

        $data = [
            'title' => $this->title,
            'slug' => $this->slug,
            'product_ids' => $this->product_ids,
            'main_title' => $this->main_title,
            'countdown_end_date' => $this->countdown_end_date,
            'regular_price' => $this->regular_price,
            'discount_price' => $this->discount_price,
            'benefits_title' => $this->benefits_title,
            'benefits_list' => array_filter($this->benefits_list),
            'benefits_icon' => $this->benefits_icon,
            'trust_title' => $this->trust_title,
            'trust_list' => array_filter($this->trust_list),
            'trust_icon' => $this->trust_icon,
            'contact_number' => $this->contact_number,
            'status' => $this->status,
            'show_hero' => $this->show_hero,
            'show_countdown' => $this->show_countdown,
            'show_price' => $this->show_price,
            'show_benefits' => $this->show_benefits,
            'show_trust' => $this->show_trust,
            'show_contact' => $this->show_contact,
            'show_checkout' => $this->show_checkout,
            'show_footer' => $this->show_footer,
        ];

        if ($this->hero_image) {
            if ($landingPage->hero_image) {
                Storage::disk('public')->delete($landingPage->hero_image);
            }
            $data['hero_image'] = $this->hero_image->store('landing-pages', 'public');
        }

        if ($this->secondary_image) {
            if ($landingPage->secondary_image) {
                Storage::disk('public')->delete($landingPage->secondary_image);
            }
            $data['secondary_image'] = $this->secondary_image->store('landing-pages', 'public');
        }

        $landingPage->update($data);

        $this->dispatch('success', message: 'Landing Page Updated Successfully!');

        return redirect()->route('admin.landing-page.index');
    }

    public function render()
    {
        return view('livewire.admin.landing-page.landing-page-edit')->layout('livewire.admin.layouts.app');
    }
}
