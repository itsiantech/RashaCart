<?php

namespace App\Livewire\Admin\Attribute;

use App\Models\Attribute;
use App\Models\AttributeValue;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;

class AttributeComponent extends Component
{
    use WithFileUploads;

    public $attributeList;

    public $attribute_id;

    public $name;

    public $slug;

    public $type = 'button';

    public $is_filterable = false;

    public $is_active = true;

    public $sort_order = 0;

    public $value = '';

    public $showForm = false;

    public $isEdit = false;

    public $search = '';

    public $attribute_value_id;

    public $attribute_value_name;

    public $value_color_hex = '';

    public $value_swatch_image;

    public $existing_swatch_image = null;

    public $value_sort_order = 0;

    public $value_attribute_id;

    public $isValueEdit = false;

    public $showValueFormForAttributeId = null;

    protected $listeners = ['deleteConfirmed' => 'deleteAttribute'];

    public function mount()
    {
        $this->loadAttributes();
    }

    public function loadAttributes()
    {
        $this->attributeList = Attribute::with('values')
            ->when($this->search, function ($query) {
                return $query->where('name', 'like', '%'.$this->search.'%');
            })
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    public function updatedSearch()
    {
        $this->loadAttributes();
    }

    public function updatedName($value)
    {
        $this->slug = Str::slug($value);
    }

    public function showAddForm()
    {
        // abort_unless(Auth::user()->can('attribute.add'), 403);
        $this->resetForm();
        $this->showForm = true;
        $this->isEdit = false;
    }

    public function showEditForm($id)
    {
        // abort_unless(Auth::user()->can('attribute.edit'), 403);
        $attribute = Attribute::with('values')->findOrFail($id);
        $this->attribute_id = $attribute->id;
        $this->name = $attribute->name;
        $this->slug = $attribute->slug;
        $this->type = $attribute->type ?? 'button';
        $this->is_filterable = (bool) $attribute->is_filterable;
        $this->is_active = (bool) $attribute->is_active;
        $this->sort_order = $attribute->sort_order ?? 0;
        $this->showForm = true;
        $this->isEdit = true;
    }

    public function resetForm()
    {
        $this->reset([
            'attribute_id',
            'name',
            'slug',
            'value',
            'type',
            'is_filterable',
            'is_active',
            'sort_order',
        ]);
        $this->type = 'button';
        $this->is_active = true;
        $this->resetValidation();
    }

    public function saveAttribute()
    {
        $validated = $this->validate([
            'name' => 'required|min:3|max:255',
            'slug' => 'required|unique:attributes,slug,'.$this->attribute_id,
            'type' => 'required|in:color_swatch,image_swatch,button,dropdown',
            'is_filterable' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        if ($this->isEdit) {
            Attribute::findOrFail($this->attribute_id)->update($validated);
            $message = 'Attribute updated successfully!';
        } else {
            Attribute::create($validated);
            $message = 'Attribute created successfully!';
        }

        $this->dispatch('success', ['message' => $message]);

        $this->loadAttributes();
        $this->showForm = false;
        $this->resetForm();
    }

    public function confirmDelete($id)
    {
        // abort_unless(Auth::user()->can('attribute.delete'), 403);
        $this->attribute_id = $id;
        $this->dispatch('showDeleteConfirmation');
    }

    public function deleteAttribute()
    {
        // abort_unless(Auth::user()->can('attribute.delete'), 403);
        $attribute = Attribute::findOrFail($this->attribute_id);

        // Guard: variants use this attribute → refuse, suggest deactivate
        $used = $attribute->usageCount();
        if ($used > 0) {
            $this->dispatch('error', message: "এই attribute {$used}টি variant-এ ব্যবহার হয়েছে। Delete না করে Deactivate করুন।");

            return;
        }

        $attribute->values()->delete(); // delete values first
        $attribute->delete();

        $this->dispatch('success', ['message' => 'Attribute delete success!']);
        $this->loadAttributes();
    }

    public function toggleAttributeActive($id)
    {
        $attribute = Attribute::findOrFail($id);
        $attribute->update(['is_active' => ! $attribute->is_active]);
        $this->loadAttributes();
    }

    public function resetValueForm()
    {
        $this->reset([
            'attribute_value_id',
            'attribute_value_name',
            'value_color_hex',
            'value_swatch_image',
            'existing_swatch_image',
            'value_sort_order',
            'value_attribute_id',
            'isValueEdit',
        ]);
        $this->resetValidation();
    }

    public function toggleValueForm($attributeId)
    {
        if ($this->showValueFormForAttributeId === $attributeId) {
            $this->resetValueForm(); // Hide
            $this->showValueFormForAttributeId = null;
        } else {
            $this->resetValueForm(); // Reset old value form
            $this->showValueFormForAttributeId = $attributeId;
            $this->value_attribute_id = $attributeId;
        }
    }

    public function saveAttributeValue($attributeId)
    {
        $this->validate([
            'attribute_value_name' => 'required|min:1|max:255',
            'value_color_hex' => 'nullable|regex:/^#[0-9A-Fa-f]{6}$/',
            'value_swatch_image' => 'nullable|image|max:2048',
            'value_sort_order' => 'nullable|integer|min:0',
        ]);

        $swatchPath = $this->existing_swatch_image;
        if ($this->value_swatch_image) {
            $swatchPath = $this->value_swatch_image->store('attributes/swatches', 'public');
        }

        $data = [
            'value' => $this->attribute_value_name,
            'color_hex' => $this->value_color_hex ?: null,
            'swatch_image' => $swatchPath,
            'sort_order' => $this->value_sort_order ?? 0,
        ];

        if ($this->isValueEdit) {
            AttributeValue::findOrFail($this->attribute_value_id)->update($data);
            $msg = 'Value updated';
        } else {
            $data['attribute_id'] = $attributeId;
            AttributeValue::create($data);
            $msg = 'Value added';
        }

        $this->dispatch('success', ['message' => $msg]);

        $this->resetValueForm();
        $this->loadAttributes(); // refresh list
    }

    public function editValue($valueId)
    {
        $value = AttributeValue::findOrFail($valueId);
        $this->attribute_value_id = $value->id;
        $this->attribute_value_name = $value->value;
        $this->value_color_hex = $value->color_hex;
        $this->existing_swatch_image = $value->swatch_image;
        $this->value_sort_order = $value->sort_order ?? 0;
        $this->value_attribute_id = $value->attribute_id;
        $this->isValueEdit = true;
    }

    public function confirmDeleteValue($id)
    {
        $this->attribute_value_id = $id;
        $this->dispatch('showDeleteConfirmation');
    }

    public function ShowDeleteValue($valueId)
    {
        $this->dispatch('showDeleteConfirmation', valueId: $valueId);
    }

    public function deleteValue($valueId)
    {
        // abort_unless(Auth::user()->can('attribute.delete'), 403);
        $value = AttributeValue::findOrFail($valueId);

        // Guard: variants use this value → refuse
        $used = $value->usageCount();
        if ($used > 0) {
            $this->dispatch('error', message: "এই value {$used}টি variant-এ ব্যবহার হয়েছে। আগে variant থেকে সরান।");

            return;
        }

        $value->delete();
        $this->dispatch('success', message: 'Data deleted!');
        $this->loadAttributes();
    }

    public function render()
    {
        return view('livewire.admin.attribute.attribute-component')->layout('livewire.admin.layouts.app');
    }
}
