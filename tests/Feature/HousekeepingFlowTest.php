<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Customer;
use App\Models\HousekeepingTask;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HousekeepingFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_room_becomes_dirty_and_task_is_created_on_checkout(): void
    {
        // 1. Setup: Room, Guest, Booking (Checked-in)
        $user = User::factory()->create();
        $this->actingAs($user);

        $roomType = RoomType::create([
            'name' => 'Deluxe',
            'price_per_night' => 100,
            'capacity' => 2
        ]);

        $room = Room::create([
            'room_number' => '101',
            'room_type_id' => $roomType->id,
            'status' => 'occupied'
        ]);

        $customer = Customer::create([
            'name' => 'John Doe',
            'type' => 'individual',
            'phone' => '1234567890',
            'is_active' => true,
        ]);

        $booking = Booking::create([
            'booking_number' => 'BK-TEST123',
            'customer_id' => $customer->id,
            'room_id' => $room->id,
            'check_in_date' => now()->subDays(1),
            'check_out_date' => now()->addDays(1),
            'nights' => 2,
            'room_charge' => 200,
            'adults' => 2,
            'status' => 'checked_in',
            'created_by' => $user->id
        ]);

        // 2. Action: Check-out
        $response = $this->post(route('bookings.check-out', $booking));

        // 3. Assertions
        $response->assertStatus(302);

        // Room status should be 'dirty'
        $this->assertEquals('dirty', $room->fresh()->status);

        // Housekeeping task should be created
        $this->assertDatabaseHas('housekeeping_tasks', [
            'room_id' => $room->id,
            'task_type' => 'Check-out Cleaning',
            'status' => 'pending'
        ]);

        $task = HousekeepingTask::where('room_id', $room->id)->first();

        // 4. Action: Start Cleaning
        $this->patch(route('housekeeping.updateStatus', $task), ['status' => 'in_progress']);

        // Assert room status is 'cleaning'
        $this->assertEquals('cleaning', $room->fresh()->status);

        // 5. Action: Complete Cleaning
        $this->patch(route('housekeeping.updateStatus', $task), ['status' => 'completed']);

        // Assert room status is 'available'
        $this->assertEquals('available', $room->fresh()->status);
    }
}
