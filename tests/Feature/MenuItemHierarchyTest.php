<?php

namespace Tests\Feature;

use App\Models\Menu;
use App\Models\MenuItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

/**
 * partials/menu-item.blade.php recurses through children() with no depth
 * limit or cycle guard - a menu item parented to itself, or to one of its
 * own descendants, would infinite-loop that recursion and take down the
 * site's main nav (rendered on every public page) from a single bad
 * admin edit.
 */
class MenuItemHierarchyTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_menu_item_cannot_be_its_own_parent(): void
    {
        $menu = Menu::create(['name' => 'Main', 'slug' => 'main']);
        $item = MenuItem::create(['menu_id' => $menu->id, 'label' => 'Item A']);

        $this->expectException(ValidationException::class);

        $item->update(['parent_id' => $item->id]);
    }

    public function test_a_menu_item_cannot_be_parented_to_its_own_descendant(): void
    {
        $menu = Menu::create(['name' => 'Main', 'slug' => 'main']);
        $grandparent = MenuItem::create(['menu_id' => $menu->id, 'label' => 'Grandparent']);
        $parent = MenuItem::create(['menu_id' => $menu->id, 'label' => 'Parent', 'parent_id' => $grandparent->id]);
        $child = MenuItem::create(['menu_id' => $menu->id, 'label' => 'Child', 'parent_id' => $parent->id]);

        $this->expectException(ValidationException::class);

        // Grandparent -> Child would close the loop: Grandparent -> Parent
        // -> Child -> Grandparent.
        $grandparent->update(['parent_id' => $child->id]);
    }

    public function test_valid_multi_level_nesting_is_still_allowed(): void
    {
        $menu = Menu::create(['name' => 'Main', 'slug' => 'main']);
        $top = MenuItem::create(['menu_id' => $menu->id, 'label' => 'Top']);
        $mid = MenuItem::create(['menu_id' => $menu->id, 'label' => 'Mid', 'parent_id' => $top->id]);
        $leaf = MenuItem::create(['menu_id' => $menu->id, 'label' => 'Leaf', 'parent_id' => $mid->id]);

        $this->assertSame($top->id, $mid->fresh()->parent_id);
        $this->assertSame($mid->id, $leaf->fresh()->parent_id);
    }

    /**
     * The Filament form's parent_id Select filters its options through
     * descendantIds() (plus the record's own id) - this is the exact
     * data descendantIds() must return for that filtering to correctly
     * exclude a grandparent's own descendants from its own parent choices.
     */
    public function test_descendant_ids_returns_every_level_below_an_item(): void
    {
        $menu = Menu::create(['name' => 'Main', 'slug' => 'main']);
        $grandparent = MenuItem::create(['menu_id' => $menu->id, 'label' => 'Grandparent']);
        $parent = MenuItem::create(['menu_id' => $menu->id, 'label' => 'Parent', 'parent_id' => $grandparent->id]);
        $child = MenuItem::create(['menu_id' => $menu->id, 'label' => 'Child', 'parent_id' => $parent->id]);
        $unrelated = MenuItem::create(['menu_id' => $menu->id, 'label' => 'Unrelated']);

        $this->assertEqualsCanonicalizing([$parent->id, $child->id], $grandparent->descendantIds());
        $this->assertNotContains($unrelated->id, $grandparent->descendantIds());
    }
}
