<?php

namespace App\Livewire\Admin\Attribute;

use App\Models\Attribute;
use App\Models\AttributeValue;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;

class AttributeComponent extends Component
{
    public $attributeList;

    public $attribute_id;

    public $name;

    public $slug;

    public $value = '';

    public $showForm = false;

    public $isEdit = false;

    public $search = '';

    public $attribute_value_id;

    public $attribute_value_name;

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
        ]);
        $this->resetValidation();
    }

    public function saveAttribute()
    {
        $validated = $this->validate([
            'name' => 'required|min:3|max:255',
            'slug' => 'required|unique:attributes,slug,'.$this->attribute_id,
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
        $attribute->values()->delete(); // delete values first
        $attribute->delete();

        $this->dispatch('success', ['message' => 'Attribute delete success!']);
        $this->loadAttributes();
    }

    public function resetValueForm()
    {
        $this->reset([
            'attribute_value_id',
            'attribute_value_name',
            'value_attribute_id',
            'isValueEdit',
        ]);
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
        ]);

        if ($this->isValueEdit) {
            AttributeValue::findOrFail($this->attribute_value_id)->update([
                'value' => $this->attribute_value_name,
            ]);
            $msg = 'Value updated';
        } else {
            AttributeValue::create([
                'attribute_id' => $attributeId,
                'value' => $this->attribute_value_name,
            ]);
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
        AttributeValue::findOrFail($valueId)->delete();
        $this->dispatch('success', message: 'Data deleted!');
        $this->loadAttributes();
    }

    public function render()
    {
        return view('livewire.admin.attribute.attribute-component')->layout('livewire.admin.layouts.app');
    }
}
