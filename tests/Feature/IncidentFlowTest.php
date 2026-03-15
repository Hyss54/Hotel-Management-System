<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Customer;
use App\Models\Incident;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IncidentFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        // Assuming roles are handled via spatie/laravel-permission as seen in composer.json
        // For simplicity in this test, we'll just authenticate the user.
    }

    public function test_user_can_view_incidents_index()
    {
        $response = $this->actingAs($this->user)->get(route('incidents.index'));
        $response->assertStatus(200);
    }

    public function test_user_can_create_incident()
    {
        $customer = Customer::factory()->create(['is_active' => true]);
        $room = Room::factory()->create(['is_active' => true]);

        $data = [
            'customer_id' => $customer->id,
            'room_id' => $room->id,
            'title' => 'Test Incident',
            'description' => 'Test Description',
            'severity' => 'high',
        ];

        $response = $this->actingAs($this->user)->post(route('incidents.store'), $data);

        $response->assertRedirect(route('incidents.index'));
        $this->assertDatabaseHas('incidents', [
            'title' => 'Test Incident',
            'severity' => 'high',
        ]);
    }

    public function test_user_can_update_incident_status()
    {
        $incident = Incident::factory()->create(['status' => 'open']);

        $response = $this->actingAs($this->user)->patch(route('incidents.updateStatus', $incident), [
            'status' => 'resolved',
        ]);

        $response->assertRedirect();
        $this->assertEquals('resolved', $incident->fresh()->status);
    }

    public function test_user_can_edit_incident()
    {
        $incident = Incident::factory()->create();

        $response = $this->actingAs($this->user)->get(route('incidents.edit', $incident));
        $response->assertStatus(200);

        $response = $this->actingAs($this->user)->put(route('incidents.update', $incident), [
            'customer_id' => $incident->customer_id,
            'title' => 'Updated Title',
            'description' => 'Updated Description',
            'severity' => 'low',
        ]);

        $response->assertRedirect(route('incidents.show', $incident));
        $this->assertEquals('Updated Title', $incident->fresh()->title);
    }
}
