<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\LaundryItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventorySynchronizationTest extends TestCase
{
    use RefreshDatabase;

    protected $laundry;
    protected $housekeeping;
    protected $user;
    protected $item;

    protected function setUp(): void
    {
        parent::setUp();

        $this->laundry = Department::create(['name' => 'Laundry']);
        $this->housekeeping = Department::create(['name' => 'Housekeeping']);
        $this->user = User::factory()->create(['department_id' => $this->laundry->id]);
        
        $this->item = LaundryItem::create([
            'name' => 'Bed Sheet',
            'stock_quantity' => 100,
            'dirty_quantity' => 0,
            'in_housekeeping_quantity' => 50,
            'unit' => 'pcs',
            'min_stock_level' => 10,
            'is_active' => true
        ]);
    }

    public function test_can_collect_dirty_items()
    {
        $response = $this->actingAs($this->user)
            ->post(route('inventory-sync.collect-dirty'), [
                'laundry_item_id' => $this->item->id,
                'quantity' => 10,
                'notes' => 'Collected from Room 101'
            ]);

        $response->assertRedirect();
        $this->item->refresh();

        $this->assertEquals(40, $this->item->in_housekeeping_quantity);
        $this->assertEquals(10, $this->item->dirty_quantity);
        $this->assertDatabaseHas('inventory_transactions', [
            'laundry_item_id' => $this->item->id,
            'transaction_type' => 'dirty_collection',
            'quantity' => 10
        ]);
    }

    public function test_can_deliver_clean_items()
    {
        $response = $this->actingAs($this->user)
            ->post(route('inventory-sync.deliver-clean'), [
                'laundry_item_id' => $this->item->id,
                'quantity' => 20,
            ]);

        $response->assertRedirect();
        $this->item->refresh();

        $this->assertEquals(80, $this->item->stock_quantity);
        $this->assertEquals(70, $this->item->in_housekeeping_quantity);
        $this->assertDatabaseHas('inventory_transactions', [
            'laundry_item_id' => $this->item->id,
            'transaction_type' => 'clean_delivery',
            'quantity' => 20
        ]);
    }

    public function test_can_mark_items_as_clean()
    {
        $this->item->update(['dirty_quantity' => 30]);

        $response = $this->actingAs($this->user)
            ->post(route('inventory-sync.mark-clean'), [
                'laundry_item_id' => $this->item->id,
                'quantity' => 15,
            ]);

        $response->assertRedirect();
        $this->item->refresh();

        $this->assertEquals(15, $this->item->dirty_quantity);
        $this->assertEquals(115, $this->item->stock_quantity);
        $this->assertDatabaseHas('inventory_transactions', [
            'laundry_item_id' => $this->item->id,
            'transaction_type' => 'wash_complete',
            'quantity' => 15
        ]);
    }
}
