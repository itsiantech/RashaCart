<?php

namespace App\Livewire\Admin\Category;

use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;

class CategoryComponent extends Component
{
    use WithFileUploads;

    const MAX_DEPTH = 3; // Category > Sub Category > Sub Sub Category

    public $forest = [];

    public $parentOptions = [];

    public $totalCount = 0;

    public $activeCount = 0;

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
        $all = Category::orderBy('sort_order')->orderBy('name')->get();

        $directCounts = DB::table('products')
            ->select('category_id', DB::raw('COUNT(*) as total'))
            ->groupBy('category_id')
            ->pluck('total', 'category_id');

        $byParent = $all->groupBy(fn ($c) => $c->parent_id ?: 'root');

        // Inclusive product count (self + all descendants)
        $inclusive = [];
        $sumSubtree = function ($node) use (&$sumSubtree, $byParent, $directCounts, &$inclusive) {
            $total = (int) ($directCounts[$node->id] ?? 0);
            foreach ($byParent->get($node->id, collect()) as $child) {
                $total += $sumSubtree($child);
            }

            return $inclusive[$node->id] = $total;
        };
        foreach ($byParent->get('root', collect()) as $root) {
            $sumSubtree($root);
        }

        $term = mb_strtolower(trim($this->search));
        $matches = fn ($node) => $term !== '' && str_contains(mb_strtolower($node->name), $term);

        $makeRow = function ($node, $depth) use ($byParent, $inclusive, $directCounts) {
            return [
                'id' => $node->id,
                'name' => $node->name,
                'slug' => $node->slug,
                'image' => $node->image,
                'status' => (bool) $node->status,
                'is_featured' => (bool) $node->is_featured,
                'depth' => $depth,
                'parent_id' => $node->parent_id,
                'parent_name' => null,
                'has_children' => $byParent->has($node->id),
                'products' => $inclusive[$node->id] ?? (int) ($directCounts[$node->id] ?? 0),
            ];
        };

        $this->forest = [];

        // Nested tree: every node carries its own children list so the admin
        // list renders one sortable container per level — dragging a category
        // moves its whole sub-tree instead of a single row.
        $build = function ($parentKey, $depth, $parentName) use (&$build, $byParent, $makeRow, $term, $matches) {
            $nodes = [];

            foreach ($byParent->get($parentKey, collect()) as $node) {
                $children = $build($node->id, $depth + 1, $node->name);

                // While searching keep matches plus their ancestors
                if ($term !== '' && ! $matches($node) && count($children) === 0) {
                    continue;
                }

                $row = $makeRow($node, $depth);
                $row['parent_name'] = $parentName;
                $row['children'] = $children;

                $nodes[] = $row;
            }

            return $nodes;
        };

        $this->forest = $build('root', 0, null);

        $this->totalCount = $all->count();
        $this->activeCount = $all->where('status', true)->count();

