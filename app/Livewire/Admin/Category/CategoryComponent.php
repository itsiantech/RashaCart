<?php

namespace App\Livewire\Admin\Category;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Rule;

class CategoryComponent extends Component
{
    use WithFileUploads;

    public $categories;
    public $category_id;
    
    #[Rule('required|min:3|max:255')]
    public $name;
    
    public $slug;
    
    #[Rule('nullable|image|max:2048')] // 2MB Max
    public $image;
    
    public $status = true;
    public $is_featured = false;
    public $parent_id = null;
    
    public $showForm = false;
    public $isEdit = false;
    public $search = '';

    protected $listeners = ['deleteConfirmed' => 'deleteCategory'];

    public function mount()
    {
        $this->loadCategories();
    }

    public function loadCategories()
    {
        $this->categories = Category::when($this->search, function($query) {
                return $query->where('name', 'like', '%'.$this->search.'%');
            })
            ->with('children')
            ->whereNull('parent_id')
            ->orderBy('name')
            ->get();
    }

    public function updatedSearch()
    {
        $this->loadCategories();
    }

    public function updatedName($value)
    {
        $this->slug = Str::slug($value);
    }

    public function showAddForm()
    {
        $this->resetForm();
        $this->showForm = true;
        $this->isEdit = false;
    }

    public function showEditForm($id)
    {
        $category = Category::findOrFail($id);
        
        $this->category_id = $id;
        $this->name = $category->name;
        $this->slug = $category->slug;
        $this->status = $category->status; 
        $this->is_featured = $category->is_featured;
        $this->parent_id = $category->parent_id;
        
        $this->showForm = true;
        $this->isEdit = true;
    }

    public function resetForm()
    {
        $this->reset([
            'category_id',
            'name',
            'slug',
            'image',
            'status',
            'is_featured',
            'parent_id'
        ]);
        $this->resetValidation();
    }

    public function saveCategory()
    {
        $validated = $this->validate([
            'name' => 'required|min:3|max:255',
            'slug' => 'required|unique:categories,slug,'.$this->category_id,
            'image' => 'nullable|image|max:2048',
            'status' => 'boolean',
            'is_featured' => 'boolean',
            'parent_id' => 'nullable|exists:categories,id'
        ]);

        $data = [
            'name' => $this->name,
            'slug' => $this->slug,
            'status' => $this->status, 
            'is_featured' => $this->is_featured,
            'parent_id' => $this->parent_id
        ];

        // Handle image upload
        if ($this->image) {
            
            if ($this->isEdit) {
                $oldCategory = Category::find($this->category_id);
                if ($oldCategory->image) {
                    Storage::delete($oldCategory->image);
                }
            }
            
            $imagePath = $this->image->store('categories', 'public');
            $data['image'] = $imagePath;
        }

        if ($this->isEdit) {
            Category::find($this->category_id)->update($data);
            $message = 'Category updated successfully!';
        } else {
            Category::create($data);
            $message = 'Category added successfully!';
        }

        
        $this->dispatch('success', message: $message);
        $this->loadCategories();
        $this->showForm = false;
        $this->resetForm();
    }

    public function confirmDelete($id)
    {
        $this->category_id = $id;
        $this->dispatch('showDeleteConfirmation');
    }

    public function deleteCategory()
    {
        $category = Category::find($this->category_id);
        if ($category->image) {
            Storage::delete($category->image);
        }
        
        $category->delete();
         $this->dispatch('success', message: 'Category Created!');
        $this->loadCategories();
    }

    public function render()
    {
        return view('livewire.admin.category.category-component')->layout('livewire.admin.layouts.app');
    }
}