<?php

namespace Tests\Feature;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CategoryMenuTest extends TestCase
{
    use RefreshDatabase;

    protected function seedTree(): array
    {
        $fashion = Category::create(['name' => 'Fashion', 'slug' => 'fashion', 'status' => 1]);
        $shoes = Category::create(['name' => 'Shoes', 'slug' => 'shoes', 'status' => 1, 'parent_id' => $fashion->id]);
        $sneakers = Category::create(['name' => 'Sneakers', 'slug' => 'sneakers', 'status' => 1, 'parent_id' => $shoes->id]);
        $plain = Category::create(['name' => 'Plain', 'slug' => 'plain', 'status' => 1]);

        return [$fashion, $shoes, $sneakers, $plain];
    }

    public function test_homepage_shows_submenu_and_sub_submenu(): void
    {
        [$fashion, $shoes, $sneakers, $plain] = $this->seedTree();

        $html = $this->get('/')->assertOk()->getContent();

        // Level 1 + flyout panels with level 2 and level 3
        $this->assertStringContainsString('Fashion', $html);
        $this->assertStringContainsString('hp-fly-panel', $html);
        $this->assertStringContainsString(route('frontend.category.products.view', 'shoes'), $html);
        $this->assertStringContainsString(route('frontend.category.products.view', 'sneakers'), $html);
    }

    public function test_childless_category_has_no_flyout(): void
    {
        $this->seedTree();

        $html = $this->get('/')->assertOk()->getContent();

        // "Plain" row must not carry a flyout panel right after it
        $pos = strpos($html, '>Plain<');
        $this->assertNotFalse($pos);
        $this->assertStringNotContainsString('hp-fly-panel', substr($html, $pos, 400));
    }

    public function test_inactive_children_are_hidden(): void
    {
        [$fashion, $shoes, $sneakers, $plain] = $this->seedTree();
        $shoes->update(['status' => 0]);

        $html = $this->get('/')->assertOk()->getContent();
        $this->assertStringNotContainsString('>Shoes<', $html);
    }

    public function test_category_page_sidebar_renders_nested_tree(): void
    {
        $this->seedTree();

        // CategoryProduct page keeps the filter sidebar with the nested tree
        $html = Livewire::test(\App\Livewire\Frontend\Category\CategoryProduct::class, ['category' => 'fashion'])
            ->html();

        // Parent -> submenu -> sub-submenu nesting intact with links
        $this->assertStringContainsString(route('frontend.category.products.view', 'fashion'), $html);
        $this->assertStringContainsString(route('frontend.category.products.view', 'shoes'), $html);
        $this->assertStringContainsString(route('frontend.category.products.view', 'sneakers'), $html);

        // Scope to the sidebar (grid repeats the same names below it, and
        // Livewire morph markers break naive '>Name<' matching)
        $start = strpos($html, 'categories-category-sidebar');
        $this->assertNotFalse($start);
        $side = substr($html, $start, 12000);

        $pFashion = strpos($side, 'Fashion');
        $pShoes = strpos($side, 'Shoes');
        $pSneakers = strpos($side, 'Sneakers');
        $this->assertNotFalse($pFashion);
        $this->assertNotFalse($pShoes);
        $this->assertNotFalse($pSneakers);
        // hierarchical order: Fashion row, then Shoes nested inside, then Sneakers inside Shoes
        $this->assertTrue($pFashion < $pShoes && $pShoes < $pSneakers);
        // Sneakers sits inside a submenu <ul> that opens after the Shoes link
        $subBefore = strrpos(substr($side, 0, $pSneakers), '<ul class="submenu">');
        $this->assertNotFalse($subBefore);
        $this->assertTrue($subBefore > $pShoes);
    }

    public function test_toggle_script_uses_delegation_not_dead_selectors(): void
    {
        $src = file_get_contents(
            base_path('resources/views/livewire/frontend/layouts/partial/header/header-default.blade.php')
        );
        $this->assertStringContainsString('__catMenuBound', $src);
        $this->assertStringNotContainsString('.has-submenu > a', $src);

        $banner = file_get_contents(
            base_path('resources/views/livewire/frontend/layouts/partial/banner/banner-default.blade.php')
        );
        $this->assertStringNotContainsString('cat-submenu > a', $banner);
    }

    public function test_flyout_panels_are_not_clipped(): void
    {
        $src = file_get_contents(
            base_path('resources/views/livewire/frontend/layouts/partial/category/category-flyout.blade.php')
        );
        // hp-cat-card sets overflow-y:auto — flyout must override or panels get cut off
        $this->assertStringContainsString('.hp-fly-card.hp-cat-card', $src);
        $this->assertStringContainsString('overflow: visible', $src);
    }