        $this->buildParentOptions($byParent);
    }

    private function buildParentOptions($byParent)
    {
        $excluded = collect();
        if ($this->isEdit && $this->category_id) {
            $excluded->push((int) $this->category_id);
            $collectDescendants = function ($id) use (&$collectDescendants, $byParent, $excluded) {
                foreach ($byParent->get($id, collect()) as $child) {
                    $excluded->push($child->id);
                    $collectDescendants($child->id);
                }
            };
            $collectDescendants((int) $this->category_id);
        }

        $options = [];
        $walk = function ($parentId, $depth) use (&$walk, $byParent, $excluded, &$options) {
            foreach ($byParent->get($parentId ?: 'root', collect()) as $node) {
                if ($excluded->contains($node->id) || $depth > self::MAX_DEPTH - 2) {
                    continue;
                }
                $options[] = [
                    'id' => $node->id,
                    'label' => str_repeat('-', $depth).($depth ? ' ' : '').$node->name,
                ];
                $walk($node->id, $depth + 1);
            }
        };
        $walk(null, 0);

        $this->parentOptions = $options;
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
        $this->status = (bool) $category->status;
        $this->is_featured = (bool) $category->is_featured;
        $this->parent_id = $category->parent_id;
        $this->image = null;

        $this->showForm = true;
        $this->isEdit = true;
        $this->loadCategories();
        $this->dispatch('scroll-to-form');
    }

    public function cancelForm()
    {
        $this->resetForm();
        $this->showForm = false;
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
            'parent_id',
        ]);
        $this->status = true;
        $this->is_featured = false;
        $this->parent_id = null;
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
            'parent_id' => 'nullable|exists:categories,id',
        ]);

        if ($this->parent_id) {
            $depth = 0;
            $cursor = Category::find($this->parent_id);
            while ($cursor) {
                if ($this->isEdit && $cursor->id == $this->category_id) {
                    $this->addError('parent_id', 'A category cannot be placed inside its own sub-category.');

                    return;
                }
                $depth++;
                $cursor = $cursor->parent_id ? Category::find($cursor->parent_id) : null;
            }

            if ($depth > self::MAX_DEPTH - 1) {
                $this->addError('parent_id', 'Only 3 levels allowed: Category > Sub Category > Sub Sub Category.');

                return;
            }
        }

        $data = [
            'name' => $this->name,
            'slug' => $this->slug,
            'status' => $this->status,
            'is_featured' => $this->is_featured,
            'parent_id' => $this->parent_id ?: null,
        ];

        if (! $this->isEdit) {
            $query = Category::query();
            $data['sort_order'] = ((int) $query->where('parent_id', $this->parent_id)->max('sort_order')) + 1;
        }

        if ($this->image) {
            if ($this->isEdit && $this->category_id) {
                $old = Category::find($this->category_id);
                if ($old?->image) {
                    Storage::disk('public')->delete($old->image);
                }
            }
            $data['image'] = $this->image->store('categories', 'public');
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

    public function deleteImage()
    {
        if (! $this->category_id) {
            return;
        }
        $category = Category::find($this->category_id);
        if ($category?->image) {
            Storage::disk('public')->delete($category->image);
            $category->update(['image' => null]);
            $this->dispatch('success', message: 'Category image removed!');
        }
        $this->image = null;
    }

    public function toggleStatus($id)
    {
        $category = Category::find($id);
        if (! $category) {
            return;
        }
        $category->update(['status' => ! $category->status]);
        $this->loadCategories();
        $this->dispatch('success', message: $category->status ? 'Category activated!' : 'Category deactivated!');
    }

    /**
     * Persist a drag-and-drop order from the tree list. The blade sends the
     * flat visible order (all levels); each parent's rows keep their relative
     * position in that list, so a root category dragged past another root
     * (together with its children) simply moves below it on the storefront.
     */
    public function reorderAll($ids = [])
    {
        $ids = collect((array) $ids)
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->unique()
            ->values();

        if ($ids->isEmpty()) {
            return;
        }

        $parents = Category::whereIn('id', $ids)
            ->pluck('parent_id', 'id');

        DB::transaction(function () use ($ids, $parents) {
            $position = [];

            foreach ($ids as $id) {
                if (! $parents->has($id)) {
                    continue;
                }

                $parentKey = (int) ($parents[$id] ?? 0);
                $position[$parentKey] = ($position[$parentKey] ?? 0) + 1;

                Category::whereKey($id)->update(['sort_order' => $position[$parentKey]]);
            }
        });

        Category::flushCache();
        $this->loadCategories();
        $this->dispatch('success', message: 'Category order updated!');
    }

    public function confirmDelete($id)
    {
        $this->category_id = $id;
        $this->dispatch('showDeleteConfirmation');
    }

    public function deleteCategory()
    {
        $category = Category::find($this->category_id);
        if (! $category) {
            return;
        }

        if ($category->children()->count()) {
            $this->dispatch('error', message: '"'.$category->name.'" has sub-categories. Delete or move them first.');
            $this->category_id = null;

            return;
        }

        if ($category->products()->count()) {
            $this->dispatch('error', message: '"'.$category->name.'" has products attached. Move them to another category first.');
            $this->category_id = null;

            return;
        }

        if ($category->image) {
            Storage::disk('public')->delete($category->image);
        }

        $category->delete();
        $this->category_id = null;
        $this->dispatch('success', message: 'Category deleted successfully!');
        $this->loadCategories();
    }

    public function render()
    {
        return view('livewire.admin.category.category-component')
            ->layout('livewire.admin.layouts.app');
    }
}
