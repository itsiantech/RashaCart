<?php

namespace App\Livewire\Admin\Pages;

use App\Models\Page;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;

class PageForm extends Component
{
    use WithFileUploads;

    public $pageId;
    public $title = '';
    public $slug = '';
    public $description = '';
    public $seo_title = '';
    public $meta_title = '';
    public $status = true;

    public $feature_image;
    public $existing_feature_image;

    public $blocks = [];
    public $blockUploads = [];

    private const COLUMN_TYPES = ['text', 'image'];

    public function mount($id = null)
    {
        if ($id === null) {
            return;
        }

        $page = Page::findOrFail($id);

        $this->pageId = $page->id;
        $this->title = $page->title;
        $this->slug = $page->slug;
        $this->description = $page->description ?? '';
        $this->seo_title = $page->seo_title ?? '';
        $this->meta_title = $page->meta_title ?? '';
        $this->status = (bool) $page->status;
        $this->existing_feature_image = $page->feature_image;
        $this->blocks = $page->blocks ?? [];
    }

    protected function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'slug' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('pages', 'slug')->ignore($this->pageId),
            ],
            'description' => 'nullable|string',
            'seo_title' => 'nullable|string|max:255',
            'meta_title' => 'nullable|string|max:255',
            'feature_image' => 'nullable|image|max:4096',
            'status' => 'boolean',
        ];
    }

    protected function messages()
    {
        return [
            'slug.regex' => 'Slug can only contain lowercase letters, numbers and hyphens.',
        ];
    }

    public function updatedTitle($value)
    {
        if (! $this->pageId) {
            $this->slug = Str::slug($value);
        }
    }

    public function addBlock($type)
    {
        $type = in_array($type, ['text', 'image', 'columns'], true) ? $type : 'text';

        $block = [
            'id' => 'b'.Str::lower(Str::random(8)),
            'type' => $type,
            'content' => '',
            'image' => '',
            'caption' => '',
        ];

        if ($type === 'columns') {
            $block['layout'] = 2;
            $block['columns'] = [$this->blankColumn(), $this->blankColumn()];
        }

        $this->blocks[] = $block;
    }

    public function removeBlock($blockId)
    {
        $index = $this->blockIndex($blockId);
        if ($index === null) {
            return;
        }

        array_splice($this->blocks, $index, 1);
        $this->blocks = array_values($this->blocks);
    }

    public function moveBlock($blockId, $direction)
    {
        $index = $this->blockIndex($blockId);
        if ($index === null) {
            return;
        }

        $target = $index + ($direction < 0 ? -1 : 1);
        if (! isset($this->blocks[$target])) {
            return;
        }

        [$this->blocks[$index], $this->blocks[$target]] = [$this->blocks[$target], $this->blocks[$index]];
    }

    public function reorderBlocks($ids = [])
    {
        $order = array_flip(array_values((array) $ids));

        usort($this->blocks, function ($a, $b) use ($order) {
            return ($order[$a['id']] ?? 0) <=> ($order[$b['id']] ?? 0);
        });
    }

    public function setBlockContent($blockId, $column = null, $html = '')
    {
        $index = $this->blockIndex($blockId);
        if ($index === null) {
            return;
        }

        if ($column !== null && isset($this->blocks[$index]['columns'][(int) $column])) {
            $this->blocks[$index]['columns'][(int) $column]['content'] = $html;
            return;
        }

        $this->blocks[$index]['content'] = $html;
    }

    public function setDescription($html)
    {
        $this->description = $html;
    }

    public function setLayout($blockId, $layout)
    {
        $index = $this->blockIndex($blockId);
        if ($index === null) {
            return;
        }

        $count = (int) $layout === 3 ? 3 : 2;
        $columns = $this->blocks[$index]['columns'] ?? [];

        while (count($columns) < $count) {
            $columns[] = $this->blankColumn();
        }

        $this->blocks[$index]['layout'] = $count;
        $this->blocks[$index]['columns'] = array_slice($columns, 0, $count);
    }

    public function setColumnType($blockId, $column, $type)
    {
        $index = $this->blockIndex($blockId);
        $column = (int) $column;

        if ($index === null || ! isset($this->blocks[$index]['columns'][$column])) {
            return;
        }

        $this->blocks[$index]['columns'][$column]['type'] = in_array($type, self::COLUMN_TYPES, true) ? $type : 'text';
    }

    public function clearBlockImage($blockId, $column = null)
    {
        $index = $this->blockIndex($blockId);
        if ($index === null) {
            return;
        }

        if ($column !== null && isset($this->blocks[$index]['columns'][(int) $column])) {
            $this->blocks[$index]['columns'][(int) $column]['image'] = '';
            return;
        }

        $this->blocks[$index]['image'] = '';
    }

    public function clearFeatureImage()
    {
        $this->feature_image = null;
        $this->existing_feature_image = null;
    }

    public function updatedBlockUploads($value, $key)
    {
        if (! $value) {
            return;
        }

        $validator = Validator::make(['file' => $value], ['file' => 'image|max:4096']);

        if ($validator->fails()) {
            $this->addError('blockUploads', $validator->errors()->first('file'));
            unset($this->blockUploads[$key]);
            return;
        }

        $path = $value->store('pages', 'public');

        [$blockId, $column] = array_pad(explode('_c', $key, 2), 2, null);
        $index = $this->blockIndex($blockId);

        if ($index !== null) {
            if ($column !== null && isset($this->blocks[$index]['columns'][(int) $column])) {
                $this->blocks[$index]['columns'][(int) $column]['image'] = $path;
            } else {
                $this->blocks[$index]['image'] = $path;
            }
        }

        unset($this->blockUploads[$key]);
    }

    public function save()
    {
        $this->validate();

        $data = [
            'title' => $this->title,
            'slug' => Str::slug($this->slug) ?: Str::slug($this->title),
            'description' => $this->description,
            'blocks' => array_values($this->blocks),
            'seo_title' => $this->seo_title ?: null,
            'meta_title' => $this->meta_title ?: null,
            'status' => $this->status,
        ];

        if ($this->feature_image) {
            $data['feature_image'] = $this->feature_image->store('pages', 'public');
        } elseif (! $this->existing_feature_image) {
            $data['feature_image'] = null;
        }

        if ($this->pageId) {
            $page = Page::findOrFail($this->pageId);
            $oldImage = $page->feature_image;
            $page->update($data);

            $newImage = array_key_exists('feature_image', $data) ? $data['feature_image'] : $oldImage;
            if ($oldImage && $oldImage !== $newImage) {
                Storage::disk('public')->delete($oldImage);
            }

            $this->dispatch('success', message: 'Page updated successfully!');
        } else {
            Page::create($data);
            $this->dispatch('success', message: 'Page created successfully!');
        }

        return redirect()->route('admin.pages.index');
    }

    private function blankColumn(): array
    {
        return ['type' => 'text', 'content' => '', 'image' => '', 'caption' => ''];
    }

    private function blockIndex($blockId)
    {
        foreach ($this->blocks as $index => $block) {
            if (($block['id'] ?? null) === $blockId) {
                return $index;
            }
        }

        return null;
    }

    public function render()
    {
        return view('livewire.admin.pages.page-form')->layout('livewire.admin.layouts.app');
    }
}
