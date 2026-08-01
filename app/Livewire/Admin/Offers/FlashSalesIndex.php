<?php

namespace App\Livewire\Admin\Offers;

use App\Models\FlashSale;
use Livewire\Component;
use Livewire\WithPagination;

class FlashSalesIndex extends Component
{
    use WithPagination;

    public string $search = '';

    public string $filterStatus = ''; // '', 'live', 'upcoming', 'expired'

    // Confirm delete
    public ?int $deleteId = null;

    public string $deleteName = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'filterStatus' => ['except' => ''],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    // ─── Toggle Active ────────────────────────────────────────────────────────

    public function toggleActive(int $id): void
    {
        $flashSale = FlashSale::findOrFail($id);
        $flashSale->update(['is_active' => ! $flashSale->is_active]);

        session()->flash('success', 'Flash sale status updated.');
    }

    // ─── Delete ───────────────────────────────────────────────────────────────

    public function confirmDelete(int $id): void
    {
        $flashSale = FlashSale::findOrFail($id);
        $this->deleteId = $id;
        $this->deleteName = $flashSale->title;

        $this->dispatch('show-delete-modal');
    }

    public function delete(): void
    {
        if ($this->deleteId) {
            FlashSale::findOrFail($this->deleteId)->delete();
            session()->flash('success', 'Flash sale deleted successfully.');
        }

        $this->deleteId = null;
        $this->deleteName = '';
        $this->dispatch('hide-delete-modal');
    }

    public function cancelDelete(): void
    {
        $this->deleteId = null;
        $this->deleteName = '';
        $this->dispatch('hide-delete-modal');
    }

    // ─── Render ───────────────────────────────────────────────────────────────

    public function render()
    {
        $query = FlashSale::withCount('items')
            ->when($this->search, fn ($q) => $q->where('title', 'like', '%'.$this->search.'%'))
            ->latest();

        $flashSales = $query->paginate(10);

        // Attach status attribute for filtering in blade (or filter in PHP)
        if ($this->filterStatus) {
            $flashSales = $query->paginate(100); // load more to filter
            $filtered = $flashSales->getCollection()->filter(
                fn ($fs) => $fs->status === $this->filterStatus
            );
            $flashSales->setCollection($filtered);
        }

        return view('livewire.admin.offers.flash-sales-index', compact('flashSales'))
            ->layout('livewire.admin.layouts.app', ['title' => 'Flash Sales']);
    }
}
