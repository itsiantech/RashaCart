<?php

namespace App\Livewire\Admin\Settings;

use App\Models\Category;
use App\Models\Page;
use App\Models\Settings;
use Illuminate\Support\Str;
use Livewire\Component;

class MenuSetting extends Component
{
    public string $tab = 'pages';

    /** @var array<int, array{id: string, type: string, ref_id: int, children: array}> */
    public array $menu = [];

    public function mount(): void
    {
        $raw = setting('header_menu');
        $tree = is_string($raw) && $raw !== '' ? json_decode($raw, true) : [];

        $this->menu = $this->normalize(is_array($tree) ? $tree : []);
    }

    private function normalize(array $items): array
    {
        $rows = [];

        foreach ($items as $item) {
            if (! is_array($item) || empty($item['id'])) {
                continue;
            }

            $type = in_array($item['type'] ?? '', ['page', 'category'], true) ? $item['type'] : 'page';

            $rows[] = [
                'id' => (string) $item['id'],
                'type' => $type,
                'ref_id' => (int) ($item['ref_id'] ?? 0),
                'children' => $this->normalize(is_array($item['children'] ?? null) ? $item['children'] : []),
            ];
        }

        return $rows;
    }

    private function flatten(): array
    {
        $rows = [];
        $walk = function (array $items, int $depth) use (&$walk, &$rows): void {
            foreach ($items as $item) {
                $rows[] = [
                    'id' => $item['id'],
                    'type' => $item['type'],
                    'ref_id' => (int) $item['ref_id'],
                    'depth' => $depth,
                ];
                $walk($item['children'], $depth + 1);
            }
        };
        $walk($this->menu, 0);

        return $rows;
    }

    private function alreadyInMenu(int $refId, string $type): bool
    {
        foreach ($this->flatten() as $row) {
            if ($row['type'] === $type && $row['ref_id'] === $refId) {
                return true;
            }
        }

        return false;
    }

    public function addPage(int $id): void
    {
        $page = Page::query()->where('status', 1)->find($id);

        if (! $page || $this->alreadyInMenu((int) $page->id, 'page')) {
            return;
        }

        $this->menu[] = $this->node('page', (int) $page->id);
        $this->persist();
    }

    public function addCategory(int $id): void
    {
        $category = Category::query()->where('status', 1)->find($id);

        if (! $category || $this->alreadyInMenu((int) $category->id, 'category')) {
            return;
        }

        $this->menu[] = $this->node('category', (int) $category->id);
        $this->persist();
    }

    private function node(string $type, int $refId): array
    {
        return [
            'id' => Str::random(9),
            'type' => $type,
            'ref_id' => $refId,
            'children' => [],
        ];
    }

    public function removeItem(string $id): void
    {
        $this->menu = $this->without($this->menu, $id);
        $this->persist();
    }

    private function without(array $items, string $id): array
    {
        $rows = [];

        foreach ($items as $item) {
            if ((string) $item['id'] === $id) {
                continue;
            }

            $item['children'] = $this->without($item['children'], $id);
            $rows[] = $item;
        }

        return $rows;
    }

    /**
     * Rebuild the tree from the drag-drop payload: one row per item with the
     * id of its parent list ('' = top level). Unknown/orphan ids are kept at
     * the top level so a bad payload can never delete menu entries.
     */
    public function reorderMenu(array $rows): void
    {
        $meta = [];
        $walk = function (array $items) use (&$walk, &$meta): void {
            foreach ($items as $item) {
                $meta[$item['id']] = ['type' => $item['type'], 'ref_id' => $item['ref_id']];
                $walk($item['children']);
            }
        };
        $walk($this->menu);

        $children = [];
        $taken = [];

        foreach ($rows as $row) {
            if (! is_array($row) || ! isset($row['id']) || ! isset($meta[$row['id']])) {
                continue;
            }

            $id = (string) $row['id'];
            if (isset($taken[$id])) {
                continue;
            }

            $taken[$id] = true;
            $children[(string) ($row['parent'] ?? '')][] = $id;
        }

        $seen = [];
        $build = function (string $parent, int $level) use (&$build, $children, $meta, &$seen): array {
            if ($level > 2) {
                return [];
            }

            $out = [];

            foreach ($children[$parent] ?? [] as $id) {
                if (isset($seen[$id])) {
                    continue;
                }

                $seen[$id] = true;
                $out[] = [
                    'id' => $id,
                    'type' => $meta[$id]['type'],
                    'ref_id' => $meta[$id]['ref_id'],
                    'children' => $build($id, $level + 1),
                ];
            }

            return $out;
        };

        $tree = $build('', 0);

        foreach ($meta as $id => $item) {
            if (isset($seen[$id])) {
                continue;
            }

            $seen[$id] = true;
            $tree[] = [
                'id' => $id,
                'type' => $item['type'],
                'ref_id' => $item['ref_id'],
                'children' => [],
            ];
        }

        $this->menu = $tree;
        $this->persist();
    }

    private function persist(): void
    {
        Settings::updateOrCreate(
            ['key' => 'header_menu'],
            ['value' => json_encode(array_values($this->menu))]
        );

        Settings::clearCache();
    }

    private function menuRows(array $items, int $depth, $pages, $categories): array
    {
        $rows = [];

        foreach ($items as $item) {
            $label = '';
            $url = '';
            $missing = false;

            if ($item['type'] === 'page') {
                $page = $pages->get($item['ref_id']);
                $label = $page?->title ?: 'Unknown page';
                $url = $page ? '/page/'.$page->slug : '';
                $missing = ! $page || ! $page->status;
            } else {
                $category = $categories->get($item['ref_id']);
                $label = $category?->name ?: 'Unknown category';
                $url = $category ? '/category/products/'.$category->slug : '';
                $missing = ! $category || ! $category->status;
            }

            $rows[] = [
                'id' => $item['id'],
                'label' => $label,
                'url' => $url,
                'type' => $item['type'],
                'missing' => $missing,
                'depth' => $depth,
                'children' => $this->menuRows($item['children'], $depth + 1, $pages, $categories),
            ];
        }

        return $rows;
    }

    private function categoryRows($categories): array
    {
        $grouped = $categories->groupBy(fn ($category) => (int) ($category->parent_id ?? 0));
        $rows = [];

        $walk = function (int $parentId, int $depth) use (&$walk, $grouped, &$rows): void {
            foreach ($grouped->get($parentId, collect()) as $category) {
                $rows[] = [
                    'id' => (int) $category->id,
                    'name' => $category->name,
                    'depth' => $depth,
                ];
                $walk((int) $category->id, $depth + 1);
            }
        };

        $walk(0, 0);

        return $rows;
    }

    public function render()
    {
        $pages = Page::query()
            ->orderBy('title')
            ->get(['id', 'title', 'slug', 'status']);

        $categories = Category::query()
            ->where('status', 1)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'name', 'parent_id', 'sort_order']);

        $menuRows = $this->menuRows($this->menu, 0, $pages->keyBy('id'), Category::query()->get(['id', 'name', 'slug', 'status'])->keyBy('id'));

        $inMenu = ['page' => [], 'category' => []];
        foreach ($this->flatten() as $row) {
            $inMenu[$row['type']][] = $row['ref_id'];
        }

        return view('livewire.admin.settings.menu-setting', [
            'pages' => $pages,
            'categoryRows' => $this->categoryRows($categories),
            'menuRows' => $menuRows,
            'menuCount' => count($this->flatten()),
            'inMenuPages' => $inMenu['page'],
            'inMenuCategories' => $inMenu['category'],
        ])->layout('livewire.admin.layouts.app');
    }
}