    public function test_admin_subcategory_nests_under_chosen_parent(): void
    {        [$fashion] = [$this->seedTree()[0]];
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin = \App\Models\User::factory()->create();
        $admin->assignRole('admin');

        $t = Livewire::actingAs($admin)->test(\App\Livewire\Admin\Category\CategoryComponent::class);
        $t->set('name', 'Boots')->set('slug', 'boots')->set('parent_id', $fashion->id)->call('saveCategory');

        $boots = Category::where('slug', 'boots')->first();
        $this->assertNotNull($boots);
        $this->assertEquals($fashion->id, (int) $boots->parent_id);

        // Tree shows it right under Fashion at depth 1
        $tree = $t->get('tree');
        $names = array_column($tree, 'name');
        $fi = array_search('Fashion', $names);
        $bi = array_search('Boots', $names);
        $this->assertTrue($fi !== false && $bi !== false && $bi > $fi);
        $this->assertSame(1, $tree[$bi]['depth']);
    }

    public function test_admin_tree_lists_children_directly_under_parents(): void
    {        // Reproduces the reported bug: Tshirt (child of Fashion) rendered at
        // the very bottom after all roots instead of under Fashion.
        $fashion = Category::create(['name' => 'Fashion', 'slug' => 'fashion', 'status' => 1]);
        Category::create(['name' => 'Woman', 'slug' => 'woman', 'status' => 1]);
        Category::create(['name' => 'Skinacare', 'slug' => 'skinacare', 'status' => 1]);
        Category::create(['name' => 'MAN', 'slug' => 'man', 'status' => 1, 'parent_id' => $fashion->id]);
        Category::create(['name' => 'Tshirt', 'slug' => 'tshirt', 'status' => 1, 'parent_id' => $fashion->id]);

        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin = \App\Models\User::factory()->create();
        $admin->assignRole('admin');

        $tree = Livewire::actingAs($admin)->test(\App\Livewire\Admin\Category\CategoryComponent::class)->get('tree');
        $names = array_column($tree, 'name');

        // DFS order: Fashion + children first, then remaining roots (alpha)
        $this->assertSame(
            ['Fashion', 'MAN', 'Tshirt', 'Skinacare', 'Woman'],
            $names
        );
        $this->assertSame([0, 1, 1, 0, 0], array_column($tree, 'depth'));
    }

    public function test_megamenu_hover_bridge_and_timers(): void
    {
        $src = file_get_contents(
            base_path('resources/views/livewire/frontend/layouts/partial/category/category-flyout.blade.php')
        );

        // 1. No gap: panel touches the item + invisible ::before bridge
        $this->assertStringContainsString('left: 100%;', $src);
        $this->assertStringNotContainsString('calc(100%', $src);
        $this->assertStringContainsString('.hp-fly-panel::before', $src);

        // 2. Hide delay with clearTimeout on re-entry
        $this->assertStringContainsString('HIDE_MS = 200', $src);
        $this->assertStringContainsString('clearTimeout(item._hpHide)', $src);
        $this->assertStringContainsString("item.matches(':hover')", $src);

        // 4. Hover-intent switch grace
        $this->assertStringContainsString('SWITCH_MS = 150', $src);

        // 5. Touch (tap open, aria-expanded) + keyboard (Esc + arrows)
        $this->assertStringContainsString('aria-expanded', $src);
        $this->assertStringContainsString("'Escape'", $src);
        $this->assertStringContainsString("'ArrowDown'", $src);
        $this->assertStringContainsString("'ArrowRight'", $src);
        $this->assertStringContainsString('hover: none', $src);

        // 6. Look untouched: same row/chevron markup
        $this->assertStringContainsString('hp-cat-item', $src);
        $this->assertStringContainsString('fa-chevron-right', $src);
    }

    public function test_megamenu_with_many_categories_and_scroll(): void
    {        for ($i = 1; $i <= 12; $i++) {
            Category::create(['name' => "Cat $i", 'slug' => "cat-$i", 'status' => 1]);
        }

        $html = $this->get('/')->assertOk()->getContent();

        for ($i = 1; $i <= 12; $i++) {
            $this->assertStringContainsString("Cat $i", $html);
        }
        // Scrollable list container still present (panels live outside it)
        $this->assertStringContainsString('hp-fly-list', $html);
        $this->assertStringContainsString('overflow-y: auto', $html);
    }

    public function test_level3_panel_escapes_scroll_clipping(): void
    {
        $src = file_get_contents(
            base_path('resources/views/livewire/frontend/layouts/partial/category/category-flyout.blade.php')
        );

        // L3 is positioned fixed next to its row (scroll containers clip absolute children)
        $this->assertStringContainsString('.hp-fly-l2 > .hp-fly-panel.fx', $src);
        $this->assertStringContainsString('position: fixed', $src);
        $this->assertStringContainsString('getBoundingClientRect', $src);
        // Stays glued on scroll/resize, keyboard focus positions too
        $this->assertStringContainsString("addEventListener('scroll'", $src);
        $this->assertStringContainsString("addEventListener('focusin'", $src);
    }
}
