<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Customer;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\Booking;

class CheckoutTest2 extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_endpoint()
    {
        $admin = User::forceCreate([
            'name' => 'Admin',
            'email' => 'a@b.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);
        $this->actingAs($admin);
        
        $customer = Customer::forceCreate(['name' => 'Test', 'phone' => '123', 'email' => 'c@d.com', 'is_active'=>true]);
        $roomType = RoomType::forceCreate(['name' => 'RT', 'price_per_night' => 100, 'capacity' => 2, 'is_active' => true]);
        $room = Room::forceCreate(['room_number' => '101', 'room_type_id' => $roomType->id, 'status' => 'occupied', 'is_active' => true]);
        
        $booking = Booking::forceCreate([
            'booking_number' => 'BK-123',
            'customer_id' => $customer->id,
            'room_id' => $room->id,
            'status' => 'checked_in',
            'check_in_date' => now()->subDay(),
            'check_out_date' => now()->addDay(),
            'room_charge' => 100,
            'adults' => 1,
            'children' => 0,
        ]);
        
        $this->withoutExceptionHandling();
        $response = $this->post(route('bookings.check-out', $booking));
        $response->assertStatus(302);
        
        $booking->refresh();
        $this->assertEquals('checked_out', $booking->status);
    }
}
