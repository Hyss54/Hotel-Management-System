<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Customer;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\Booking;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_endpoint()
    {
        $this->seed(); // Seed the database to get an admin user and realistic data
        $admin = User::first();
        $this->actingAs($admin);
        
        // Find a reserved booking
        $booking = Booking::first();
        $booking->update(['status' => 'checked_in']);
        
        $this->withoutExceptionHandling();
        $response = $this->post(route('bookings.check-out', $booking));
        $response->assertStatus(302);
        
        $booking->refresh();
        $this->assertEquals('checked_out', $booking->status);
    }
}
